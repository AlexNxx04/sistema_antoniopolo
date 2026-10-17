<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Notificacion::with('usuario');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('leido')) {
            $query->where('leido', $request->leido === 'si' ? 1 : 0);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('usuario', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('cedula', 'like', "%{$buscar}%");
            });
        }

        $notificaciones = $query->orderByDesc('enviado_at')->paginate(20);

        $usuarios = User::where('activo', true)->orderBy('nombres')->get();

        return view('gerente.notificaciones.index', compact('notificaciones', 'usuarios'));
    }

    public function create()
    {
        $usuarios = User::where('activo', true)->orderBy('nombres')->get();

        return view('gerente.notificaciones.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => ['required', 'exists:usuarios,usuario_id'],
            'tipo' => ['required', 'in:llamado_turno,sancion,documento_vencido,alerta_general'],
            'mensaje' => ['required', 'string', 'max:500'],
        ]);

        Notificacion::create([
            'usuario_id' => $validated['usuario_id'],
            'tipo' => $validated['tipo'],
            'mensaje' => $validated['mensaje'],
            'enviado_at' => now(),
            'leido' => false,
        ]);

        return redirect()->route('gerente.notificaciones.index')
            ->with('success', 'Notificación enviada exitosamente.');
    }

    public function marcarLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->marcarLeido();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notificación marquée como leída']);
        }

        return redirect()->back();
    }

    public function marcarTodasLeidas()
    {
        Notificacion::where('leido', 0)->update([
            'leido' => 1,
            'leido_at' => now(),
        ]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Todas las notificaciones marcadas como leídas']);
        }

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

public function destroy($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notificación eliminada']);
        }

        return redirect()->back()->with('success', 'Notificación eliminada.');
    }

    public function getNoLeidas()
    {
        return Notificacion::where('usuario_id', Auth::user()->usuario_id)
            ->where('leido', 0)
            ->count();
    }

    public function api()
    {
        $usuarioId = Auth::user()->usuario_id;

        $notificaciones = Notificacion::with('usuario')
            ->where('usuario_id', $usuarioId)
            ->orderByDesc('enviado_at')
            ->limit(5)
            ->get()
            ->map(function ($notif) {
                return [
                    'notif_id' => $notif->notif_id,
                    'tipo' => $notif->tipo,
                    'mensaje' => $notif->mensaje,
                    'leido' => (bool) $notif->leido,
                    'enviado_at' => $notif->enviado_at->toIso8601String(),
                    'enviado_at_human' => $notif->enviado_at->diffForHumans(),
                    'usuario_nombre' => $notif->usuario ? $notif->usuario->nombres.' '.$notif->usuario->apellidos : 'Sistema',
                ];
            })
            ->values()
            ->toArray();

        $noLeidas = Notificacion::where('usuario_id', $usuarioId)
            ->where('leido', 0)
            ->count();

        return response()->json([
            'notificaciones' => $notificaciones,
            'noLeidas' => $noLeidas,
        ]);
    }

    public function marcarTodasVistas()
    {
        Notificacion::where('usuario_id', Auth::user()->usuario_id)
            ->where('leido', 0)
            ->update([
                'leido' => 1,
                'leido_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }
}
