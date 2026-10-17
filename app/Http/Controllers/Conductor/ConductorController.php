<?php

namespace App\Http\Controllers\Conductor;

use App\Http\Controllers\Controller;
use App\Models\AlertaGeocerca;
use App\Models\Justificativo;
use App\Models\Multa;
use App\Models\Notificacion;
use App\Models\PosicionGps;
use App\Models\Punto;
use App\Models\Salida;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConductorController extends Controller
{
    public function puedeConductorRegistrar(): bool
    {
        $ahora = Carbon::now();
        
        $horaLimiteManiana = Carbon::createFromTimeString('06:00');
        $horaLimiteTarde = Carbon::createFromTimeString('17:30');
        
        return $ahora->lt($horaLimiteManiana) || $ahora->gte($horaLimiteTarde);
    }

    public function index()
    {
        $usuario = auth()->user();
        $hoy = Carbon::today();

        $turnoHoy = Turno::with(['unidad', 'punto'])
            ->where('usuario_id', $usuario->usuario_id)
            ->whereDate('fecha', $hoy)
            ->first();

        $ultimaSalida = null;
        $enRuta = false;
        $esPuntero = false;

        if ($turnoHoy) {
            $ultimaSalida = Salida::with(['puntoOrigen', 'llegada.puntoDestino'])
                ->where('turno_id', $turnoHoy->turno_id)
                ->orderByDesc('hora_salida')
                ->first();

            $enRuta = $ultimaSalida && ! $ultimaSalida->llegada;
            $esPuntero = $turnoHoy->posicion <= 2;
        }

        $puedeRegistrar = $this->puedeConductorRegistrar();

        $notificaciones = Notificacion::where('usuario_id', $usuario->usuario_id)
            ->where('leido', 0)
            ->orderByDesc('enviado_at')
            ->limit(5)
            ->get();

        $multasPendientes = Multa::where('usuario_id', $usuario->usuario_id)
            ->where('estado', 'pendiente')
            ->count();

        return view('conductor.index', compact('turnoHoy', 'ultimaSalida', 'enRuta', 'notificaciones', 'usuario', 'puedeRegistrar', 'esPuntero', 'multasPendientes'));
    }

    public function confirmarPresencia(Request $request)
    {
        $usuario = auth()->user();
        $hoy = Carbon::today();

        $turno = Turno::with('punto')
            ->where('usuario_id', $usuario->usuario_id)
            ->whereDate('fecha', $hoy)
            ->first();

        if (!$turno) {
            return response()->json(['error' => 'No tienes turno hoy'], 404);
        }

        $validated = $request->validate([
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'es_puntero' => ['required', 'boolean'],
        ]);

        $punto = $turno->punto;
        if (!$punto || !$punto->latitud || !$punto->longitud) {
            return response()->json(['error' => 'El punto no tiene coordenadas configuradas'], 400);
        }

        $distancia = $this->calcularDistancia(
            $validated['latitud'],
            $validated['longitud'],
            $punto->latitud,
            $punto->longitud
        );

        $radio = $punto->radio_geofence_m ?? 50;

        if ($distancia > $radio) {
            return response()->json([
                'error' => 'No estás en el punto de trabajo. Debes estar en ' . $punto->nombre . ' para confirmar presencia. (Distancia: ' . round($distancia) . 'm - Máx: ' . $radio . 'm)'
            ], 403);
        }

        $turno->update([
            'presencia_confirmada' => true,
            'presencia_at' => now(),
            'presencia_lat' => $validated['latitud'],
            'presencia_lng' => $validated['longitud'],
            'es_puntero_confirmado' => $validated['es_puntero'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Presencia confirmada en ' . $punto->nombre . ' (' . round($distancia) . 'm del punto)'
        ]);
    }

    private function calcularDistancia($lat1, $lng1, $lat2, $lng2): float
    {
        $radioTierra = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $radioTierra * $c;
    }

    public function registrarSalida(Request $request)
    {
        if (! $this->puedeConductorRegistrar()) {
            return response()->json([
                'error' => 'No puedes registrar salidas en este horario. La tarjetera está presente y es quien debe hacerlo.',
                'horario_tarjetera' => '6:00 AM - 5:30 PM',
            ], 403);
        }

        $validated = $request->validate([
            'turno_id' => ['required', 'exists:turnos_diarios,turno_id'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $turno = Turno::with('unidad')->findOrFail($validated['turno_id']);
        $usuario = auth()->user();

        if ($turno->usuario_id !== $usuario->usuario_id) {
            return response()->json(['error' => 'No tienes permiso para registrar esta salida'], 403);
        }

        $salida = Salida::create([
            'turno_id' => $validated['turno_id'],
            'punto_origen_id' => $turno->punto_id,
            'hora_salida' => now(),
            'registrado_by' => $usuario->usuario_id,
            'registro_auto' => false,
            'es_excepcion' => true,
        ]);

        PosicionGps::create([
            'salida_id' => $salida->salida_id,
            'latitud' => $validated['latitud'],
            'longitud' => $validated['longitud'],
            'velocidad' => $request->velocidad ?? 0,
            'timestamp' => now(),
            'es_offline' => false,
        ]);

        $this->verificarGeocerca($salida, $turno, $validated['latitud'], $validated['longitud']);

        return response()->json([
            'success' => true,
            'salida_id' => $salida->salida_id,
            'mensaje' => 'Salida registrada (excepción: tarjetera no disponible)',
        ]);
    }

    public function registrarPosicion(Request $request)
    {
        $validated = $request->validate([
            'salida_id' => ['required', 'exists:salidas,salida_id'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $salida = Salida::findOrFail($validated['salida_id']);
        $turno = Turno::findOrFail($salida->turno_id);
        $usuario = auth()->user();

        if ($turno->usuario_id !== $usuario->usuario_id) {
            return response()->json(['error' => 'No tienes permiso'], 403);
        }

        PosicionGps::create([
            'salida_id' => $validated['salida_id'],
            'latitud' => $validated['latitud'],
            'longitud' => $validated['longitud'],
            'velocidad' => $request->velocidad ?? 0,
            'timestamp' => now(),
            'es_offline' => false,
        ]);

        $this->verificarGeocerca($salida, $turno, $validated['latitud'], $validated['longitud']);

        return response()->json(['success' => true]);
    }

    private function verificarGeocerca(Salida $salida, Turno $turno, $lat, $lng)
    {
        $puntoOrigen = $salida->puntoOrigen;
        $puntoDestino = $turno->punto;
        $cincoMinutosAtras = now()->subMinutes(5);
        $unidad = $turno->unidad;

        $puntos = collect();
        if ($puntoOrigen && $puntoOrigen->latitud && $puntoOrigen->longitud) {
            $puntos->push($puntoOrigen);
        }
        if ($puntoDestino && $puntoDestino->punto_id !== ($puntoOrigen->punto_id ?? null)
            && $puntoDestino->latitud && $puntoDestino->longitud) {
            $puntos->push($puntoDestino);
        }

        foreach ($puntos as $punto) {
            $distancia = $this->calcularDistancia($lat, $lng, $punto->latitud, $punto->longitud);
            $radio = $punto->radio_geofence_m ?? 50;

            if ($distancia > $radio) {
                $tipo = $punto->punto_id === ($puntoOrigen->punto_id ?? null)
                    ? 'fuera_geocerca_origen'
                    : 'fuera_geocerca_destino';

                $existeReciente = AlertaGeocerca::where('salida_id', $salida->salida_id)
                    ->where('tipo', $tipo)
                    ->where('registrado_at', '>=', $cincoMinutosAtras)
                    ->exists();

                if (!$existeReciente) {
                    AlertaGeocerca::create([
                        'salida_id' => $salida->salida_id,
                        'turno_id' => $turno->turno_id,
                        'unidad_id' => $unidad?->unidad_id ?? 0,
                        'tipo' => $tipo,
                        'latitud' => $lat,
                        'longitud' => $lng,
                        'distancia_m' => round($distancia, 2),
                        'punto_id' => $punto->punto_id,
                        'mensaje' => "Unidad {$unidad?->placa} está a " . round($distancia) . "m del punto {$punto->nombre} (máx: {$radio}m)",
                        'registrado_at' => now(),
                    ]);
                }
            }
        }

        if ($puntoOrigen && $puntoDestino
            && $puntoOrigen->punto_id !== $puntoDestino->punto_id
            && $puntoOrigen->latitud && $puntoOrigen->longitud
            && $puntoDestino->latitud && $puntoDestino->longitud) {

            $distanciaRuta = $this->distanciaALinea(
                $lat, $lng,
                $puntoOrigen->latitud, $puntoOrigen->longitud,
                $puntoDestino->latitud, $puntoDestino->longitud
            );

            if ($distanciaRuta > 500) {
                $existeDesvio = AlertaGeocerca::where('salida_id', $salida->salida_id)
                    ->where('tipo', 'desvio_ruta')
                    ->where('registrado_at', '>=', $cincoMinutosAtras)
                    ->exists();

                if (!$existeDesvio) {
                    AlertaGeocerca::create([
                        'salida_id' => $salida->salida_id,
                        'turno_id' => $turno->turno_id,
                        'unidad_id' => $unidad?->unidad_id ?? 0,
                        'tipo' => 'desvio_ruta',
                        'latitud' => $lat,
                        'longitud' => $lng,
                        'distancia_m' => round($distanciaRuta, 2),
                        'mensaje' => "Unidad {$unidad?->placa} se desvió " . round($distanciaRuta) . "m de la ruta {$puntoOrigen->nombre} → {$puntoDestino->nombre}",
                        'registrado_at' => now(),
                    ]);
                }
            }
        }
    }

    private function distanciaALinea($px, $py, $ax, $ay, $bx, $by): float
    {
        $dx = $bx - $ax;
        $dy = $by - $ay;
        $lengthSq = $dx * $dx + $dy * $dy;

        if ($lengthSq == 0) {
            return $this->calcularDistancia($px, $py, $ax, $ay);
        }

        $t = max(0, min(1, (($px - $ax) * $dx + ($py - $ay) * $dy) / $lengthSq));
        $projX = $ax + $t * $dx;
        $projY = $ay + $t * $dy;

        return $this->calcularDistancia($px, $py, $projX, $projY);
    }

    public function registrarLlegada(Request $request)
    {
        $validated = $request->validate([
            'salida_id' => ['required', 'exists:salidas,salida_id'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $salida = Salida::with('llegada')->findOrFail($validated['salida_id']);
        $turno = Turno::findOrFail($salida->turno_id);
        $usuario = auth()->user();

        if ($turno->usuario_id !== $usuario->usuario_id) {
            return response()->json(['error' => 'No tienes permiso'], 403);
        }

        if ($salida->llegada) {
            return response()->json(['error' => 'Esta salida ya tiene llegada registrada'], 400);
        }

        if ($salida->es_excepcion && ! $this->puedeConductorRegistrar()) {
            return response()->json([
                'error' => 'No puedes registrar llegadas en este horario.',
                'horario_tarjetera' => '6:00 AM - 5:30 PM',
            ], 403);
        }

        PosicionGps::create([
            'salida_id' => $salida->salida_id,
            'latitud' => $validated['latitud'],
            'longitud' => $validated['longitud'],
            'velocidad' => $request->velocidad ?? 0,
            'timestamp' => now(),
            'es_offline' => false,
        ]);

        $destinoId = $turno->punto_id === 1 ? 2 : 1;

        $tiempoMinutos = Carbon::parse($salida->hora_salida)->diffInMinutes(now());

        $llegada = $salida->llegada()->create([
            'punto_destino_id' => $destinoId,
            'hora_llegada' => now(),
            'tiempo_trayecto_min' => $tiempoMinutos,
            'es_offline' => false,
        ]);

        $this->verificarGeocerca($salida, $turno, $validated['latitud'], $validated['longitud']);

        return response()->json([
            'success' => true,
            'tiempo' => $tiempoMinutos,
            'mensaje' => "Llegada registrada. Tiempo de trayecto: {$tiempoMinutos} minutos",
        ]);
    }

    public function sincronizarOffline(Request $request)
    {
        $validated = $request->validate([
            'datos' => ['required', 'array'],
            'datos.*.tipo' => ['required', 'in:salida,llegada,posicion'],
            'datos.*.latitud' => ['required', 'numeric'],
            'datos.*.longitud' => ['required', 'numeric'],
            'datos.*.timestamp' => ['required'],
        ]);

        $usuario = auth()->user();
        $procesados = 0;

        foreach ($validated['datos'] as $dato) {
            if ($dato['tipo'] === 'posicion' && isset($dato['salida_id'])) {
                PosicionGps::create([
                    'salida_id' => $dato['salida_id'],
                    'latitud' => $dato['latitud'],
                    'longitud' => $dato['longitud'],
                    'velocidad' => $dato['velocidad'] ?? 0,
                    'timestamp' => $dato['timestamp'],
                    'es_offline' => true,
                ]);
                $procesados++;
            }
        }

        return response()->json([
            'success' => true,
            'procesados' => $procesados
        ]);
    }

    public function historial()
    {
        $usuario = auth()->user();

        $turnos = Turno::with(['unidad', 'punto'])
            ->where('usuario_id', $usuario->usuario_id)
            ->whereDate('fecha', '<=', Carbon::today())
            ->orderByDesc('fecha')
            ->limit(30)
            ->get();

        $multas = Multa::where('usuario_id', $usuario->usuario_id)
            ->orderByDesc('registrado_en')
            ->limit(10)
            ->get();

        return view('conductor.historial', compact('turnos', 'multas'));
    }

    public function multas()
    {
        $usuario = auth()->user();

        $multas = Multa::where('usuario_id', $usuario->usuario_id)
            ->orderByDesc('registrado_en')
            ->paginate(20);

        return view('conductor.multas', compact('multas'));
    }

    public function justificativos()
    {
        $usuario = auth()->user();

        $justificativos = Justificativo::with('turno')
            ->whereHas('turno', function ($query) use ($usuario) {
                $query->where('usuario_id', $usuario->usuario_id);
            })
            ->orderByDesc('registrado_at')
            ->paginate(20);

        return view('conductor.justificativos', compact('justificativos'));
    }

    public function crearJustificativo(Request $request)
    {
        $usuario = auth()->user();
        
        $validated = $request->validate([
            'turno_id' => ['required', 'exists:turnos_diarios,turno_id'],
            'tipo' => ['required', 'in:atraso,inasistencia,sustitucion'],
            'motivo' => ['required', 'string', 'max:500'],
            'sustituto_nombre' => ['nullable', 'string', 'max:100'],
            'sustituto_cedula' => ['nullable', 'string', 'max:20'],
            'sustituto_relacion' => ['nullable', 'string', 'max:50'],
        ]);

        $turno = Turno::findOrFail($validated['turno_id']);

        if ($turno->usuario_id !== $usuario->usuario_id) {
            return response()->json(['error' => 'No tienes permiso'], 403);
        }

        if (!Justificativo::puedeJustificar($turno->turno_id)) {
            return response()->json(['error' => 'No puedes justificar este turno'], 403);
        }

        Justificativo::create([
            'turno_id' => $validated['turno_id'],
            'tipo' => $validated['tipo'],
            'motivo' => $validated['motivo'],
            'sustituto_nombre' => $validated['sustituto_nombre'],
            'sustituto_cedula' => $validated['sustituto_cedula'],
            'sustituto_relacion' => $validated['sustituto_relacion'],
            'registrado_by' => $usuario->usuario_id,
            'registrado_at' => now(),
            'estado' => 'pendiente',
        ]);

        return redirect()->back()->with('success', 'Justificativo enviado exitosamente');
    }

    public function notificaciones()
    {
        $usuario = auth()->user();

        $notificaciones = Notificacion::where('usuario_id', $usuario->usuario_id)
            ->orderByDesc('enviado_at')
            ->paginate(20);

        return view('conductor.notificaciones', compact('notificaciones'));
    }

    public function marcarLeida($id)
    {
        $notificacion = Notificacion::where('notif_id', $id)
            ->where('usuario_id', auth()->user()->usuario_id)
            ->firstOrFail();

        $notificacion->marcarLeido();

        return redirect()->back();
    }

    public function offline()
    {
        return view('conductor.offline');
    }
}
