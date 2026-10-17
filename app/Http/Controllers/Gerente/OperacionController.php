<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\CicloAnual;
use App\Models\Punto;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperacionController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $puntoId = $request->get('punto_id');

        $ciclos = CicloAnual::where('activo', 1)->first();

        $puntos = Punto::where('activo', 1)->orderBy('nombre')->get();

        $turnosQuery = Turno::with(['usuario', 'unidad', 'punto'])
            ->where('fecha', $fecha);
        
        $turnosTodos = Turno::with(['usuario', 'unidad', 'punto'])
            ->where('fecha', $fecha)
            ->orderBy('punto_id')
            ->orderBy('posicion')
            ->get();

        if ($puntoId) {
            $turnosQuery->where('punto_id', $puntoId);
        }

        $turnos = $turnosQuery->orderBy('punto_id')
            ->orderBy('posicion')
            ->get();

        $operacion = $turnosTodos->where('es_descanso', 0);
        $descanso = $turnosTodos->where('es_descanso', 1);
        
        $operacionGuaranda = $operacion->where('punto_id', 1);
        $operacionSalinas = $operacion->where('punto_id', 2);
        $descansoGuaranda = $descanso->where('punto_id', 1);
        $descansoSalinas = $descanso->where('punto_id', 2);

        return view('gerente.operacion.index', compact(
            'fecha', 'puntos', 'puntoId', 'turnos', 'operacion', 'descanso', 'ciclos',
            'operacionGuaranda', 'operacionSalinas', 'descansoGuaranda', 'descansoSalinas'
        ));
    }

    public function show($id)
    {
        $turno = Turno::with(['usuario', 'unidad', 'punto', 'salidas', 'salidas.llegada'])
            ->findOrFail($id);

        return view('gerente.operacion.show', compact('turno'));
    }
}
