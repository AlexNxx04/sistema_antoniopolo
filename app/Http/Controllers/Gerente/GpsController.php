<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\AlertaGeocerca;
use App\Models\HistorialCambio;
use App\Models\LlegadaGps;
use App\Models\Multa;
use App\Models\Punto;
use App\Models\Salida;
use App\Models\Turno;
use App\Models\Unidad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GpsController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->filled('fecha') ? Carbon::parse($request->fecha) : Carbon::today();
        $puntoId = $request->filled('punto_id') ? $request->punto_id : null;
        $unidadId = $request->filled('unidad_id') ? $request->unidad_id : null;

        $query = Salida::with(['turno.usuario', 'turno.unidad', 'puntoOrigen', 'llegada.puntoDestino', 'posiciones'])
            ->whereDate('hora_salida', $fecha);

        if ($puntoId) {
            $query->whereHas('turno', function ($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            });
        }

        if ($unidadId) {
            $query->whereHas('turno', function ($q) use ($unidadId) {
                $q->where('unidad_id', $unidadId);
            });
        }

        $salidas = $query->orderBy('hora_salida')->get();

        $unidades = Unidad::where('estado', 'activo')->orderBy('numero_interno')->get();
        $unidadesActivas = $this->getUnidadesActivas($fecha, $puntoId);
        $puntos = Punto::where('activo', 1)->orderBy('nombre')->get();

        $stats = $this->calcularStats($salidas, $fecha, $puntoId);

        return view('gerente.gps.index', compact('salidas', 'unidades', 'unidadesActivas', 'puntos', 'stats', 'fecha', 'puntoId', 'unidadId'));
    }

    private function calcularStats($salidas, $fecha, $puntoId = null)
    {
        $query = Turno::whereDate('fecha', $fecha)
            ->where('es_descanso', 0);
        
        if ($puntoId) {
            $query->where('punto_id', $puntoId);
        }
        
        $turnosDia = $query->count();
        $total = $salidas->count();
        $completadas = $salidas->filter(function ($salida) {
            return $salida->llegada !== null;
        })->count();
        $enRuta = $total - $completadas;
        $enEspera = $turnosDia - $total;

        return [
            'en_ruta' => $enRuta,
            'completadas' => $completadas,
            'en_espera' => max(0, $enEspera),
            'total_hoy' => $turnosDia,
        ];
    }

    public function mapa(Request $request)
    {
        $fecha = $request->filled('fecha') ? Carbon::parse($request->fecha) : Carbon::today();
        $unidadId = $request->filled('unidad_id') ? $request->unidad_id : null;

        $query = Salida::with([
            'turno.usuario',
            'turno.unidad',
            'puntoOrigen',
            'llegada.puntoDestino',
            'posiciones',
        ])->whereDate('hora_salida', $fecha);

        if ($unidadId) {
            $query->whereHas('turno', function ($q) use ($unidadId) {
                $q->where('unidad_id', $unidadId);
            });
        }

        $salidas = $query->orderBy('hora_salida')->get();
        $unidades = Unidad::where('estado', 'activo')->orderBy('numero_interno')->get();

        $posiciones = [];
        foreach ($salidas as $salida) {
            if ($salida->posiciones->isNotEmpty()) {
                $posiciones[$salida->salida_id] = [
                    'salida' => $salida,
                    'puntos' => $salida->posiciones->map(function ($p) {
                        return [
                            'lat' => $p->latitud,
                            'lng' => $p->longitud,
                            'velocidad' => $p->velocidad,
                            'tiempo' => $p->timestamp->format('H:i:s'),
                        ];
                    })->toArray(),
                ];
            }
        }

        return view('gerente.gps.mapa', compact('salidas', 'unidades', 'posiciones', 'fecha', 'unidadId'));
    }

    public function detalleSalida($id)
    {
        $salida = Salida::with([
            'turno.usuario',
            'turno.unidad',
            'puntoOrigen',
            'llegada.puntoDestino',
            'posiciones',
            'registrador',
        ])->findOrFail($id);

        return view('gerente.gps.detalle', compact('salida'));
    }

    public function posicionesEnVivo()
    {
        $salidasActivas = Salida::with([
                'turno.usuario',
                'turno.unidad',
                'turno.punto',
                'puntoOrigen',
                'llegada',
            ])
            ->whereNull('llegada_id')
            ->whereDate('hora_salida', today())
            ->get();

        $unidades = [];
        foreach ($salidasActivas as $salida) {
            $ultimaPos = $salida->posiciones()
                ->orderByDesc('timestamp')
                ->first();

            $unidades[] = [
                'salida_id' => $salida->salida_id,
                'unidad_placa' => $salida->turno->unidad->placa ?? 'N/A',
                'unidad_interno' => $salida->turno->unidad->numero_interno ?? 'N/A',
                'conductor' => $salida->turno->usuario->nombres ?? 'N/A',
                'turno_posicion' => $salida->turno->posicion,
                'punto_origen' => $salida->puntoOrigen->nombre ?? 'N/A',
                'hora_salida' => $salida->hora_salida->format('H:i'),
                'lat' => $ultimaPos->latitud ?? null,
                'lng' => $ultimaPos->longitud ?? null,
                'velocidad' => $ultimaPos->velocidad ?? 0,
                'ultima_actualizacion' => $ultimaPos->timestamp->format('H:i:s') ?? null,
                'en_ruta' => $salida->llegada === null,
            ];
        }

        $puntos = Punto::where('activo', 1)
            ->select('punto_id', 'nombre', 'latitud', 'longitud', 'radio_geofence_m')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->punto_id,
                    'nombre' => $p->nombre,
                    'lat' => $p->latitud,
                    'lng' => $p->longitud,
                    'radio' => $p->radio_geofence_m ?? 50,
                ];
            });

        return response()->json([
            'unidades' => $unidades,
            'puntos' => $puntos,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function alertas()
    {
        $alertas = AlertaGeocerca::with(['salida.turno.unidad', 'punto', 'unidad'])
            ->orderByDesc('registrado_at')
            ->limit(50)
            ->get()
            ->map(function ($a) {
                return [
                    'alerta_id' => $a->alerta_id,
                    'tipo' => $a->tipo,
                    'mensaje' => $a->mensaje,
                    'unidad_placa' => $a->unidad->placa ?? $a->salida?->turno?->unidad?->placa ?? 'N/A',
                    'punto_nombre' => $a->punto->nombre ?? 'N/A',
                    'distancia_m' => $a->distancia_m,
                    'lat' => $a->latitud,
                    'lng' => $a->longitud,
                    'leido' => $a->leido,
                    'registrado_at' => $a->registrado_at->format('H:i:s'),
                    'fecha' => $a->registrado_at->format('d/m/Y'),
                ];
            });

        $noLeidas = AlertaGeocerca::where('leido', false)->count();

        return response()->json([
            'alertas' => $alertas,
            'no_leidas' => $noLeidas,
        ]);
    }

    public function marcarAlertasLeidas()
    {
        AlertaGeocerca::marcarLeidas();
        return response()->json(['success' => true]);
    }

    public function registrarSalida(Request $request)
    {
        $validated = $request->validate([
            'turno_id' => ['required', 'exists:turnos_diarios,turno_id'],
            'punto_origen_id' => ['required', 'exists:puntos,punto_id'],
        ]);

        $turno = Turno::with('usuario', 'unidad')->findOrFail($validated['turno_id']);

        $salida = Salida::create([
            'turno_id' => $validated['turno_id'],
            'punto_origen_id' => $validated['punto_origen_id'],
            'hora_salida' => now(),
            'registrado_by' => Auth::user()->usuario_id,
            'registro_auto' => 0,
            'es_excepcion' => 0,
        ]);

        return redirect()->back()->with('success', 'Salida registrada para unidad '.$turno->unidad->placa);
    }

    public function registrarLlegada(Request $request, $id)
    {
        $salida = Salida::with(['llegada', 'turno.usuario', 'turno.unidad'])->findOrFail($id);

        if ($salida->llegada) {
            return redirect()->back()->withErrors(['error' => 'Esta salida ya tiene una llegada registrada.']);
        }

        $validated = $request->validate([
            'punto_destino_id' => ['required', 'exists:puntos,punto_id'],
        ]);

        $tiempoMinutos = Carbon::parse($salida->hora_salida)->diffInMinutes(now());
        $puntoOrigenId = $salida->punto_origen_id;
        $puntoDestinoId = $validated['punto_destino_id'];

        $tiempoEstimado = (int) ParametroController::getTiempoEstimadoRuta();
        $minutosRetraso = max(0, $tiempoMinutos - $tiempoEstimado);
        $tieneRetraso = $minutosRetraso > 0;

        DB::beginTransaction();
        try {
            $llegada = LlegadaGps::create([
                'salida_id' => $salida->salida_id,
                'punto_destino_id' => $validated['punto_destino_id'],
                'hora_llegada' => now(),
                'tiempo_trayecto_min' => $tiempoMinutos,
                'es_offline' => 0,
            ]);

            $turno = $salida->turno;
            $mensajeMulta = '';

            if ($tieneRetraso && $turno) {
                $valorPorMinuto = ParametroController::getValorMultaPorMinuto();
                $montoMulta = $minutosRetraso * $valorPorMinuto;

                $multa = Multa::crearPorRetraso(
                    $turno->usuario_id,
                    $turno->turno_id,
                    $llegada->llegada_id,
                    $minutosRetraso,
                    $valorPorMinuto
                );

                $mensajeMulta = " | MULTA: {$minutosRetraso} min x \${$valorPorMinuto} = \${$montoMulta}";

                HistorialCambio::registrar(
                    'multas',
                    $multa->multa_id,
                    'crear',
                    Auth::user()->usuario_id,
                    'multa_retraso',
                    null,
                    "Retraso de {$minutosRetraso} minutos. Monto: \${$montoMulta}",
                    $request->ip()
                );
            }

            DB::commit();

            $mensaje = "Llegada registrada. Tiempo: {$tiempoMinutos} min (estimado: {$tiempoEstimado} min).";
            if ($tieneRetraso) {
                $mensaje .= " RETRASO: {$minutosRetraso} minutos.".$mensajeMulta;
            }

            return redirect()->back()->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Error: '.$e->getMessage()]);
        }
    }

    private function getUnidadesActivas($fecha, $puntoId = null)
    {
        $query = Turno::with(['unidad', 'usuario', 'punto'])
            ->whereDate('fecha', $fecha)
            ->where('es_descanso', 0)
            ->where('estado', '!=', 'no_presento');
        
        if ($puntoId) {
            $query->where('punto_id', $puntoId);
        }
        
        $turnos = $query->get();

        $salidasHoy = Salida::whereDate('hora_salida', $fecha)
            ->whereHas('turno', function ($q) use ($puntoId) {
                $q->where('es_descanso', 0);
                if ($puntoId) {
                    $q->where('punto_id', $puntoId);
                }
            })
            ->pluck('turno_id')
            ->toArray();

        $unidadesOperando = [];
        foreach ($turnos as $turno) {
            $enRuta = in_array($turno->turno_id, $salidasHoy);

            $unidadesOperando[] = [
                'turno' => $turno,
                'en_ruta' => $enRuta,
                'conductor' => $turno->usuario ? ($turno->usuario->nombres ?? 'Sin asignar') : 'Sin asignar',
                'placa' => $turno->unidad ? ($turno->unidad->placa ?? 'N/A') : 'N/A',
                'numero_interno' => $turno->unidad ? ($turno->unidad->numero_interno ?? 'N/A') : 'N/A',
                'punto_nombre' => $turno->punto ? ($turno->punto->nombre ?? 'N/A') : 'N/A',
            ];
        }

        return $unidadesOperando;
    }
}
