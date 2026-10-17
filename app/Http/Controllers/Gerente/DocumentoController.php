<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\HistorialCambio;
use App\Models\Notificacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Documento::with('usuario');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('usuario', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('cedula', 'like', "%{$buscar}%");
            })->orWhere('numero', 'like', "%{$buscar}%");
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('vencimiento')) {
            $fechaLimite = Carbon::today()->addDays((int) $request->vencimiento)->toDateString();
            $query->where('fecha_vencimiento', '<=', $fechaLimite);
        }

        $documentos = $query->orderByDesc('fecha_vencimiento')->paginate(15);

        $conductores = User::where('rol_id', 3)->where('activo', true)->orderBy('nombres')->get();

        $alertas = $this->obtenerAlertas();

        return view('gerente.documentos.index', compact('documentos', 'conductores', 'alertas'));
    }

    public function create()
    {
        $conductores = User::where('rol_id', 3)->where('activo', true)->orderBy('nombres')->get();

        return view('gerente.documentos.create', compact('conductores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => ['required', 'exists:usuarios,usuario_id'],
            'tipo' => ['required', Rule::in(['licencia', 'matricula'])],
            'numero' => ['nullable', 'string', 'max:50'],
            'fecha_emision' => ['nullable', 'date'],
            'fecha_vencimiento' => ['required', 'date'],
        ]);

        $fechaVencimiento = Carbon::parse($validated['fecha_vencimiento']);
        $estado = $fechaVencimiento->isPast() ? 'vencido' : 'vigente';

        $documento = Documento::create([
            'usuario_id' => $validated['usuario_id'],
            'tipo' => $validated['tipo'],
            'numero' => $validated['numero'] ?? null,
            'fecha_emision' => $validated['fecha_emision'] ?? null,
            'fecha_vencimiento' => $validated['fecha_vencimiento'],
            'estado' => $estado,
            'updated_at' => now(),
            'updated_by' => Auth::user()->usuario_id,
        ]);

        $usuario = User::find($validated['usuario_id']);

        if ($estado === 'vencido') {
            $this->crearNotificacionVencimiento($documento, $usuario);
        }

        HistorialCambio::registrar(
            'documentos',
            $documento->documento_id,
            'crear',
            Auth::user()->usuario_id,
            null,
            null,
            "Documento {$documento->tipo_nombre} #{$documento->numero} - {$usuario->nombres}",
            $request->ip()
        );

        return redirect()->route('gerente.documentos.index')
            ->with('success', 'Documento registrado exitosamente.');
    }

    public function show($id)
    {
        $documento = Documento::with(['usuario', 'editor'])->findOrFail($id);

        return view('gerente.documentos.show', compact('documento'));
    }

    public function edit($id)
    {
        $documento = Documento::with('usuario')->findOrFail($id);
        $conductores = User::where('rol_id', 3)->where('activo', true)->orderBy('nombres')->get();

        return view('gerente.documentos.edit', compact('documento', 'conductores'));
    }

    public function update(Request $request, $id)
    {
        $documento = Documento::findOrFail($id);

        $validated = $request->validate([
            'usuario_id' => ['required', 'exists:usuarios,usuario_id'],
            'tipo' => ['required', Rule::in(['licencia', 'matricula'])],
            'numero' => ['nullable', 'string', 'max:50'],
            'fecha_emision' => ['nullable', 'date'],
            'fecha_vencimiento' => ['required', 'date'],
        ]);

        $fechaVencimiento = Carbon::parse($validated['fecha_vencimiento']);
        $estadoAnterior = $documento->estado;
        $estado = $fechaVencimiento->isPast() ? 'vencido' : 'vigente';

        $documento->update([
            'usuario_id' => $validated['usuario_id'],
            'tipo' => $validated['tipo'],
            'numero' => $validated['numero'] ?? null,
            'fecha_emision' => $validated['fecha_emision'] ?? null,
            'fecha_vencimiento' => $validated['fecha_vencimiento'],
            'estado' => $estado,
            'updated_at' => now(),
            'updated_by' => Auth::user()->usuario_id,
        ]);

        $usuario = User::find($validated['usuario_id']);

        if ($estado === 'vencido' && $estadoAnterior !== 'vencido') {
            $this->crearNotificacionVencimiento($documento, $usuario);
        }

        HistorialCambio::registrar(
            'documentos',
            $documento->documento_id,
            'modificar',
            Auth::user()->usuario_id,
            'estado',
            $estadoAnterior,
            $estado,
            $request->ip()
        );

        return redirect()->route('gerente.documentos.index')
            ->with('success', 'Documento actualizado exitosamente.');
    }

    public function destroy(Request $request, $id)
    {
        $documento = Documento::findOrFail($id);
        $usuario = $documento->usuario;

        HistorialCambio::registrar(
            'documentos',
            $documento->documento_id,
            'eliminar',
            Auth::user()->usuario_id,
            null,
            "Documento {$documento->tipo_nombre} #{$documento->numero} - {$usuario->nombres}",
            null,
            $request->ip()
        );

        $documento->delete();

        return redirect()->route('gerente.documentos.index')
            ->with('success', 'Documento eliminado exitosamente.');
    }

    public function verificarVencimientos()
    {
        $resultado = Documento::verificarVencimientos();

        $mensaje = 'Se encontraron '.count($resultado['vencidos']).' documentos vencidos y '.count($resultado['porVencer']).' por vencer.';

        return redirect()->route('gerente.documentos.index')
            ->with('success', $mensaje);
    }

    public function exportarVencidos()
    {
        $documentos = Documento::with('usuario')
            ->where('estado', 'vencido')
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('gerente.documentos.vencidos', compact('documentos'));
    }

    private function crearNotificacionVencimiento(Documento $documento, User $usuario)
    {
        $tipoDoc = $documento->tipo_nombre;
        $nombre = $usuario->nombres.' '.$usuario->apellidos;

        Notificacion::create([
            'usuario_id' => $usuario->usuario_id,
            'tipo' => 'documento_vencido',
            'mensaje' => "Su {$tipoDoc} ha vencido. No podrá operar hasta regularizar su documentación.",
            'enviado_at' => now(),
            'leido' => 0,
            'referencia_id' => $documento->documento_id,
            'referencia_tabla' => 'documentos',
        ]);
    }

    private function obtenerAlertas(): array
    {
        $vencidos = Documento::where('estado', 'vencido')->count();
        $porVencer = Documento::where('fecha_vencimiento', '>=', now()->toDateString())
            ->where('fecha_vencimiento', '<=', now()->addDays(30)->toDateString())
            ->where('estado', 'vigente')
            ->count();

        return [
            'vencidos' => $vencidos,
            'porVencer' => $porVencer,
        ];
    }
}
