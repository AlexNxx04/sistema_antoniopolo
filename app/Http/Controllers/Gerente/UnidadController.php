<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\HistorialCambio;
use App\Models\Punto;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnidadController extends Controller
{
    public function index(Request $request)
    {
        $query = Unidad::with('punto');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('placa', 'like', "%{$buscar}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('punto_id')) {
            $query->where('punto_id', $request->punto_id);
        }

        $unidades = $query->orderBy('punto_id')->orderBy('numero_interno')->get();
        $puntos = Punto::orderBy('nombre')->get();

        return view('gerente.unidades.index', compact('unidades', 'puntos'));
    }

    public function create()
    {
        $puntos = Punto::orderBy('nombre')->get();

        return view('gerente.unidades.create', compact('puntos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => ['required', 'string', 'max:20', 'unique:unidades,placa'],
            'numero_interno' => ['required', 'string', 'max:10', 'unique:unidades,numero_interno'],
            'celular_gps' => ['nullable', 'string', 'max:20'],
            'punto_id' => ['required', 'exists:puntos,punto_id'],
        ]);

        $validated['estado'] = 'activo';
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        $unidad = Unidad::create($validated);

        HistorialCambio::registrar(
            'unidades',
            $unidad->unidad_id,
            'crear',
            Auth::user()->usuario_id,
            null,
            null,
            "Unidad {$unidad->placa} - {$unidad->numero_interno}",
            $request->ip()
        );

        return redirect()->route('gerente.unidades.index')
            ->with('success', 'Unidad creada exitosamente.');
    }

    public function show($id)
    {
        $unidad = Unidad::with('punto')->findOrFail($id);
        
        $conductor = null;
        if (class_exists(\App\Models\User::class)) {
            $conductor = \App\Models\User::where('unidad_id', $id)->first();
        }
        
        $turnos = null;
        if (class_exists(\App\Models\Turno::class)) {
            $turnos = \App\Models\Turno::where('unidad_id', $id)
                ->with('punto')
                ->orderBy('fecha', 'desc')
                ->limit(10)
                ->get();
        }

        return view('gerente.unidades.show', compact('unidad', 'conductor', 'turnos'));
    }

    public function edit($id)
    {
        $unidad = Unidad::findOrFail($id);
        $puntos = Punto::orderBy('nombre')->get();

        return view('gerente.unidades.edit', compact('unidad', 'puntos'));
    }

    public function update(Request $request, $id)
    {
        $unidad = Unidad::findOrFail($id);

        $validated = $request->validate([
            'placa' => ['required', 'string', 'max:20', Rule::unique('unidades', 'placa')->ignore($unidad->unidad_id, 'unidad_id')],
            'numero_interno' => ['required', 'string', 'max:10', Rule::unique('unidades', 'numero_interno')->ignore($unidad->unidad_id, 'unidad_id')],
            'celular_gps' => ['nullable', 'string', 'max:20'],
            'punto_id' => ['required', 'exists:puntos,punto_id'],
            'estado' => ['required', Rule::in(['activo', 'inactivo', 'mantenimiento'])],
        ]);

        $cambios = [];
        $campos = ['placa', 'numero_interno', 'celular_gps', 'punto_id', 'estado'];

        foreach ($campos as $campo) {
            if ($validated[$campo] != $unidad->$campo) {
                $cambios[$campo] = [
                    'anterior' => $unidad->$campo,
                    'nuevo' => $validated[$campo],
                ];
            }
        }

        $validated['updated_at'] = now();
        $unidad->update($validated);

        foreach ($cambios as $campo => $valores) {
            HistorialCambio::registrar(
                'unidades',
                $unidad->unidad_id,
                'modificar',
                Auth::user()->usuario_id,
                $campo,
                $valores['anterior'],
                $valores['nuevo'],
                $request->ip()
            );
        }

        return redirect()->route('gerente.unidades.index')
            ->with('success', 'Unidad actualizada exitosamente.');
    }

    public function destroy(Request $request, $id)
    {
        $unidad = Unidad::findOrFail($id);

        $estadoAnterior = $unidad->estado;
        $unidad->update(['estado' => 'dado_baja', 'updated_at' => now()]);

        HistorialCambio::registrar(
            'unidades',
            $unidad->unidad_id,
            'modificar',
            Auth::user()->usuario_id,
            'estado',
            $estadoAnterior,
            'dado_baja',
            $request->ip()
        );

        return redirect()->route('gerente.unidades.index')
            ->with('success', 'Unidad dada de baja correctamente.');
    }

    public function toggleEstado(Request $request, $id)
    {
        $unidad = Unidad::findOrFail($id);
        $nuevoEstado = $unidad->estado === 'activo' ? 'inactivo' : 'activo';

        $unidad->update(['estado' => $nuevoEstado, 'updated_at' => now()]);

        HistorialCambio::registrar(
            'unidades',
            $unidad->unidad_id,
            'modificar',
            Auth::user()->usuario_id,
            'estado',
            $unidad->estado,
            $nuevoEstado,
            $request->ip()
        );

        $mensaje = $nuevoEstado === 'activo' ? 'activada' : 'desactivada';

        return redirect()->back()->with('success', "Unidad {$mensaje} exitosamente.");
    }

    public function reactivar(Request $request, $id)
    {
        $unidad = Unidad::findOrFail($id);

        if ($unidad->estado !== 'dado_baja') {
            return redirect()->back()->withErrors(['error' => 'Esta unidad no está dada de baja.']);
        }

        $unidad->update(['estado' => 'activo', 'updated_at' => now()]);

        HistorialCambio::registrar(
            'unidades',
            $unidad->unidad_id,
            'modificar',
            Auth::user()->usuario_id,
            'estado',
            'dado_baja',
            'activo',
            $request->ip()
        );

        return redirect()->route('gerente.unidades.index')
            ->with('success', 'Unidad reactivada exitosamente.');
    }
}
