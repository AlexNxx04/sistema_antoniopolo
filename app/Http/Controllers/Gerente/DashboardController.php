<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\CicloAnual;
use App\Models\Documento;
use App\Models\Multa;
use App\Models\Notificacion;
use App\Models\Punto;
use App\Models\Salida;
use App\Models\Turno;
use App\Models\Unidad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hoy = Carbon::today();

        $stats = [
            'total_usuarios' => User::where('activo', 1)->count(),
            'conductores' => User::where('rol_id', 3)->where('activo', 1)->count(),
            'tarjeteras' => User::where('rol_id', 2)->where('activo', 1)->count(),
            'gerentes' => User::where('rol_id', 1)->where('activo', 1)->count(),
            'usuarios_inactivos' => User::where('activo', 0)->count(),
            'unidades_activas' => Unidad::where('estado', 'activo')->count(),
            'unidades_total' => Unidad::count(),
            'unidades_mantenimiento' => Unidad::where('estado', 'mantenimiento')->count(),
        ];

        $cicloActivo = CicloAnual::getCicloActivo();

        if ($cicloActivo) {
            $turnosHoy = Turno::where('ciclo_id', $cicloActivo->ciclo_id)
                ->whereDate('fecha', $hoy);

            $turnosOperando = (clone $turnosHoy)->where('es_descanso', 0)->count();
            $turnosDescanso = (clone $turnosHoy)->where('es_descanso', 1)->count();
            $turnosBloqueados = (clone $turnosHoy)->where('bloqueado', 1)->count();
        } else {
            $turnosOperando = 0;
            $turnosDescanso = 0;
            $turnosBloqueados = 0;
        }

        $salidasHoy = Salida::whereDate('hora_salida', $hoy)->count();
        $salidasCompletadas = Salida::whereDate('hora_salida', $hoy)
            ->whereHas('llegada')
            ->count();

        $sancionesPendientes = Multa::where('estado', 'pendiente')->count();

        $totalSancionesMes = Multa::whereMonth('registrado_en', Carbon::now()->month)
            ->sum('monto');
        $totalCobradoMes = Multa::where('estado', 'cobrada')
            ->whereMonth('registrado_en', Carbon::now()->month)
            ->sum('monto');

        $documentosProximosVencer = Documento::where('estado', 'vigente')
            ->whereBetween('fecha_vencimiento', [Carbon::today(), Carbon::today()->addDays(30)])
            ->count();

        $documentosVencidos = Documento::where('estado', 'vencido')->count();

        $puntos = Punto::where('activo', 1)->get();

        $usuariosRecientes = User::with('rol')
            ->orderBy('usuario_id', 'desc')
            ->limit(6)
            ->get();

        $ultimasSanciones = Multa::with('usuario')
            ->orderBy('registrado_en', 'desc')
            ->limit(5)
            ->get();

        $notificaciones = Notificacion::where('usuario_id', $user->usuario_id)
            ->orderBy('enviado_at', 'desc')
            ->limit(10)
            ->get();
        $notificacionesNoLeidas = Notificacion::where('usuario_id', $user->usuario_id)
            ->where('leido', 0)
            ->count();

        return view('gerente.dashboard', compact(
            'user',
            'stats',
            'turnosOperando',
            'turnosDescanso',
            'turnosBloqueados',
            'salidasHoy',
            'salidasCompletadas',
            'sancionesPendientes',
            'totalSancionesMes',
            'totalCobradoMes',
            'documentosProximosVencer',
            'documentosVencidos',
            'cicloActivo',
            'puntos',
            'usuariosRecientes',
            'ultimasSanciones',
            'hoy',
            'notificaciones',
            'notificacionesNoLeidas'
        ));
    }
}
