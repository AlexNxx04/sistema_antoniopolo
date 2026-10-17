<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Punto;
use App\Models\Salida;
use App\Models\Turno;
use App\Models\AlertaGeocerca;
use App\Models\CarreraEspecial;
use App\Models\HistorialCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GPSController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $turnosConfirmados = Turno::with(['usuario', 'unidad'])
            ->where('punto_id', $puntoId)
            ->whereDate('fecha', now()->toDateString())
            ->where('estado', 'confirmado')
            ->orderBy('posicion')
            ->get();
        
        $unidades = \App\Models\Unidad::where('punto_id', $puntoId)
            ->where('estado', 'activo')
            ->orderBy('numero_interno')
            ->get();
        
        $carrerasActivas = DB::table('carreras_especiales as ce')
            ->join('turnos_diarios as t', 'ce.turno_id', '=', 't.turno_id')
            ->leftJoin('usuarios as u', 't.usuario_id', '=', 'u.usuario_id')
            ->leftJoin('unidades as un', 't.unidad_id', '=', 'un.unidad_id')
            ->where('t.punto_id', $puntoId)
            ->whereNull('ce.hora_regreso')
            ->whereDate('ce.hora_salida_especial', now()->toDateString())
            ->select('ce.*', 'u.nombres', 'u.apellidos', 't.posicion', 'un.placa', 'un.numero_interno')
            ->get();
        
        $carrerasHoy = DB::table('carreras_especiales as ce')
            ->join('turnos_diarios as t', 'ce.turno_id', '=', 't.turno_id')
            ->leftJoin('usuarios as u', 't.usuario_id', '=', 'u.usuario_id')
            ->leftJoin('unidades as un', 't.unidad_id', '=', 'un.unidad_id')
            ->where('t.punto_id', $puntoId)
            ->whereDate('ce.hora_salida_especial', now()->toDateString())
            ->select('ce.*', 'u.nombres', 'u.apellidos', 't.posicion', 'un.placa', 'un.numero_interno')
            ->orderByDesc('ce.hora_salida_especial')
            ->get();
        
        $salidasAnticipadas = $carrerasHoy->filter(function($c) {
            return in_array($c->tipo_salida, ['anticipada', 'llena']);
        });
        
        $salidasNormales = $carrerasHoy->filter(function($c) {
            return $c->tipo_salida === 'normal';
        });
        
        $puntoNombre = $puntoId == 1 ? 'Guaranda' : 'Salinas';

        return view('tarjetera.gps.index', compact('turnosConfirmados', 'unidades', 'carrerasActivas', 'carrerasHoy', 'puntoNombre', 'salidasAnticipadas', 'salidasNormales', 'puntoId'));
    }
    
    public function registrarSalida(Request $request)
    {
        $user = Auth::user();
        $turnoId = $request->input('turno_id');
        $tipo = $request->input('tipo', 'normal');

        $turno = Turno::findOrFail($turnoId);

        $carreraId = DB::table('carreras_especiales')->insertGetId([
            'turno_id' => $turnoId,
            'hora_salida_especial' => now(),
            'tipo_salida' => $tipo,
            'posicion_antes' => $turno->posicion,
            'registrado_by' => $user->usuario_id,
        ]);

        $tiposSalida = [
            'normal' => 'Normal',
            'anticipada' => 'Anticipada',
            'llena' => 'Unidad Llena',
            'carrera' => 'Carrera Especial',
        ];

        HistorialCambio::registrar(
            'carreras_especiales',
            $carreraId,
            'crear',
            $user->usuario_id,
            'salida_gps',
            null,
            "Tipo: {$tiposSalida[$tipo]} | Turno #{$turno->posicion}",
            $request->ip(),
            $user->punto_id,
            "Salida {$tiposSalida[$tipo]} registrada - Conductor: " . ($turno->usuario->nombres ?? 'N/A')
        );

        $mensaje = match($tipo) {
            'anticipada' => 'Salida anticipada registrada',
            'llena' => 'Salida por unidad llena',
            'carrera' => 'Carrera especial registrada',
            default => 'Salida normal registrada'
        };

        return redirect()->back()->with('success', $mensaje . ': ' . ($turno->usuario->nombres ?? 'N/A'));
    }

    public function finalizarCarrera($id)
    {
        $user = Auth::user();

        $carrera = DB::table('carreras_especiales')->where('carrera_id', $id)->first();

        DB::table('carreras_especiales')
            ->where('carrera_id', $id)
            ->update([
                'hora_regreso' => now(),
            ]);

        HistorialCambio::registrar(
            'carreras_especiales',
            $id,
            'modificar',
            $user->usuario_id,
            'hora_regreso',
            $carrera->hora_salida_especial ?? '-',
            now()->toDateTimeString(),
            request()->ip(),
            $user->punto_id,
            "Llegada registrada - Carrera #$id finalizada"
        );

        return redirect()->back()->with('success', 'Carrera finalizada');
    }

    public function mapaEnVivo()
    {
        $user = Auth::user();
        $puntoNombre = $user->punto->nombre ?? 'Punto';

        return view('tarjetera.gps.mapa', compact('puntoNombre'));
    }

    public function posicionesEnVivo()
    {
        $puntoId = Auth::user()->punto_id;

        $salidasActivas = Salida::with([
                'turno.usuario',
                'turno.unidad',
                'turno.punto',
                'puntoOrigen',
                'llegada',
            ])
            ->whereNull('llegada_id')
            ->whereDate('hora_salida', today())
            ->whereHas('turno', function ($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
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
            ->where('punto_id', $puntoId)
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

    public function getUnidades()
    {
        $puntoId = Auth::user()->punto_id;
        
        $unidades = DB::table('unidades')
            ->where('punto_id', $puntoId)
            ->where('estado', 'activo')
            ->select('unidad_id', 'numero_interno', 'placa', 'latitud', 'longitud')
            ->get();
        
        return response()->json($unidades);
    }

    public function alertas()
    {
        $puntoId = Auth::user()->punto_id;

        $alertas = AlertaGeocerca::with(['salida.turno.unidad', 'punto', 'unidad'])
            ->whereHas('turno', function ($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
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

        $noLeidas = AlertaGeocerca::where('leido', false)
            ->whereHas('turno', function ($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
            ->count();

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
    
    public function getPosiciones()
    {
        $puntoId = Auth::user()->punto_id;
        
        $turnos = Turno::with(['usuario', 'unidad'])
            ->where('punto_id', $puntoId)
            ->whereDate('fecha', now()->toDateString())
            ->where('estado', 'confirmado')
            ->orderBy('posicion')
            ->get();
        
        return response()->json($turnos);
    }
}