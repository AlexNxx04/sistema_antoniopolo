<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Salida;
use App\Models\Turno;
use App\Models\PosicionGps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalidaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $salidas = Salida::with(['turno', 'turno.unidad', 'turno.usuario'])
            ->whereHas('turno', function($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
            ->whereDate('created_at', now()->toDateString())
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('tarjetera.salidas.index', compact('salidas'));
    }
    
    public function create()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $turnos = Turno::with(['usuario', 'unidad'])
            ->where('punto_id', $puntoId)
            ->whereDate('fecha', now()->toDateString())
            ->where('bloqueado', 0)
            ->where('es_descanso', 0)
            ->orderBy('posicion')
            ->get();
        
        return view('tarjetera.salidas.create', compact('turnos'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|exists:turnos_diarios,turno_id',
        ]);
        
        $user = Auth::user();
        $turno = Turno::with('unidad')->findOrFail($request->turno_id);
        
        // Obtener posición GPS actual
        $posicion = PosicionGps::where('unidad_id', $turno->unidad_id)
            ->orderByDesc('timestamp')
            ->first();
        
        $salida = Salida::create([
            'turno_id' => $turno->turno_id,
            'punto_id' => $turno->punto_id,
            'unidad_id' => $turno->unidad_id,
            'usuario_id' => $turno->usuario_id,
            'hora_salida' => now(),
            'latitud' => $posicion->latitud ?? null,
            'longitud' => $posicion->longitud ?? null,
            'registrado_por' => $user->usuario_id,
        ]);
        
        return redirect()->route('tarjetera.salidas.index')->with('success', 'Salida registrada correctamente.');
    }
}