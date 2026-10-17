<?php
namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\CicloAnual;
use App\Models\Unidad;
use App\Models\User;
use App\Models\Turno;
use App\Models\HistorialCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SorteoController extends Controller
{
    private function asegurarColumnas()
    {
        if (!Schema::hasColumn('grupos_descanso', 'es_vacante')) {
            Schema::table('grupos_descanso', function ($table) {
                $table->boolean('es_vacante')->default(false)->after('dia_descanso');
            });
        }
        if (!Schema::hasColumn('ciclos_anuales', 'vuelta_actual')) {
            Schema::table('ciclos_anuales', function ($table) {
                $table->integer('vuelta_actual')->default(0)->after('activo');
            });
        }
        if (!Schema::hasColumn('grupo_miembros', 'posicion_en_grupo')) {
            Schema::table('grupo_miembros', function ($table) {
                $table->integer('posicion_en_grupo')->default(0)->after('miembro_id');
            });
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        $cicloActivo = CicloAnual::getCicloActivo();

        $unidades = Unidad::where('punto_id', $puntoId)
            ->where('estado', 'activo')
            ->orderBy('numero_interno')
            ->get();

        $hoy = now()->toDateString();
        $today = now()->format('N');

        $turnosHoy = Turno::with(['usuario', 'unidad'])
            ->where('fecha', $hoy)
            ->where('punto_id', $puntoId)
            ->orderBy('posicion')
            ->get();

        $yaSorteado = $turnosHoy->isNotEmpty();
        $gruposData = [];

        $conductorIds = User::where('rol_id', 3)
            ->where('punto_id', $puntoId)
            ->where('activo', 1)
            ->pluck('usuario_id');

        if ($conductorIds->isEmpty()) {
            return view('tarjetera.sorteo.index', compact(
                'cicloActivo', 'unidades', 'turnosHoy', 'yaSorteado', 'puntoId', 'gruposData'
            ));
        }

        $gruposIds = DB::table('grupo_miembros')
            ->whereIn('usuario_id', $conductorIds)
            ->pluck('grupo_id')
            ->unique()
            ->values();

        if ($gruposIds->isEmpty()) {
            $yaSorteado = false;
            return view('tarjetera.sorteo.index', compact(
                'cicloActivo', 'unidades', 'turnosHoy', 'yaSorteado', 'puntoId', 'gruposData'
            ));
        }

        $grupos = DB::table('grupos_descanso')
            ->whereIn('grupo_id', $gruposIds)
            ->where('activo', 1)
            ->orderBy('grupo_id')
            ->get();

        foreach ($grupos as $grupo) {
            $miembros = DB::table('grupo_miembros')
                ->join('usuarios', 'grupo_miembros.usuario_id', '=', 'usuarios.usuario_id')
                ->where('grupo_miembros.grupo_id', $grupo->grupo_id)
                ->orderBy('grupo_miembros.posicion_en_grupo')
                ->select('usuarios.usuario_id', 'usuarios.nombres', 'usuarios.telefono')
                ->get();

            if ($miembros->isNotEmpty()) {
                $gruposData[] = [
                    'grupo' => $grupo,
                    'miembros' => $miembros
                ];
            }
        }

        if ($puntoId == 1 && $gruposData) {
            $semanaCiclo = ((now()->weekOfYear - 1) % 20) + 1;
            $cicloActivoLocal = CicloAnual::getCicloActivo();
            $vueltaActual = $cicloActivoLocal ? ($cicloActivoLocal->vuelta_actual ?? 0) : 0;

            $posicionRotativa = intval((($semanaCiclo - 1) / 5)) % 4;
            $grupoPrestaVacante = (($semanaCiclo - 1) % 5) + 1;

            $grupoPrestaData = null;
            foreach ($gruposData as $g) {
                if ($g['grupo']->nombre == 'Grupo ' . $grupoPrestaVacante) {
                    $grupoPrestaData = $g;
                    break;
                }
            }

            if ($grupoPrestaData) {
                $grupo7 = (object) [
                    'grupo_id' => 'g7',
                    'nombre' => 'Grupo 7',
                    'dia_descanso' => 7,
                    'es_vacante' => 0,
                    'es_dominical' => true,
                ];
                $gruposData[] = [
                    'grupo' => $grupo7,
                    'miembros' => $grupoPrestaData['miembros'],
                    'grupo_presta' => $grupoPrestaVacante
                ];
            }
        }

        return view('tarjetera.sorteo.index', compact(
            'cicloActivo', 'unidades', 'turnosHoy', 'yaSorteado', 'puntoId', 'gruposData'
        ));
    }

    public function ejecutar(Request $request)
    {
        $this->asegurarColumnas();

        $puntoId = Auth::user()->punto_id;
        $cicloId = $request->input('ciclo_id');
        $cicloActivo = CicloAnual::find($cicloId);
        $userId = Auth::id();

        $conductores = User::where('rol_id', 3)
            ->where('punto_id', $puntoId)
            ->where('activo', 1)
            ->with('unidad')
            ->get();

        if ($conductores->isEmpty()) {
            return redirect()->route('tarjetera.sorteo.index')
                ->with('error', 'No hay conductores activos');
        }

        $numOperan = $puntoId == 1 ? 19 : 18;
        $numDescansan = $puntoId == 1 ? 4 : 3;

        $hoy = now()->toDateString();
        $today = now()->format('N');

        $conductorIds = $conductores->pluck('usuario_id');

        $gruposIds = DB::table('grupo_miembros')
            ->whereIn('usuario_id', $conductorIds)
            ->pluck('grupo_id')
            ->unique()
            ->values();

        $usuariosDescansanIds = [];

        if ($puntoId == 1) {
            $semanaCiclo = ((now()->weekOfYear - 1) % 20) + 1;
            $vueltaActual = $cicloActivo ? ($cicloActivo->vuelta_actual ?? 0) : 0;

            $posicionRotativa = intval((($semanaCiclo - 1) / 5)) % 4;
            $grupoPrestaVacante = (($semanaCiclo - 1) % 5) + 1;

            if ($today == 7) {
                $grupoPresta = DB::table('grupos_descanso')
                    ->whereIn('grupo_id', $gruposIds)
                    ->where('nombre', 'Grupo ' . $grupoPrestaVacante)
                    ->first();

                if ($grupoPresta) {
                    $usuariosDescansanIds = DB::table('grupo_miembros')
                        ->where('grupo_id', $grupoPresta->grupo_id)
                        ->pluck('usuario_id')
                        ->toArray();
                }
            } else {
                $idsDescansan = DB::table('grupos_descanso')
                    ->whereIn('grupo_id', $gruposIds)
                    ->where('dia_descanso', $today)
                    ->pluck('grupo_id');

                $usuariosDescansanIds = DB::table('grupo_miembros')
                    ->whereIn('grupo_id', $idsDescansan)
                    ->pluck('usuario_id')
                    ->toArray();
            }

            $usuariosDescansanIds = array_unique($usuariosDescansanIds);
            $usuariosDescansanIds = collect(array_values($usuariosDescansanIds));
        } else {
            $idsDescansan = DB::table('grupos_descanso')
                ->whereIn('grupo_id', $gruposIds)
                ->where('dia_descanso', $today)
                ->pluck('grupo_id');

            $usuariosDescansanIds = DB::table('grupo_miembros')
                ->whereIn('grupo_id', $idsDescansan)
                ->pluck('usuario_id');
        }

        $descansanArray = $conductores->filter(fn($c) => $usuariosDescansanIds->contains($c->usuario_id))->values()->all();
        $operanArray = $conductores->reject(fn($c) => $usuariosDescansanIds->contains($c->usuario_id))->values()->all();

        shuffle($operanArray);

        $punterosArray = array_slice($operanArray, 0, 2);
        $seguidoresArray = array_slice($operanArray, 2, $numOperan - 2);

        $conductorUnidades = [];
        foreach ($conductores as $c) {
            if ($c->unidad) {
                $conductorUnidades[$c->usuario_id] = $c->unidad->unidad_id;
            }
        }

        DB::statement("DELETE FROM turnos_diarios WHERE DATE(fecha) = ? AND punto_id = ?", [$hoy, $puntoId]);

        DB::beginTransaction();

        try {
            $posicion = 1;

            foreach ($punterosArray as $conductor) {
                $unidadId = $conductorUnidades[$conductor->usuario_id] ?? null;
                DB::table('turnos_diarios')->insert([
                    'ciclo_id' => $cicloId,
                    'fecha' => $hoy,
                    'punto_id' => $puntoId,
                    'usuario_id' => $conductor->usuario_id,
                    'unidad_id' => $unidadId,
                    'posicion' => $posicion,
                    'es_puntero' => 1,
                    'es_descanso' => 0,
                    'generado_by' => $userId,
                    'generado_at' => now(),
                ]);
                $posicion++;
            }

            foreach ($seguidoresArray as $conductor) {
                $unidadId = $conductorUnidades[$conductor->usuario_id] ?? null;
                DB::table('turnos_diarios')->insert([
                    'ciclo_id' => $cicloId,
                    'fecha' => $hoy,
                    'punto_id' => $puntoId,
                    'usuario_id' => $conductor->usuario_id,
                    'unidad_id' => $unidadId,
                    'posicion' => $posicion,
                    'es_puntero' => 0,
                    'es_descanso' => 0,
                    'generado_by' => $userId,
                    'generado_at' => now(),
                ]);
                $posicion++;
            }

            foreach ($descansanArray as $conductor) {
                $unidadId = $conductorUnidades[$conductor->usuario_id] ?? null;
                DB::table('turnos_diarios')->insert([
                    'ciclo_id' => $cicloId,
                    'fecha' => $hoy,
                    'punto_id' => $puntoId,
                    'usuario_id' => $conductor->usuario_id,
                    'unidad_id' => $unidadId,
                    'posicion' => $posicion,
                    'es_puntero' => 0,
                    'es_descanso' => 1,
                    'generado_by' => $userId,
                    'generado_at' => now(),
                ]);
                $posicion++;
            }

            foreach ($descansanArray as $conductor) {
                User::where('usuario_id', $conductor->usuario_id)->increment('dias_descansados');
            }

            $fecha = now()->subDay()->toDateString();
            foreach ($punterosArray as $conductor) {
                User::where('usuario_id', $conductor->usuario_id)->update([
                    'veces_puntero' => DB::raw('veces_puntero + 1'),
                    'ultimo_descanso' => $fecha,
                ]);
            }

            DB::commit();

            HistorialCambio::registrar(
                'turnos_diarios',
                $cicloId,
                'generar',
                $userId,
                'sorteo_diario',
                null,
                "Fecha: $hoy | Operan: " . count($operanArray) . " | Descansan: " . count($descansanArray),
                request()->ip(),
                $puntoId,
                'Sorteo diario ejecutado'
            );

            return redirect()->route('tarjetera.sorteo.index')
                ->with('success', 'Sorteo completado');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tarjetera.sorteo.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function crearGrupos(Request $request)
    {
        $this->asegurarColumnas();

        $puntoId = Auth::user()->punto_id;
        $cicloId = $request->input('ciclo_id');

        $conductores = User::where('rol_id', 3)
            ->where('punto_id', $puntoId)
            ->where('activo', 1)
            ->with('unidad')
            ->get();

        if ($conductores->isEmpty()) {
            return redirect()->route('tarjetera.sorteo.index')
                ->with('error', 'No hay conductores activos en este punto');
        }

        $conductorIds = $conductores->pluck('usuario_id');

        $existenGrupos = DB::table('grupo_miembros')
            ->join('grupos_descanso', 'grupo_miembros.grupo_id', '=', 'grupos_descanso.grupo_id')
            ->whereIn('grupo_miembros.usuario_id', $conductorIds)
            ->where('grupos_descanso.ciclo_id', $cicloId)
            ->where('grupos_descanso.activo', 1)
            ->exists();

        if ($existenGrupos) {
            $gruposIdsExistentes = DB::table('grupos_descanso')
                ->where('ciclo_id', $cicloId)
                ->where('activo', 1)
                ->pluck('grupo_id');

            DB::table('grupo_miembros')->whereIn('grupo_id', $gruposIdsExistentes)->delete();
            DB::table('grupos_descanso')->whereIn('grupo_id', $gruposIdsExistentes)->delete();
        }

        $conductoresArray = $conductores->all();
        shuffle($conductoresArray);

        DB::beginTransaction();

        try {
            if ($puntoId == 1) {
                $ciclo = CicloAnual::find($cicloId);
                if ($ciclo) {
                    $ciclo->vuelta_actual = 0;
                    $ciclo->save();
                }

                for ($g = 1; $g <= 6; $g++) {
                    $esVacante = ($g == 6) ? 1 : 0;
                    $gId = DB::table('grupos_descanso')->insertGetId([
                        'ciclo_id' => $cicloId,
                        'nombre' => 'Grupo ' . $g,
                        'dia_descanso' => $g,
                        'es_vacante' => $esVacante,
                        'activo' => 1,
                        'creado_por' => Auth::id(),
                        'creado_en' => now(),
                    ]);

                    if ($g <= 5) {
                        $miembros = array_slice($conductoresArray, ($g - 1) * 4, 4);
                        $posicion = 1;
                        foreach ($miembros as $conductor) {
                            DB::table('grupo_miembros')->insert([
                                'grupo_id' => $gId,
                                'usuario_id' => $conductor->usuario_id,
                                'unidad_id' => $conductor->unidad_id ?? null,
                                'posicion_en_grupo' => $posicion,
                            ]);
                            $posicion++;
                        }
                    } else {
                        $fijos = array_slice($conductoresArray, 20, 3);
                        $posicion = 1;
                        foreach ($fijos as $conductor) {
                            DB::table('grupo_miembros')->insert([
                                'grupo_id' => $gId,
                                'usuario_id' => $conductor->usuario_id,
                                'unidad_id' => $conductor->unidad_id ?? null,
                                'posicion_en_grupo' => $posicion,
                            ]);
                            $posicion++;
                        }

                        $vacante = $conductoresArray[20] ?? null;
                        if ($vacante) {
                            DB::table('grupo_miembros')->insert([
                                'grupo_id' => $gId,
                                'usuario_id' => $vacante->usuario_id,
                                'unidad_id' => $vacante->unidad_id ?? null,
                                'posicion_en_grupo' => 4,
                            ]);
                        }
                    }
                }

                DB::commit();

                HistorialCambio::registrar(
                    'grupos_descanso',
                    $cicloId,
                    'crear',
                    Auth::id(),
                    'grupos_descanso',
                    null,
                    $puntoId == 1 ? 'Guaranda: 5 grupos de 4 + G6 con vacante' : 'Salinas: 7 grupos de 3',
                    request()->ip(),
                    $puntoId,
                    'Grupos de descanso creados'
                );

                return redirect()->route('tarjetera.sorteo.index')
                    ->with('success', 'Grupos Guaranda: 5 grupos de 4 + Grupo 6 (3 fijos + 1 vacante)');

            } else {
                $diasSalinas = [1, 2, 3, 4, 5, 6, 7];

                for ($g = 1; $g <= 7; $g++) {
                    $gId = DB::table('grupos_descanso')->insertGetId([
                        'ciclo_id' => $cicloId,
                        'nombre' => 'Grupo ' . $g,
                        'dia_descanso' => $diasSalinas[$g - 1],
                        'es_vacante' => 0,
                        'activo' => 1,
                        'creado_por' => Auth::id(),
                        'creado_en' => now(),
                    ]);
                    $miembros = array_slice($conductoresArray, ($g - 1) * 3, 3);
                    $posicion = 1;
                    foreach ($miembros as $conductor) {
                        DB::table('grupo_miembros')->insert([
                            'grupo_id' => $gId,
                            'usuario_id' => $conductor->usuario_id,
                            'unidad_id' => $conductor->unidad_id ?? null,
                            'posicion_en_grupo' => $posicion,
                        ]);
                        $posicion++;
                    }
                }

                DB::commit();

                HistorialCambio::registrar(
                    'grupos_descanso',
                    $cicloId,
                    'crear',
                    Auth::id(),
                    'grupos_descanso',
                    null,
                    'Salinas: 7 grupos de 3',
                    request()->ip(),
                    $puntoId,
                    'Grupos de descanso creados'
                );

                return redirect()->route('tarjetera.sorteo.index')
                    ->with('success', 'Grupos Salinas: 7 grupos de 3');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tarjetera.sorteo.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function randomizarDias(Request $request)
    {
        $puntoId = Auth::user()->punto_id;
        $cicloId = $request->input('ciclo_id');

        $this->asegurarColumnas();

        $conductorIds = User::where('rol_id', 3)
            ->where('punto_id', $puntoId)
            ->where('activo', 1)
            ->pluck('usuario_id');

        $gruposIds = DB::table('grupo_miembros')
            ->whereIn('usuario_id', $conductorIds)
            ->pluck('grupo_id')
            ->unique()
            ->values();

        $grupos = DB::table('grupos_descanso')
            ->whereIn('grupo_id', $gruposIds)
            ->where('activo', 1)
            ->orderBy('grupo_id')
            ->get();

        if ($grupos->isEmpty()) {
            return redirect()->route('tarjetera.sorteo.index')
                ->with('error', 'No hay grupos para randomizar');
        }

        try {
            $numGrupos = $grupos->count();

            if ($puntoId == 2) {
                $dias = range(1, 7);
                shuffle($dias);

                $i = 0;
                foreach ($grupos as $grupo) {
                    DB::table('grupos_descanso')
                        ->where('grupo_id', $grupo->grupo_id)
                        ->update(['dia_descanso' => $dias[$i]]);
                    $i++;
                }
            } else {
                $dias = range(1, 6);
                shuffle($dias);

                $i = 0;
                foreach ($grupos as $grupo) {
                    if ($grupo->nombre != 'Grupo 6') {
                        DB::table('grupos_descanso')
                            ->where('grupo_id', $grupo->grupo_id)
                            ->update(['dia_descanso' => $dias[$i]]);
                        $i++;
                    }
                }
            }

            HistorialCambio::registrar(
                'grupos_descanso',
                $cicloId,
                'modificar',
                Auth::id(),
                'dia_descanso',
                'dias anteriores',
                'dias randomizados',
                request()->ip(),
                $puntoId,
                'Días de descanso randomizados'
            );

            return redirect()->route('tarjetera.sorteo.index')
                ->with('success', 'Dias randomizados correctamente');

        } catch (\Exception $e) {
            return redirect()->route('tarjetera.sorteo.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function eliminarGrupos(Request $request)
    {
        $puntoId = Auth::user()->punto_id;
        $cicloId = $request->input('ciclo_id');

        $gruposIds = DB::table('grupos_descanso')
            ->where('ciclo_id', $cicloId)
            ->where('activo', 1)
            ->pluck('grupo_id');

        if ($gruposIds->isNotEmpty()) {
            DB::table('grupo_miembros')->whereIn('grupo_id', $gruposIds)->delete();
            DB::table('grupos_descanso')->whereIn('grupo_id', $gruposIds)->delete();
        }

        $ciclo = CicloAnual::find($cicloId);
        if ($ciclo) {
            $ciclo->vuelta_actual = 0;
            $ciclo->save();
        }

        HistorialCambio::registrar(
            'grupos_descanso',
            $cicloId,
            'eliminar',
            Auth::id(),
            'grupos_descanso',
            'grupos anteriores',
            null,
            request()->ip(),
            $puntoId,
            'Grupos de descanso eliminados'
        );

        return redirect()->route('tarjetera.sorteo.index')
            ->with('success', 'Grupos eliminados. Ahora puedes crear nuevos grupos.');
    }

    public function limpiarSorteos(Request $request)
    {
        $puntoId = Auth::user()->punto_id;
        $hoy = now()->toDateString();

        DB::table('turnos_diarios')
            ->where('punto_id', $puntoId)
            ->whereDate('fecha', $hoy)
            ->delete();

        HistorialCambio::registrar(
            'turnos_diarios',
            0,
            'eliminar',
            Auth::id(),
            'turnos_diarios',
            "Turnos del $hoy",
            null,
            request()->ip(),
            $puntoId,
            'Turnos del día eliminados'
        );

        return redirect()->route('tarjetera.sorteo.index')
            ->with('success', 'Sorteos del dia eliminados.');
    }
}