<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Justificativo;
use App\Models\Turno;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JustificativoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $justificativos = Justificativo::with(['turno.usuario', 'turno.unidad'])
            ->whereHas('turno', function($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
            ->whereDate('registrado_at', now()->toDateString())
            ->orderByDesc('registrado_at')
            ->get();
        
        $pendientes = $justificativos->whereNull('estado')->count();
        
        return view('tarjetera.justificativos.index', compact('justificativos', 'pendientes'));
    }
    
    public function pendientes()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        return Justificativo::with(['turno.usuario', 'turno.unidad'])
            ->whereHas('turno', function($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
            ->whereNull('estado')
            ->whereDate('registrado_at', now()->toDateString())
            ->orderByDesc('registrado_at')
            ->get();
    }
    
    public function aprobar(Request $request, $id)
    {
        $justificativo = Justificativo::findOrFail($id);
        $user = Auth::user();
        
        $turno = $justificativo->turno;
        
        DB::beginTransaction();
        try {
            $justificativo->estado = 'aprobado';
            $justificativo->revisado_by = $user->usuario_id;
            $justificativo->revisado_at = now();
            $justificativo->save();
            
            $this->notificarAdelanto($turno, $justificativo);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Justificativo aprobado.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Error al aprobar: ' . $e->getMessage());
        }
    }
    
    public function rechazar(Request $request, $id)
    {
        $justificativo = Justificativo::findOrFail($id);
        $user = Auth::user();
        
        $justificativo->estado = 'rechazado';
        $justificativo->revisado_by = $user->usuario_id;
        $justificativo->revisado_at = now();
        $justificativo->save();
        
        return redirect()->back()->with('info', 'Justificativo rechazado.');
    }

    private function notificarAdelanto($turno, $justificativo)
    {
        $siguientePosicion = $turno->posicion + 1;
        
        $turnoSiguiente = Turno::where('punto_id', $turno->punto_id)
            ->where('fecha', $turno->fecha)
            ->where('posicion', $siguientePosicion)
            ->first();
        
        if ($turnoSiguiente) {
            $mensaje = "Turno #{$turno->posicion} ({$turno->usuario->nombres}) presentó justificación y será Adelantado. ";
            $mensaje .= "Por favor presente a la brevedad posible. ";
            $mensaje .= "Motivo: {$justificativo->motivo}";
            
            Notificacion::create([
                'usuario_id' => $turnoSiguiente->usuario_id,
                'titulo' => 'Adelanto de Turno',
                'mensaje' => $mensaje,
                'tipo' => 'info',
                'registrado_por' => Auth::user()->usuario_id,
            ]);
        }
        
        $mensajeGerente = "Justificativo aprobado para turno #{$turno->posicion}. ";
        $mensajeGerente .= "Conductor: {$turno->usuario->nombres}. ";
        $mensajeGerente .= "Tipo: {$justificativo->tipo}. ";
        $mensajeGerente .= "Motivo: {$justificativo->motivo}";
        
        $gerentes = DB::table('usuarios')
            ->where('rol_id', 2)
            ->where('activo', 1)
            ->pluck('usuario_id');
        
        foreach ($gerentes as $gerenteId) {
            Notificacion::create([
                'usuario_id' => $gerenteId,
                'titulo' => 'Justificativo Aprobado',
                'mensaje' => $mensajeGerente,
                'tipo' => 'info',
                'registrado_por' => Auth::user()->usuario_id,
            ]);
        }
    }
}