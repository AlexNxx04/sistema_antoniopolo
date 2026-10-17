<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\CicloAnual;
use App\Models\HistorialCambio;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CicloAnualController extends Controller
{
    public function index()
    {
        $ciclos = CicloAnual::with('creador')
            ->orderBy('anio', 'desc')
            ->paginate(10);

        $cicloActivo = CicloAnual::getCicloActivo();

        return view('gerente.ciclos.index', compact('ciclos', 'cicloActivo'));
    }

    public function create()
    {
        return view('gerente.ciclos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anio' => 'required|integer|min:2020|max:2100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ], [
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ]);

        try {
            $gerente = Auth::user();

            $data = [
                'anio' => $validated['anio'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'],
                'activo' => 0,
                'creado_by' => $gerente->usuario_id,
                'creado_at' => now(),
            ];

            $ciclo = CicloAnual::create($data);

            HistorialCambio::registrar(
                'ciclos_anuales',
                $ciclo->ciclo_id,
                'crear',
                $gerente->usuario_id,
                null,
                null,
                "Ciclo {$ciclo->anio}",
                $request->ip()
            );

            return redirect()->route('gerente.ciclos.show', $ciclo->ciclo_id)
                ->with('success', "Ciclo anual {$ciclo->anio} creado exitosamente.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear el ciclo: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $ciclo = CicloAnual::with(['grupos.unidades', 'creador'])->findOrFail($id);
        $unidades = Unidad::where('estado', 'activo')->with('punto')->get();

        return view('gerente.ciclos.show', compact('ciclo', 'unidades'));
    }

    public function activar(Request $request, $id)
    {
        $ciclo = CicloAnual::findOrFail($id);

        try {
            CicloAnual::where('activo', 1)->update(['activo' => 0]);

            $ciclo->activo = 1;
            $ciclo->save();

            $gerente = Auth::user();

            HistorialCambio::registrar(
                'ciclos_anuales',
                $ciclo->ciclo_id,
                'modificar',
                $gerente->usuario_id,
                'activo',
                0,
                1,
                $request->ip()
            );

            return redirect()->back()->with('success', "Ciclo {$ciclo->anio} activado exitosamente.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al activar el ciclo.']);
        }
    }

    public function destroy(Request $request, $id)
    {
        $ciclo = CicloAnual::findOrFail($id);

        if ($ciclo->activo) {
            return redirect()->back()->withErrors(['error' => 'No se puede eliminar un ciclo activo.']);
        }

        try {
            $gerente = Auth::user();

            HistorialCambio::registrar(
                'ciclos_anuales',
                $ciclo->ciclo_id,
                'eliminar',
                $gerente->usuario_id,
                null,
                "Ciclo {$ciclo->anio}",
                null,
                $request->ip()
            );

            $ciclo->delete();

            return redirect()->route('gerente.ciclos.index')
                ->with('success', 'Ciclo eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el ciclo.']);
        }
    }
}
