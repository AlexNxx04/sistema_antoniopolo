<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\CicloAnual;
use App\Models\HistorialCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CicloController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $puntoId = $user->punto_id;

        $cicloActivo = CicloAnual::getCicloActivo();

        $todosLosCiclos = CicloAnual::orderByDesc('anio')->get();

        $todosTerminados = $todosLosCiclos->filter(function($c) {
            return now()->gt($c->fecha_fin);
        });

        return view('tarjetera.ciclos.index', compact(
            'cicloActivo', 'puntoId', 'todosLosCiclos', 'todosTerminados'
        ));
    }

    public function store(Request $request)
    {
        try {
            $anio = $request->input('anio');
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');

            if (!$anio || !$fechaInicio || !$fechaFin) {
                return back()->with('error', 'Todos los campos son requeridos');
            }

            $existe = CicloAnual::where('anio', $anio)->first();
            if ($existe) {
                return back()->with('error', 'Ya existe un ciclo para el año ' . $anio);
            }

            $creadoPor = Auth::id() ?: 1;
            $activo = $request->has('activo') ? 1 : 0;

            if ($activo) {
                DB::table('ciclos_anuales')->where('activo', 1)->update(['activo' => 0]);
            }

            $cicloId = DB::table('ciclos_anuales')->insertGetId([
                'anio' => $anio,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'activo' => $activo,
                'creado_by' => $creadoPor,
            ]);

            HistorialCambio::registrar(
                'ciclos_anuales',
                $cicloId,
                'crear',
                Auth::id(),
                'ciclo_anual',
                null,
                "Año: $anio, Desde: $fechaInicio, Hasta: $fechaFin",
                $request->ip(),
                Auth::user()->punto_id,
                $activo ? "Ciclo $anio creado y ACTIVADO" : "Ciclo $anio creado"
            );

            $mensaje = $activo
                ? "Ciclo {$anio} activado correctamente"
                : "Ciclo {$anio} creado correctamente";

            return back()->with('success', $mensaje);
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ciclo = CicloAnual::findOrFail($id);

            $anio = $request->input('anio');
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');

            if (!$anio || !$fechaInicio || !$fechaFin) {
                return back()->with('error', 'Todos los campos son requeridos');
            }

            $existe = CicloAnual::where('anio', $anio)->where('ciclo_id', '!=', $id)->first();
            if ($existe) {
                return back()->with('error', 'Ya existe otro ciclo para el año ' . $anio);
            }

            HistorialCambio::registrar(
                'ciclos_anuales',
                $id,
                'modificar',
                Auth::id(),
                'datos_ciclo',
                "Año: {$ciclo->anio}",
                "Año: $anio",
                $request->ip(),
                Auth::user()->punto_id,
                "Ciclo $anio actualizado"
            );

            $ciclo->update([
                'anio' => $anio,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ]);

            return back()->with('success', "Ciclo {$anio} actualizado correctamente");
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function activar(Request $request, $id)
    {
        $ciclo = CicloAnual::findOrFail($id);

        DB::table('ciclos_anuales')->where('activo', 1)->update(['activo' => 0]);
        $ciclo->update(['activo' => 1]);

        HistorialCambio::registrar(
            'ciclos_anuales',
            $id,
            'modificar',
            Auth::id(),
            'activo',
            '0',
            '1',
            $request->ip(),
            Auth::user()->punto_id,
            "Ciclo {$ciclo->anio} ACTIVADO"
        );

        return redirect()->route('tarjetera.ciclos.index')
            ->with('success', "Ciclo {$ciclo->anio} activado");
    }

    public function desactivar($id)
    {
        $ciclo = CicloAnual::findOrFail($id);

        HistorialCambio::registrar(
            'ciclos_anuales',
            $id,
            'modificar',
            Auth::id(),
            'activo',
            '1',
            '0',
            request()->ip(),
            Auth::user()->punto_id,
            "Ciclo {$ciclo->anio} desactivado"
        );

        $ciclo->update(['activo' => 0]);

        return redirect()->route('tarjetera.ciclos.index')
            ->with('success', "Ciclo {$ciclo->anio} desactivado");
    }
}