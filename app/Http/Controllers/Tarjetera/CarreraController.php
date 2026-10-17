<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarreraController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $turnosActivos = Turno::with(['usuario', 'unidad'])
            ->where('punto_id', $puntoId)
            ->whereDate('fecha', now()->toDateString())
            ->where('estado', 'confirmado')
            ->orderBy('posicion')
            ->get();
        
        $carrerasActivas = DB::table('carreras_especiales as ce')
            ->leftJoin('turnos_diarios as t', 'ce.turno_id', '=', 't.turno_id')
            ->leftJoin('usuarios as u', 't.usuario_id', '=', 'u.usuario_id')
            ->leftJoin('unidades as un', 't.unidad_id', '=', 'un.unidad_id')
            ->where('t.punto_id', $puntoId)
            ->whereNull('ce.hora_regreso')
            ->select('ce.*', 'u.nombres', 'un.placa', 'un.numero_interno', 't.posicion', 't.punto_id')
            ->get();
        
        $carrerasFinalizadas = DB::table('carreras_especiales as ce')
            ->leftJoin('turnos_diarios as t', 'ce.turno_id', '=', 't.turno_id')
            ->leftJoin('usuarios as u', 't.usuario_id', '=', 'u.usuario_id')
            ->where('t.punto_id', $puntoId)
            ->whereNotNull('ce.hora_regreso')
            ->select('ce.*', 'u.nombres', 't.posicion')
            ->orderByDesc('ce.hora_regreso')
            ->limit(20)
            ->get();
        
        return view('tarjetera.carrera.index', compact('turnosActivos', 'carrerasActivas', 'carrerasFinalizadas'));
    }
    
    public function registrar(Request $request)
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        $turnoId = $request->input('turno_id');
        
        $turno = Turno::findOrFail($turnoId);
        
        if ($turno->estado !== 'confirmado') {
            return redirect()->back()->with('error', 'Este turno no está confirmado para salir.');
        }
        
        DB::table('carreras_especiales')->insert([
            'turno_id' => $turnoId,
            'hora_salida_especial' => now(),
            'posicion_antes' => $turno->posicion,
            'registrado_by' => $user->usuario_id,
        ]);
        
        return redirect()->back()->with('success', 'Carrera especial registrada para ' . $turno->usuario->nombres);
    }
    
    public function finalizar(Request $request, $id)
    {
        DB::table('carreras_especiales')
            ->where('carrera_id', $id)
            ->update([
                'hora_regreso' => now(),
            ]);
        
        return redirect()->back()->with('success', 'Carrera finalizada. El turno se reubicará según su posición.');
    }
}