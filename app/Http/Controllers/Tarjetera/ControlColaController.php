<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ControlColaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $turnosDia = Turno::with(['usuario', 'unidad'])
            ->where('punto_id', $puntoId)
            ->whereDate('fecha', now()->toDateString())
            ->orderBy('posicion')
            ->get();
        
        $estadisticas = [
            'total' => $turnosDia->count(),
            'confirmados' => $turnosDia->where('estado', 'confirmado')->count(),
            'en_espera' => $turnosDia->whereIn('estado', ['espera', 'llamado_1', 'llamado_2'])->count(),
            'bloqueados' => $turnosDia->where('estado', 'no_presento')->count(),
            'sancionados' => $turnosDia->where('sancion_9am', 1)->count(),
        ];
        
        $carrerasActivas = DB::table('carreras_especiales as ce')
            ->leftJoin('turnos_diarios as t', 'ce.turno_id', '=', 't.turno_id')
            ->where('t.punto_id', $puntoId)
            ->whereNull('ce.hora_regreso')
            ->select('ce.*', 't.posicion as turno_posicion')
            ->get();
        
        return view('tarjetera.control-cola.index', compact('turnosDia', 'estadisticas', 'carrerasActivas'));
    }
    
    public function bloquear(Request $request, $id)
    {
        $user = Auth::user();
        $turno = Turno::findOrFail($id);
        $tipo = $request->input('tipo', 'seguidor');
        
        if ($turno->es_descanso) {
            return redirect()->back()->with('error', 'No se puede bloquear un conductor en descanso.');
        }
        
        if ($turno->estado === 'confirmado') {
            return redirect()->back()->with('error', 'Este turno ya está confirmado.');
        }
        
        $turno->estado = 'no_presento';
        $turno->bloqueado = 1;
        $turno->motivo_bloqueo = $request->input('motivo', $tipo === 'seguidor' ? 'No respondió a los dos llamados.' : 'Bloqueado por carrera especial.');
        $turno->bloqueado_por = $user->usuario_id;
        $turno->bloqueado_en = now();
        $turno->save();
        
        Notificacion::create([
            'usuario_id' => $turno->usuario_id,
            'tipo' => 'bloqueo',
            'mensaje' => 'BLOQUEO: ' . ($tipo === 'seguidor' ? 'No respondió a los llamados.' : 'Bloqueado por carrera especial.'),
            'enviado_por' => $user->usuario_id,
            'enviado_at' => now(),
            'leido' => false,
        ]);
        
        return redirect()->back()->with('success', 'Turno bloqueado: ' . $turno->usuario->nombres);
    }
    
    public function sancionar(Request $request, $id)
    {
        $user = Auth::user();
        $turno = Turno::findOrFail($id);
        
        $turno->sancion_9am = 1;
        $turno->sancion_por = 'Sancionado: Debe retirarse a las 9am.';
        $turno->save();
        
        Notificacion::create([
            'usuario_id' => $turno->usuario_id,
            'tipo' => 'sancion',
            'mensaje' => 'SANCIÓN: Puntero sancionado. Debe trabajar hasta las 9am.',
            'enviado_por' => $user->usuario_id,
            'enviado_at' => now(),
            'leido' => false,
        ]);
        
        return redirect()->back()->with('success', 'Puntero sancionado: ' . $turno->usuario->nombres);
    }
    
    public function reubicar(Request $request, $id)
    {
        $user = Auth::user();
        $turno = Turno::findOrFail($id);
        $nuevaPosicion = $request->input('posicion', $turno->posicion);
        
        $turnosAjustar = Turno::where('punto_id', $turno->punto_id)
            ->whereDate('fecha', $turno->fecha)
            ->where('turno_id', '!=', $id)
            ->where('posicion', '>=', $nuevaPosicion)
            ->where('posicion', '<', $turno->posicion)
            ->orderBy('posicion')
            ->get();
        
        foreach ($turnosAjustar as $t) {
            $t->posicion = $t->posicion + 1;
            $t->save();
        }
        
        $turno->posicion = $nuevaPosicion;
        $turno->save();
        
        return redirect()->back()->with('success', 'Turnos reubicados correctamente.');
    }
    
    public function moverPosicion(Request $request, $id)
    {
        $user = Auth::user();
        $turno = Turno::findOrFail($id);
        $direccion = $request->input('direccion');
        
        if ($direccion === 'subir') {
            $turnoArriba = Turno::where('punto_id', $turno->punto_id)
                ->whereDate('fecha', $turno->fecha)
                ->where('posicion', $turno->posicion - 1)
                ->first();
            
            if ($turnoArriba) {
                $turnoArriba->posicion = $turno->posicion;
                $turnoArriba->save();
            }
            
            $turno->posicion = $turno->posicion - 1;
        } else {
            $turnoAbajo = Turno::where('punto_id', $turno->punto_id)
                ->whereDate('fecha', $turno->fecha)
                ->where('posicion', $turno->posicion + 1)
                ->first();
            
            if ($turnoAbajo) {
                $turnoAbajo->posicion = $turno->posicion;
                $turnoAbajo->save();
            }
            
            $turno->posicion = $turno->posicion + 1;
        }
        
        $turno->save();
        
        return redirect()->back()->with('success', 'Posición actualizada.');
    }
}