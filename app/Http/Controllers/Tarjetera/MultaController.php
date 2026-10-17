<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Multa;
use App\Services\MultaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MultaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $multas = Multa::with(['usuario', 'turno'])
            ->whereHas('usuario', function($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
            ->orderByDesc('registrado_en')
            ->paginate(20);
        
        $pendientes = Multa::whereHas('usuario', function($q) use ($puntoId) {
            $q->where('punto_id', $puntoId);
        })->whereIn('estado', ['pendiente', 'pendiente_semana'])
        ->count();
        
        return view('tarjetera.multas.index', compact('multas', 'pendientes'));
    }
    
    public function calcular(Request $request)
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $multas = MultaService::calcularYBorrarMultas($puntoId);
        
        if (empty($multas)) {
            return redirect()->back()->with('info', 'No hay multas por retraso hoy.');
        }
        
        return redirect()->back()->with('success', count($multas) . ' multa(s) registrada(s).');
    }
    
    public function cobrar(Request $request, $id)
    {
        $multa = Multa::findOrFail($id);
        $user = Auth::user();
        
        MultaService::marcarPagada($multa->multa_id, $user->usuario_id);
        
        return redirect()->back()->with('success', 'Multa cobrada correctamente.');
    }
    
    public function marcar(Request $request, $id)
    {
        $multa = Multa::findOrFail($id);
        
        MultaService::marcarPendiente($multa->multa_id);
        
        return redirect()->back()->with('success', 'Multa marcada como pendiente.');
    }
}