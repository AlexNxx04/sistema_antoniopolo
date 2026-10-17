<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\HistorialCambio;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = HistorialCambio::with('usuario');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('tabla_afectada', 'like', "%{$buscar}%")
                    ->orWhere('accion', 'like', "%{$buscar}%")
                    ->orWhere('campo_modificado', 'like', "%{$buscar}%")
                    ->orWhere('valor_anterior', 'like', "%{$buscar}%")
                    ->orWhere('valor_nuevo', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', function ($uq) use ($buscar) {
                        $uq->where('nombres', 'like', "%{$buscar}%")
                            ->orWhere('apellidos', 'like', "%{$buscar}%")
                            ->orWhere('cedula', 'like', "%{$buscar}%");
                    });
            });
        }

        if ($request->filled('tabla')) {
            $query->where('tabla_afectada', $request->tabla);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('timestamp', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('timestamp', '<=', $request->fecha_hasta);
        }

        $cambios = $query->orderByDesc('timestamp')->paginate(25);
        $usuarios = User::orderBy('nombres')->get();

        $tablas = [
            'usuarios' => 'Usuarios',
            'unidades' => 'Unidades',
            'documentos' => 'Documentos',
            'turnos_diarios' => 'Turnos Diarios',
            'turnos_config' => 'Config. Turnos',
            'ciclos_anuales' => 'Ciclos',
            'grupos_descanso' => 'Grupos Descanso',
            'multas' => 'Multas',
            'parametros_sistema' => 'Parámetros',
            'notificaciones' => 'Notificaciones',
            'salidas' => 'Salidas GPS',
            'llegadas_gps' => 'Llegadas GPS',
            'historial_cambios' => 'Auditoría',
        ];

        $acciones = [
            'crear' => 'Crear',
            'modificar' => 'Modificar',
            'eliminar' => 'Eliminar',
        ];

        return view('gerente.auditoria.index', compact('cambios', 'usuarios', 'tablas', 'acciones'));
    }

    public function show($id)
    {
        $cambio = HistorialCambio::with('usuario')->findOrFail($id);

        return view('gerente.auditoria.show', compact('cambio'));
    }

    public function exportar(Request $request)
    {
        $query = HistorialCambio::with('usuario');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('timestamp', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('timestamp', '<=', $request->fecha_hasta);
        }

        if ($request->filled('tabla')) {
            $query->where('tabla_afectada', $request->tabla);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        $cambios = $query->orderByDesc('timestamp')->get();

        $csv = fopen('php://temp', 'r+');

        fprintf($csv, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($csv, ['Fecha', 'Hora', 'Usuario', 'Cédula', 'Tabla', 'Acción', 'Campo Modificado', 'Valor Anterior', 'Valor Nuevo', 'IP']);

        foreach ($cambios as $cambio) {
            fputcsv($csv, [
                $cambio->timestamp->format('d/m/Y'),
                $cambio->timestamp->format('H:i:s'),
                $cambio->usuario ? $cambio->usuario->nombres.' '.$cambio->usuario->apellidos : 'Sistema',
                $cambio->usuario ? $cambio->usuario->cedula : 'N/A',
                $cambio->tabla_afectada,
                $cambio->accion,
                $cambio->campo_modificado ?? 'N/A',
                $cambio->valor_anterior ?? 'N/A',
                $cambio->valor_nuevo ?? 'N/A',
                $cambio->ip_origen ?? 'N/A',
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        $fecha = now()->format('Y-m-d_His');
        $nombre = "auditoria_{$fecha}.csv";

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$nombre}\"",
        ]);
    }
}
