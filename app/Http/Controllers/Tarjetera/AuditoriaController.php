<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\HistorialCambio;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $puntoId = auth()->user()->punto_id;
        $puntoNombre = $puntoId == 1 ? 'Guaranda' : 'Salinas';

        $query = HistorialCambio::with('usuario')
            ->where('punto_id', $puntoId);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('tabla_afectada', 'like', "%{$buscar}%")
                    ->orWhere('accion', 'like', "%{$buscar}%")
                    ->orWhere('campo_modificado', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', function ($uq) use ($buscar) {
                        $uq->where('nombres', 'like', "%{$buscar}%")
                            ->orWhere('apellidos', 'like', "%{$buscar}%");
                    });
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('timestamp', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('timestamp', '<=', $request->fecha_hasta);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('tabla')) {
            $query->where('tabla_afectada', $request->tabla);
        }

        $auditorias = $query->orderByDesc('timestamp')->paginate(25);
        $usuarios = User::where('punto_id', $puntoId)->orderBy('nombres')->get();

        $tablas = [
            'turnos_diarios' => 'Turnos',
            'grupos_descanso' => 'Grupos Descanso',
            'ciclos_anuales' => 'Ciclos',
            'carreras_especiales' => 'Carreras/Salidas GPS',
            'multas' => 'Multas',
            'turnos_config' => 'Config. Turnos',
            'usuarios' => 'Conductores',
            'unidades' => 'Unidades',
        ];

        $acciones = [
            'crear' => 'Crear',
            'modificar' => 'Modificar',
            'eliminar' => 'Eliminar',
            'generar' => 'Generar',
        ];

        return view('tarjetera.auditoria.index', compact('auditorias', 'usuarios', 'tablas', 'acciones', 'puntoNombre', 'puntoId'));
    }

    public function exportar(Request $request)
    {
        $puntoId = auth()->user()->punto_id;

        $query = HistorialCambio::with('usuario')
            ->where('punto_id', $puntoId);

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

        $auditorias = $query->orderByDesc('timestamp')->get();

        $csv = fopen('php://temp', 'r+');
        fprintf($csv, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($csv, ['Fecha', 'Hora', 'Usuario', 'Tabla', 'Acción', 'Campo', 'Valor Anterior', 'Valor Nuevo', 'Descripción']);

        foreach ($auditorias as $a) {
            fputcsv($csv, [
                $a->timestamp->format('d/m/Y'),
                $a->timestamp->format('H:i:s'),
                $a->usuario ? $a->usuario->nombres.' '.$a->usuario->apellidos : 'Sistema',
                $a->tabla_afectada,
                $a->accion,
                $a->campo_modificado ?? '-',
                $a->valor_anterior ?? '-',
                $a->valor_nuevo ?? '-',
                $a->descripcion ?? '-',
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        $fecha = now()->format('Y-m-d_His');
        $nombre = "auditoria_{$puntoId}_{$fecha}.csv";

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$nombre}\"",
        ]);
    }
}