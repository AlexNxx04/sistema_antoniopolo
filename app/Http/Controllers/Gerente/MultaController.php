<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\HistorialCambio;
use App\Models\Multa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MultaController extends Controller
{
    public function index(Request $request)
    {
        $usuarioId = $request->filled('usuario_id') ? $request->usuario_id : null;
        $estado = $request->filled('estado') ? $request->estado : null;
        $fechaDesde = $request->filled('fecha_desde') ? Carbon::parse($request->fecha_desde) : null;
        $fechaHasta = $request->filled('fecha_hasta') ? Carbon::parse($request->fecha_hasta) : null;

        $query = Multa::with(['usuario', 'registrador', 'turno']);

        if ($usuarioId) {
            $query->where('usuario_id', $usuarioId);
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($fechaDesde) {
            $query->whereDate('registrado_en', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('registrado_en', '<=', $fechaHasta);
        }

        $multas = $query->orderBy('registrado_en', 'desc')->paginate(20);
        $conductores = User::where('rol_id', 3)->where('activo', 1)->orderBy('nombres')->get();

        $estadisticas = [
            'pendientes' => Multa::where('estado', 'pendiente')->count(),
            'pendientes_semana' => Multa::where('estado', 'pendiente_semana')->count(),
            'cobradas' => Multa::where('estado', 'cobrada')->sum('monto'),
            'total' => Multa::count(),
        ];

        return view('gerente.multas.index', compact(
            'multas', 'conductores', 'estadisticas',
            'usuarioId', 'estado', 'fechaDesde', 'fechaHasta'
        ));
    }

    public function show($id)
    {
        $multa = Multa::with(['usuario', 'turno', 'salida', 'llegada', 'registrador', 'cobrador'])->findOrFail($id);

        return view('gerente.multas.show', compact('multa'));
    }

    public function create(Request $request)
    {
        $conductores = User::where('rol_id', 3)->where('activo', 1)->orderBy('nombres')->get();
        $turnoId = $request->filled('turno_id') ? $request->turno_id : null;

        return view('gerente.multas.create', compact('conductores', 'turnoId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,usuario_id',
            'tipo' => 'required|in:retraso_llegada,ausencia,documento_vencido,indisciplina',
            'monto' => 'required|numeric|min:0',
            'minutos_retraso' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string|max:500',
            'turno_id' => 'nullable|exists:turnos_diarios,turno_id',
        ]);

        DB::beginTransaction();
        try {
            $gerente = Auth::user();

            $multa = Multa::create([
                'usuario_id' => $validated['usuario_id'],
                'turno_id' => $validated['turno_id'] ?? null,
                'tipo' => $validated['tipo'],
                'monto' => $validated['monto'],
                'minutos_retraso' => $validated['minutos_retraso'] ?? null,
                'descripcion' => $validated['descripcion'] ?? null,
                'estado' => 'pendiente',
                'registrado_por' => $gerente->usuario_id,
                'registrado_en' => now(),
            ]);

            HistorialCambio::registrar(
                'multas',
                $multa->multa_id,
                'crear',
                $gerente->usuario_id,
                'multa',
                null,
                "Tipo: {$validated['tipo']}, Monto: \${$validated['monto']}",
                $request->ip()
            );

            DB::commit();

            return redirect()->route('gerente.multas.show', $multa->multa_id)
                ->with('success', 'Multa registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Error: '.$e->getMessage()]);
        }
    }

    public function marcarCobrada(Request $request, $id)
    {
        $multa = Multa::findOrFail($id);

        if ($multa->estado !== 'pendiente') {
            return redirect()->back()->withErrors(['error' => 'Esta multa ya fue procesada.']);
        }

        $gerente = Auth::user();

        $multa->update([
            'estado' => 'cobrada',
            'cobrado_por' => $gerente->usuario_id,
            'cobrado_en' => now(),
        ]);

        HistorialCambio::registrar(
            'multas',
            $multa->multa_id,
            'modificar',
            $gerente->usuario_id,
            'estado',
            'pendiente',
            'cobrada',
            $request->ip()
        );

        return redirect()->back()->with('success', 'Multa marcada como cobrada.');
    }

    public function marcarAnulada(Request $request, $id)
    {
        $multa = Multa::findOrFail($id);

        if ($multa->estado !== 'pendiente') {
            return redirect()->back()->withErrors(['error' => 'Esta multa ya fue procesada.']);
        }

        $validated = $request->validate([
            'motivo_anulacion' => 'required|string|max:255',
        ]);

        $gerente = Auth::user();

        $multa->update([
            'estado' => 'anulada',
            'descripcion' => ($multa->descripcion ?? '')."\n[ANULADA: {$validated['motivo_anulacion']}]",
        ]);

        HistorialCambio::registrar(
            'multas',
            $multa->multa_id,
            'modificar',
            $gerente->usuario_id,
            'estado',
            'pendiente',
            'anulada',
            $request->ip()
        );

        return redirect()->back()->with('success', 'Multa anulada.');
    }

    public function resumen(Request $request)
    {
        $anio = $request->filled('anio') ? $request->anio : now()->year;

        $conductores = User::where('rol_id', 3)
            ->where('activo', 1)
            ->with(['multas' => function ($q) use ($anio) {
                $q->whereYear('registrado_en', $anio);
            }])
            ->get();

        $estadisticasAnuales = [];
        foreach ($conductores as $conductor) {
            $multasPendientes = $conductor->multas->where('estado', 'pendiente')->sum('monto');
            $multasCobradas = $conductor->multas->where('estado', 'cobrada')->sum('monto');
            $totalMultas = $conductor->multas->count();

            $estadisticasAnuales[] = [
                'conductor' => $conductor,
                'pendientes' => $multasPendientes,
                'cobradas' => $multasCobradas,
                'total' => $totalMultas,
            ];
        }

        usort($estadisticasAnuales, fn ($a, $b) => $b['cobradas'] <=> $a['cobradas']);

        $totales = [
            'pendientes' => array_sum(array_column($estadisticasAnuales, 'pendientes')),
            'cobradas' => array_sum(array_column($estadisticasAnuales, 'cobradas')),
            'total' => array_sum(array_column($estadisticasAnuales, 'total')),
        ];

        return view('gerente.multas.resumen', compact('estadisticasAnuales', 'totales', 'anio'));
    }
}
