<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\CicloAnual;
use App\Models\GrupoDescanso;
use App\Models\Turno;
use App\Models\Unidad;
use Illuminate\Http\Request;

class SorteosController extends Controller
{
    public function index(Request $request)
    {
        $cicloId = $request->get('ciclo_id');

        $ciclos = CicloAnual::orderByDesc('anio')->get();

        if (! $cicloId && $ciclos->isNotEmpty()) {
            $cicloId = $ciclos->where('activo', 1)->first()?->ciclo_id ?? $ciclos->first()->ciclo_id;
        }

        $cicloActual = CicloAnual::with(['grupos.miembros.unidad'])
            ->find($cicloId);

        $grupos = GrupoDescanso::where('ciclo_id', $cicloId)
            ->with(['miembros.unidad'])
            ->orderBy('dia_descanso')
            ->get();

        $unidades = Unidad::where('estado', 'activo')
            ->orderBy('numero_interno')
            ->get();

        $turnos = Turno::with(['punto', 'usuario', 'unidad'])
            ->where('ciclo_id', $cicloId)
            ->orderByDesc('fecha')
            ->limit(20)
            ->get();

        $historial = [];

        return view('gerente.sorteos.index', compact(
            'ciclos', 'cicloActual', 'grupos', 'unidades', 'turnos', 'historial'
        ));
    }

    public function show($id)
    {
        $ciclo = CicloAnual::with(['grupos.miembros.unidad'])
            ->findOrFail($id);

        return view('gerente.sorteos.show', compact('ciclo'));
    }

    public function grupoShow($cicloId, $grupoId)
    {
        $ciclo = CicloAnual::findOrFail($cicloId);
        $grupo = GrupoDescanso::with(['miembros.unidad'])
            ->where('ciclo_id', $cicloId)
            ->findOrFail($grupoId);

        return view('gerente.sorteos.grupo', compact('ciclo', 'grupo'));
    }
}
