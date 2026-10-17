<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\CicloAnual;
use App\Models\HistorialCambio;
use App\Models\Punto;
use App\Models\Turno;
use App\Models\TurnoConfig;
use App\Models\Unidad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TurnoDiarioController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->filled('fecha') ? Carbon::parse($request->fecha) : Carbon::today();
        $puntoId = $request->filled('punto_id') ? $request->punto_id : null;

        $query = Turno::with(['usuario', 'unidad', 'punto', 'bloqueador'])
            ->whereDate('fecha', $fecha);

        if ($puntoId) {
            $query->where('punto_id', $puntoId);
        }

        $turnos = $query->orderBy('punto_id')->orderBy('posicion')->get();
        $puntos = Punto::where('activo', 1)->get();
        $cicloActivo = CicloAnual::getCicloActivo();

        $estadisticas = [
            'total' => $turnos->count(),
            'operando' => $turnos->where('bloqueado', 0)->where('es_descanso', 0)->count(),
            'bloqueados' => $turnos->where('bloqueado', 1)->where('es_descanso', 0)->count(),
            'descansando' => $turnos->where('es_descanso', 1)->count(),
            'punteros' => $turnos->where('es_puntero', 1)->count(),
        ];

        $cicloId = $cicloActivo ? $cicloActivo->ciclo_id : null;
        $turnoConfig = $cicloId ? TurnoConfig::where('ciclo_id', $cicloId)->where('punto_id', $puntoId)->first() : null;

        return view('gerente.turnos.index', compact(
            'turnos', 'puntos', 'fecha', 'puntoId', 'cicloActivo',
            'estadisticas', 'turnoConfig'
        ));
    }

    public function mostrarFormularioGenerar(Request $request)
    {
        $fecha = $request->filled('fecha') ? Carbon::parse($request->fecha) : Carbon::today();
        $puntoId = $request->filled('punto_id') ? $request->punto_id : null;

        $puntos = Punto::where('activo', 1)->get();
        $cicloActivo = CicloAnual::getCicloActivo();

        if (! $cicloActivo) {
            return redirect()->route('gerente.turnos.index')
                ->withErrors(['error' => 'No hay un ciclo anual activo.']);
        }

        $conductoresDisponibles = collect();
        $turnoConfig = null;

        if ($puntoId) {
            $conductoresDisponibles = User::where('rol_id', 3)
                ->where('activo', 1)
                ->where('punto_id', $puntoId)
                ->orderBy('nombres')
                ->get();

            $turnoConfig = TurnoConfig::where('ciclo_id', $cicloActivo->ciclo_id)
                ->where('punto_id', $puntoId)
                ->first();
        }

        $turnos = Turno::with(['usuario', 'unidad', 'punto'])
            ->whereDate('fecha', $fecha)
            ->where('punto_id', $puntoId)
            ->get();

        $yaGenerado = $turnoConfig !== null;
        $turnosDelDia = Turno::whereDate('fecha', $fecha)
            ->where('punto_id', $puntoId)
            ->exists();

        return view('gerente.turnos.generar', compact(
            'fecha', 'puntoId', 'puntos', 'conductoresDisponibles',
            'turnos', 'yaGenerado', 'turnosDelDia', 'turnoConfig', 'cicloActivo'
        ));
    }

    public function generar(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'punto_id' => 'required|exists:puntos,punto_id',
            'dias_trabajo' => 'required|integer|min:1|max:7',
        ]);

        $fecha = Carbon::parse($validated['fecha']);
        $puntoId = $validated['punto_id'];
        $diasTrabajo = $validated['dias_trabajo'];

        $ciclo = CicloAnual::getCicloActivo();
        if (! $ciclo) {
            return redirect()->back()->withErrors(['error' => 'No hay un ciclo anual activo.']);
        }

        DB::beginTransaction();
        try {
            $configExistente = TurnoConfig::where('ciclo_id', $ciclo->ciclo_id)
                ->where('punto_id', $puntoId)
                ->first();

            if ($configExistente) {
                DB::rollBack();

                return redirect()->back()->withErrors([
                    'error' => 'Ya existe una configuración para este punto en el ciclo actual. '
                    .'Los turnos se generan automáticamente cada día.',
                ]);
            }

            $conductores = User::where('rol_id', 3)
                ->where('activo', 1)
                ->where('punto_id', $puntoId)
                ->get();

            if ($conductores->isEmpty()) {
                DB::rollBack();

                return redirect()->back()->withErrors(['error' => 'No hay conductores activos en este punto.']);
            }

            $totalConductores = $conductores->count();
            $diasDescanso = 7 - $diasTrabajo;

            $grupos = $this->crearGruposAleatorios($conductores, $diasDescanso);

            $gerente = Auth::user();

            $configData = [
                'ciclo_id' => $ciclo->ciclo_id,
                'punto_id' => $puntoId,
                'config_json' => json_encode([
                    'grupos' => $grupos,
                    'dias_trabajo' => $diasTrabajo,
                    'dias_descanso' => $diasDescanso,
                    'total_conductores' => $totalConductores,
                ]),
                'creado_por' => $gerente->usuario_id,
            ];

            TurnoConfig::create($configData);

            $this->crearTurnosDelDia($fecha, $puntoId, $ciclo->ciclo_id, $grupos);

            HistorialCambio::registrar(
                'turnos_config',
                0,
                'crear',
                $gerente->usuario_id,
                'generacion_inicial',
                null,
                "Punto: {$puntoId}, {$totalConductores} conductores",
                $request->ip()
            );

            DB::commit();

            return redirect()->route('gerente.turnos.index', [
                'fecha' => $fecha->format('Y-m-d'),
                'punto_id' => $puntoId,
            ])->with('success', "Configuración guardada. {$totalConductores} conductores en {$diasDescanso} grupos de descanso.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Error: '.$e->getMessage()]);
        }
    }

    private function crearGruposAleatorios($conductores, $numGrupos)
    {
        $conductoresArray = $conductores->toArray();
        shuffle($conductoresArray);

        $grupos = [];
        $diasSemana = [1, 2, 3, 4, 5, 6, 0];
        $diasAsignados = array_slice($diasSemana, 0, $numGrupos);

        foreach ($diasAsignados as $index => $dia) {
            $grupos[] = [
                'dia_descanso' => $dia,
                'conductores' => [],
            ];
        }

        foreach ($conductoresArray as $conductor) {
            $grupoIndex = count($grupos) > 0 ? array_rand($grupos) : 0;
            $grupos[$grupoIndex]['conductores'][] = $conductor['usuario_id'];
        }

        return $grupos;
    }

    public function generarTurnosDelDia(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'punto_id' => 'required|exists:puntos,punto_id',
        ]);

        $fecha = Carbon::parse($validated['fecha']);
        $puntoId = $validated['punto_id'];

        $ciclo = CicloAnual::getCicloActivo();
        if (! $ciclo) {
            return redirect()->back()->withErrors(['error' => 'No hay un ciclo anual activo.']);
        }

        $turnoConfig = TurnoConfig::where('ciclo_id', $ciclo->ciclo_id)
            ->where('punto_id', $puntoId)
            ->first();

        if (! $turnoConfig) {
            return redirect()->back()->withErrors([
                'error' => 'No existe configuración para este punto. Genere los turnos iniciales primero.',
            ]);
        }

        $turnosExistentes = Turno::whereDate('fecha', $fecha)
            ->where('punto_id', $puntoId)
            ->where('es_descanso', 0)
            ->exists();

        if ($turnosExistentes) {
            return redirect()->back()->withErrors([
                'error' => 'Los turnos para este día ya fueron generados.',
            ]);
        }

        $config = json_decode($turnoConfig->config_json, true);
        $grupos = $config['grupos'];

        $this->crearTurnosDelDia($fecha, $puntoId, $ciclo->ciclo_id, $grupos);

        return redirect()->route('gerente.turnos.index', [
            'fecha' => $fecha->format('Y-m-d'),
            'punto_id' => $puntoId,
        ])->with('success', 'Turnos del día generados automáticamente.');
    }

    private function crearTurnosDelDia($fecha, $puntoId, $cicloId, $grupos)
    {
        $diaSemana = $fecha->dayOfWeek;

        Turno::whereDate('fecha', $fecha)
            ->where('punto_id', $puntoId)
            ->delete();

        $unidadesDisponibles = Unidad::where('estado', 'activo')
            ->where('punto_id', $puntoId)
            ->orderBy('numero_interno')
            ->get();

        $conductoresOperan = [];
        $conductoresDescansan = [];

        foreach ($grupos as $grupo) {
            if ($grupo['dia_descanso'] == $diaSemana) {
                $conductoresDescansan = array_merge($conductoresDescansan, $grupo['conductores']);
            } else {
                $conductoresOperan = array_merge($conductoresOperan, $grupo['conductores']);
            }
        }

        if (empty($conductoresOperan)) {
            $conductoresOperan = collect($grupos)
                ->filter(fn ($g) => $g['dia_descanso'] != $diaSemana)
                ->flatMap(fn ($g) => $g['conductores'])
                ->toArray();
        }

        $posicion = 1;
        $punterosCreados = 0;
        $conductoresYaUsados = [];

        shuffle($conductoresOperan);
        $conductoresOperan = array_values($conductoresOperan);

        foreach ($conductoresOperan as $conductorId) {
            if (in_array($conductorId, $conductoresYaUsados)) {
                continue;
            }

            $conductor = User::find($conductorId);
            if (! $conductor) {
                continue;
            }

            $unidad = $unidadesDisponibles->first();
            if (! $unidad) {
                break;
            }
            $unidadesDisponibles = $unidadesDisponibles->skip(1)->values();

            $esPuntero = ($punterosCreados < 2);

            Turno::create([
                'ciclo_id' => $cicloId,
                'fecha' => $fecha,
                'punto_id' => $puntoId,
                'usuario_id' => $conductorId,
                'unidad_id' => $unidad->unidad_id,
                'posicion' => $posicion,
                'es_puntero' => $esPuntero ? 1 : 0,
                'es_descanso' => 0,
                'generado_by' => Auth::user()->usuario_id,
                'generado_at' => now(),
                'bloqueado' => 0,
            ]);

            $conductoresYaUsados[] = $conductorId;
            $punterosCreados++;
            $posicion++;
        }

        $posicionDescanso = 1;
        foreach ($conductoresDescansan as $conductorId) {
            Turno::create([
                'ciclo_id' => $cicloId,
                'fecha' => $fecha,
                'punto_id' => $puntoId,
                'usuario_id' => $conductorId,
                'unidad_id' => null,
                'posicion' => $posicionDescanso,
                'es_puntero' => 0,
                'es_descanso' => 1,
                'generado_by' => Auth::user()->usuario_id,
                'generado_at' => now(),
                'bloqueado' => 0,
            ]);
            $posicionDescanso++;
        }
    }

    public function justificarPuntero(Request $request, $turnoId)
    {
        $turno = Turno::findOrFail($turnoId);

        if (! $turno->es_puntero) {
            return redirect()->back()->withErrors(['error' => 'Este turno no es de puntero.']);
        }

        $validated = $request->validate([
            'justificacion' => 'required|string|max:500',
            'tiene_justificacion' => 'required|boolean',
        ]);

        $turno->justificacion_retraso = $validated['justificacion'];

        if (! $validated['tiene_justificacion']) {
            $turno->hora_limite = '09:00:00';
        } else {
            $turno->hora_limite = null;
        }

        $turno->save();

        $gerente = Auth::user();

        HistorialCambio::registrar(
            'turnos_diarios',
            $turno->turno_id,
            'modificar',
            $gerente->usuario_id,
            'justificacion_puntero',
            null,
            $validated['justificacion'].($validated['tiene_justificacion'] ? '' : ' (límite 09:00)'),
            $request->ip()
        );

        return redirect()->back()->with('success', 'Justificación registrada.');
    }

    public function bloquearTurno(Request $request, $turnoId)
    {
        $turno = Turno::findOrFail($turnoId);

        if ($turno->bloqueado) {
            return redirect()->back()->withErrors(['error' => 'Este turno ya está bloqueado.']);
        }

        if ($turno->es_puntero) {
            return redirect()->back()->withErrors(['error' => 'No se puede bloquear un turno de puntero. Use la opción de justificar.']);
        }

        $validated = $request->validate([
            'motivo' => 'required|string|max:255',
        ]);

        $turno->bloqueado = 1;
        $turno->motivo_bloqueo = $validated['motivo'];
        $turno->bloqueado_por = Auth::user()->usuario_id;
        $turno->bloqueado_en = now();
        $turno->save();

        HistorialCambio::registrar(
            'turnos_diarios',
            $turno->turno_id,
            'modificar',
            Auth::user()->usuario_id,
            'bloqueado',
            0,
            1,
            $request->ip()
        );

        return redirect()->back()->with('success', 'Turno bloqueado. El conductor no operará este día.');
    }

    public function desbloquearTurno(Request $request, $turnoId)
    {
        $turno = Turno::findOrFail($turnoId);

        if (! $turno->bloqueado) {
            return redirect()->back()->withErrors(['error' => 'Este turno no está bloqueado.']);
        }

        $turno->bloqueado = 0;
        $turno->motivo_bloqueo = null;
        $turno->bloqueado_por = null;
        $turno->bloqueado_en = null;
        $turno->save();

        HistorialCambio::registrar(
            'turnos_diarios',
            $turno->turno_id,
            'modificar',
            Auth::user()->usuario_id,
            'bloqueado',
            1,
            0,
            $request->ip()
        );

        return redirect()->back()->with('success', 'Turno desbloqueado.');
    }

    public function destroy($turnoId)
    {
        $turno = Turno::findOrFail($turnoId);

        if ($turno->bloqueado) {
            return redirect()->back()->withErrors(['error' => 'No se puede eliminar un turno bloqueado.']);
        }

        $turno->delete();

        return redirect()->back()->with('success', 'Turno eliminado.');
    }
}
