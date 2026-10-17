<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use App\Models\Turno;
use App\Models\Multa;
use App\Models\Documento;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        // Stats
        $unidadesActivas = Unidad::where('punto_id', $puntoId)
            ->where('estado', 'activo')
            ->count();
        
        $multasPendientes = Multa::where('estado', 'pendiente')
            ->count();
        
        $turnosHoy = Turno::whereDate('fecha', today())
            ->where('punto_id', $puntoId)
            ->count();
        
        $unidadesEnTurno = Turno::whereDate('fecha', today())
            ->where('punto_id', $puntoId)
            ->where('es_descanso', 0)
            ->count();
        
        // Cola de turno hoy
        $cola = Turno::with('unidad')
            ->whereDate('fecha', today())
            ->where('punto_id', $puntoId)
            ->orderBy('posicion')
            ->limit(10)
            ->get();
        
        // Conductores que han confirmado presencia
        $conductoresPresentes = Turno::with(['usuario', 'unidad'])
            ->whereDate('fecha', today())
            ->where('punto_id', $puntoId)
            ->where('presencia_confirmada', true)
            ->orderBy('presencia_at')
            ->get();
        
        // Conductores que NO han confirmado (sin descanso)
        $conductoresPendientes = Turno::with(['usuario', 'unidad'])
            ->whereDate('fecha', today())
            ->where('punto_id', $puntoId)
            ->where('es_descanso', 0)
            ->where(function($q) {
                $q->whereNull('presencia_confirmada')
                  ->orWhere('presencia_confirmada', false);
            })
            ->orderBy('posicion')
            ->get();
        
        // Documentos por vencer (próximos 30 días)
        $documentosPorVencer = Documento::where(function($q) {
                $q->where('estado', 'vigente')
                  ->whereBetween('fecha_vencimiento', [now()->toDateString(), now()->addDays(30)->toDateString()]);
            })
            ->count();
        
        return view('tarjetera.dashboard', compact(
            'unidadesActivas',
            'multasPendientes',
            'turnosHoy',
            'unidadesEnTurno',
            'cola',
            'conductoresPresentes',
            'conductoresPendientes',
            'documentosPorVencer'
        ));
    }
}