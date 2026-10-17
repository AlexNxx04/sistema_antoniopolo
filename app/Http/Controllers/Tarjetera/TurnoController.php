<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TurnoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $turnosHoy = Turno::with(['usuario', 'unidad'])
            ->where('punto_id', $puntoId)
            ->whereDate('fecha', now()->toDateString())
            ->orderBy('posicion')
            ->get();
        
        $turnosOperativos = $turnosHoy->where('es_descanso', 0);
        $turnosDescanso = $turnosHoy->where('es_descanso', 1);
        
        $carrerasHoy = DB::table('carreras_especiales as ce')
            ->join('turnos_diarios as t', 'ce.turno_id', '=', 't.turno_id')
            ->leftJoin('usuarios as u', 't.usuario_id', '=', 'u.usuario_id')
            ->leftJoin('unidades as un', 't.unidad_id', '=', 'un.unidad_id')
            ->where('t.punto_id', $puntoId)
            ->whereDate('ce.hora_salida_especial', now()->toDateString())
            ->select('ce.*', 'u.nombres', 'u.apellidos', 't.posicion', 'un.placa', 'un.numero_interno')
            ->orderByDesc('ce.hora_salida_especial')
            ->get();
        
        $carrerasRealizadas = $carrerasHoy->count();
        
        return view('tarjetera.turnos.index', compact('turnosOperativos', 'turnosDescanso', 'turnosHoy', 'carrerasRealizadas', 'carrerasHoy'));
    }
    
    public function llamar(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);
        $user = Auth::user();
        $ahora = now();
        
        //GUARDAR estado original antes de cambiar
        $estadoOriginal = $turno->estado;
        
        if ($turno->es_descanso) {
            return redirect()->back()->with('error', 'No se puede llamar a un conductor en descanso.');
        }
        
        if ($turno->estado === 'confirmado' || $turno->estado === 'bloqueado' || $turno->estado === 'no_presento') {
            return redirect()->back()->with('error', 'Este turno ya está confirmado o bloqueado.');
        }
        
        if ($turno->estado === 'llamado_2') {
            return redirect()->back()->with('error', 'Ya se han realizado los dos llamados. Debe bloquear.');
        }
        
        if ($turno->estado === 'llamado_1') {
            $turno->llamado_2_at = $ahora;
            $turno->estado = 'llamado_2';
            $mensaje = 'SEGUNDO LLAMADO: Última oportunidad. Preséntese inmediatamente o su turno será bloqueado.';
        } else {
            $turno->llamado_1_at = $ahora;
            $turno->estado = 'llamado_1';
            $mensaje = 'PRIMER LLAMADO: Por favor comunicarse al punto de operación ASAP.';
        }
        
        $turno->save();
        
        // Usar el estado ANTERIOR para mostrar el mensaje correcto
        $tipoAnterior = ($estadoOriginal === 'llamado_1') ? '2do Llamado' : '1ro Llamado';
        return redirect()->back()->with('success', $tipoAnterior . ' enviado a ' . $turno->usuario->nombres);
    }
    
    public function bloquear(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);
        $user = Auth::user();
        
        if ($turno->es_descanso) {
            return redirect()->back()->with('error', 'No se puede bloquear un conductor en descanso.');
        }
        
        if ($turno->estado === 'confirmado') {
            return redirect()->back()->with('error', 'Este turno ya está confirmado.');
        }
        
        // Marcar como no presentarse
        $turno->estado = 'no_presento';
        $turno->bloqueado = 1;
        $turno->motivo_bloqueo = $request->input('motivo', 'No se presentó después de 2 llamados');
        $turno->bloqueado_por = $user->usuario_id;
        $turno->bloqueado_en = now();
        $turno->save();
        
        // Notificar al conductor
        Notificacion::create([
            'usuario_id' => $turno->usuario_id,
            'tipo' => 'bloqueo',
            'mensaje' => 'BLOQUEO: No se presentó al turno de hoy. Queda sin operar.',
            'enviado_por' => $user->usuario_id,
            'enviado_at' => now(),
            'leido' => false,
        ]);
        
        return redirect()->back()->with('success', 'Turno bloqueado. El conductor ha sido notificado.');
    }
    
    public function liberar(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);
        
        if ($turno->estado !== 'no_presento') {
            return redirect()->back()->with('error', 'Solo se pueden liberar turnos bloqueados.');
        }
        
        // Liberar el turno
        $turno->estado = 'espera';
        $turno->bloqueado = 0;
        $turno->motivo_bloqueo = null;
        $turno->bloqueado_por = null;
        $turno->bloqueado_en = null;
        $turno->save();
        
        return redirect()->back()->with('success', 'Turno liberado.');
    }
    
    public function sancionar(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);
        $user = Auth::user();
        $ahora = now();
        
        if ($turno->es_descanso) {
            return redirect()->back()->with('error', 'No se puede sancionar a un conductor en descanso.');
        }
        
        if ($turno->es_puntero) {
            // PUNTERO: Si llega tarde (más de 30 min), sanción - trabaja hasta las 9am y se retira
            // Si llega dentro de 30 min, se confirma normal
            $turno->sancion_9am = 1;
            $turno->sancion_por = 'Llegó tarde. Debe retirarse a las 9am.';
            $turno->confirmado_por = $user->usuario_id;
            $turno->confirmado_en = $ahora;
            $turno->estado = 'confirmado';
            $mensaje = 'SANCIÓN: Puntero llegó tarde. Debe trabajar hasta las 9am y retirarse.';
        } else {
            // SEGUIDOR: Si llega tarde, pierde todo el día (bloqueado)
            $turno->estado = 'no_presento';
            $turno->bloqueado = 1;
            $turno->motivo_bloqueo = 'Llegó tarde sin justificación';
            $turno->bloqueado_por = $user->usuario_id;
            $turno->bloqueado_en = $ahora;
            $mensaje = 'BLOQUEO: Conductor llegó tarde. Pierde el día de trabajo.';
        }
        
        $turno->save();
        
        // Sí se notifica al conductor
        Notificacion::create([
            'usuario_id' => $turno->usuario_id,
            'tipo' => 'sancion',
            'mensaje' => $mensaje,
            'enviado_por' => $user->usuario_id,
            'enviado_at' => $ahora,
            'leido' => false,
        ]);
        
        return redirect()->back()->with('success', $mensaje);
    }
    
    public function confirmarPresencia(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);
        $user = Auth::user();
        
        $turno->confirmado_por = $user->usuario_id;
        $turno->confirmado_en = now();
        $turno->estado = 'confirmado';
        $turno->save();
        
        // Notificar al conductor
        Notificacion::create([
            'usuario_id' => $turno->usuario_id,
            'tipo' => 'confirmacion',
            'mensaje' => 'Su presencia ha sido confirmada.Gracias por comunicarse.',
            'enviado_por' => $user->usuario_id,
            'enviado_at' => now(),
            'leido' => false,
        ]);
        
        return redirect()->back()->with('success', 'Presencia confirmada.');
    }
    
    public function historial()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $historial = DB::table('historial_cambios')
            ->where('tabla', 'turnos_diarios')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
        
        return view('tarjetera.historial.index', compact('historial'));
    }
}