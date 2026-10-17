<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\CicloAnual;
use App\Models\Documento;
use App\Models\GrupoDescanso;
use App\Models\GrupoMiembro;
use App\Models\HistorialCambio;
use App\Models\LlegadaGps;
use App\Models\Multa;
use App\Models\Notificacion;
use App\Models\PosicionGps;
use App\Models\Punto;
use App\Models\Rol;
use App\Models\Salida;
use App\Models\Turno;
use App\Models\TurnoConfig;
use App\Models\Unidad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VerificacionController extends Controller
{
    public function index()
    {
        $resultados = [];

        // 1. Verificar Roles
        $resultados['roles'] = $this->verificarRoles();

        // 2. Verificar Puntos
        $resultados['puntos'] = $this->verificarPuntos();

        // 3. Verificar Usuarios
        $resultados['usuarios'] = $this->verificarUsuarios();

        // 4. Verificar Unidades
        $resultados['unidades'] = $this->verificarUnidades();

        // 5. Verificar Ciclos
        $resultados['ciclos'] = $this->verificarCiclos();

        // 6. Verificar Turnos
        $resultados['turnos'] = $this->verificarTurnos();

        // 7. Verificar Documentos
        $resultados['documentos'] = $this->verificarDocumentos();

        // 8. Verificar Multas
        $resultados['multas'] = $this->verificarMultas();

        // 9. Verificar Salidas y GPS
        $resultados['salidas'] = $this->verificarSalidas();

        // 10. Verificar Notificaciones
        $resultados['notificaciones'] = $this->verificarNotificaciones();

        // 11. Verificar Historial
        $resultados['historial'] = $this->verificarHistorial();

        // 12. Verificar Parámetros
        $resultados['parametros'] = $this->verificarParametros();

        // 13. Verificar Modelos y Relaciones
        $resultados['modelos'] = $this->verificarModelos();

        return view('gerente.verificacion.index', compact('resultados'));
    }

    private function verificarRoles()
    {
        try {
            $roles = Rol::all();

            return [
                'estado' => 'ok',
                'mensaje' => "{$roles->count()} roles encontrados",
                'datos' => $roles->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar roles: '.$e->getMessage(),
            ];
        }
    }

    private function verificarPuntos()
    {
        try {
            $puntos = Punto::where('activo', 1)->get();

            return [
                'estado' => 'ok',
                'mensaje' => "{$puntos->count()} puntos activos",
                'datos' => $puntos->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar puntos: '.$e->getMessage(),
            ];
        }
    }

    private function verificarUsuarios()
    {
        try {
            $total = User::count();
            $activos = User::where('activo', 1)->count();
            $conductores = User::where('rol_id', 3)->where('activo', 1)->count();

            $usuarios = User::with('rol')->orderBy('usuario_id', 'desc')->limit(5)->get();

            return [
                'estado' => 'ok',
                'mensaje' => "{$total} usuarios ({$activos} activos, {$conductores} conductores)",
                'datos' => $usuarios->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar usuarios: '.$e->getMessage(),
            ];
        }
    }

    private function verificarUnidades()
    {
        try {
            $total = Unidad::count();
            $activas = Unidad::where('estado', 'activo')->count();
            $mantenimiento = Unidad::where('estado', 'mantenimiento')->count();

            $unidades = Unidad::with('punto')->orderBy('numero_interno')->limit(5)->get();

            return [
                'estado' => 'ok',
                'mensaje' => "{$total} unidades ({$activas} activas, {$mantenimiento} en mantenimiento)",
                'datos' => $unidades->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar unidades: '.$e->getMessage(),
            ];
        }
    }

    private function verificarCiclos()
    {
        try {
            $total = CicloAnual::count();
            $activo = CicloAnual::getCicloActivo();

            return [
                'estado' => 'ok',
                'mensaje' => $activo
                    ? "Ciclo activo: {$activo->anio}"
                    : "No hay ciclo activo ({$total} ciclos registrados)",
                'datos' => $activo ? [$activo->toArray()] : [],
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar ciclos: '.$e->getMessage(),
            ];
        }
    }

    private function verificarTurnos()
    {
        try {
            $hoy = Carbon::today();
            $cicloActivo = CicloAnual::getCicloActivo();

            if (! $cicloActivo) {
                return [
                    'estado' => 'advertencia',
                    'mensaje' => 'No hay ciclo activo para verificar turnos',
                    'datos' => [],
                ];
            }

            $total = Turno::where('ciclo_id', $cicloActivo->ciclo_id)
                ->whereDate('fecha', $hoy)
                ->count();

            $operando = Turno::where('ciclo_id', $cicloActivo->ciclo_id)
                ->whereDate('fecha', $hoy)
                ->where('es_descanso', 0)
                ->where('es_puntero', 0)
                ->count();

            $descanso = Turno::where('ciclo_id', $cicloActivo->ciclo_id)
                ->whereDate('fecha', $hoy)
                ->where('es_descanso', 1)
                ->count();

            $turnos = Turno::with(['usuario', 'unidad', 'punto'])
                ->where('ciclo_id', $cicloActivo->ciclo_id)
                ->whereDate('fecha', $hoy)
                ->orderBy('posicion')
                ->limit(5)
                ->get();

            return [
                'estado' => 'ok',
                'mensaje' => "Turnos de hoy: {$total} ({$operando} operando, {$descanso} descanso)",
                'datos' => $turnos->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar turnos: '.$e->getMessage(),
            ];
        }
    }

    private function verificarDocumentos()
    {
        try {
            $total = Documento::count();
            $vigentes = Documento::where('estado', 'vigente')->count();
            $vencidos = Documento::where('estado', 'vencido')->count();

            $porVencer = Documento::where('estado', 'vigente')
                ->whereBetween('fecha_vencimiento', [Carbon::today(), Carbon::today()->addDays(30)])
                ->count();

            $documentos = Documento::with('usuario')
                ->orderBy('fecha_vencimiento', 'desc')
                ->limit(5)
                ->get();

            return [
                'estado' => 'ok',
                'mensaje' => "{$total} documentos ({$vigentes} vigentes, {$vencidos} vencidos, {$porVencer} por vencer)",
                'datos' => $documentos->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar documentos: '.$e->getMessage(),
            ];
        }
    }

    private function verificarMultas()
    {
        try {
            $total = Multa::count();
            $pendientes = Multa::where('estado', 'pendiente')->count();
            $cobradas = Multa::where('estado', 'cobrada')->count();
            $anuladas = Multa::where('estado', 'anulada')->count();

            $totalMonto = Multa::sum('monto');
            $cobradoMonto = Multa::where('estado', 'cobrada')->sum('monto');

            $multas = Multa::with('usuario')
                ->orderBy('registrado_en', 'desc')
                ->limit(5)
                ->get();

            return [
                'estado' => 'ok',
                'mensaje' => "{$total} multas (Pendientes: {$pendientes}, Cobradas: {$cobradas}, Anuladas: {$anuladas}). Total: \${$totalMonto}, Cobrado: \${$cobradoMonto}",
                'datos' => $multas->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar multas: '.$e->getMessage(),
            ];
        }
    }

    private function verificarSalidas()
    {
        try {
            $hoy = Carbon::today();
            $total = Salida::whereDate('hora_salida', $hoy)->count();
            $conLlegada = Salida::whereDate('hora_salida', $hoy)
                ->whereHas('llegada')
                ->count();

            $salidas = Salida::with(['turno.usuario', 'turno.unidad', 'llegada'])
                ->whereDate('hora_salida', $hoy)
                ->orderBy('hora_salida', 'desc')
                ->limit(5)
                ->get();

            return [
                'estado' => 'ok',
                'mensaje' => "{$total} salidas hoy ({$conLlegada} completadas)",
                'datos' => $salidas->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar salidas: '.$e->getMessage(),
            ];
        }
    }

    private function verificarNotificaciones()
    {
        try {
            $total = Notificacion::count();
            $noLeidas = Notificacion::where('leido', 0)->count();

            $notificaciones = Notificacion::with('usuario')
                ->orderBy('enviado_at', 'desc')
                ->limit(5)
                ->get();

            return [
                'estado' => 'ok',
                'mensaje' => "{$total} notificaciones ({$noLeidas} no leídas)",
                'datos' => $notificaciones->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar notificaciones: '.$e->getMessage(),
            ];
        }
    }

    private function verificarHistorial()
    {
        try {
            $total = HistorialCambio::count();

            $cambios = HistorialCambio::with('usuario')
                ->orderBy('timestamp', 'desc')
                ->limit(5)
                ->get();

            return [
                'estado' => 'ok',
                'mensaje' => "{$total} registros en historial de cambios",
                'datos' => $cambios->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar historial: '.$e->getMessage(),
            ];
        }
    }

    private function verificarParametros()
    {
        try {
            $parametros = DB::table('parametros_operativos')->get();

            return [
                'estado' => 'ok',
                'mensaje' => "{$parametros->count()} parámetros operativos",
                'datos' => $parametros->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al consultar parámetros: '.$e->getMessage(),
            ];
        }
    }

    private function verificarModelos()
    {
        try {
            $resultado = [];

            // Verificar GrupoDescanso
            $resultado['grupo_descanso'] = [
                'existe' => class_exists(GrupoDescanso::class),
                'tabla' => (new GrupoDescanso)->getTable(),
            ];

            // Verificar GrupoMiembro
            $resultado['grupo_miembro'] = [
                'existe' => class_exists(GrupoMiembro::class),
                'tabla' => (new GrupoMiembro)->getTable(),
            ];

            // Verificar TurnoConfig
            $resultado['turno_config'] = [
                'existe' => class_exists(TurnoConfig::class),
                'tabla' => (new TurnoConfig)->getTable(),
            ];

            // Verificar LlegadaGps
            $resultado['llegada_gps'] = [
                'existe' => class_exists(LlegadaGps::class),
                'tabla' => (new LlegadaGps)->getTable(),
            ];

            // Verificar PosicionGps
            $resultado['posicion_gps'] = [
                'existe' => class_exists(PosicionGps::class),
                'tabla' => (new PosicionGps)->getTable(),
            ];

            return [
                'estado' => 'ok',
                'mensaje' => 'Modelos verificados',
                'datos' => $resultado,
            ];
        } catch (\Exception $e) {
            return [
                'estado' => 'error',
                'mensaje' => 'Error al verificar modelos: '.$e->getMessage(),
            ];
        }
    }
}
