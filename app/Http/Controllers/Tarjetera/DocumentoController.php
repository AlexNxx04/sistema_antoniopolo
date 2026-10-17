<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DocumentoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $documentos = Documento::with(['usuario', 'usuario.unidad'])
            ->whereHas('usuario', function($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
            ->orderBy('fecha_vencimiento')
            ->paginate(30);
        
        $proximos = Documento::with(['usuario', 'usuario.unidad'])
            ->whereHas('usuario', function($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
            ->whereDate('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->where('estado', '!=', 'vencido')
            ->orderBy('fecha_vencimiento')
            ->get();
        
        $vencidos = Documento::with(['usuario', 'usuario.unidad'])
            ->whereHas('usuario', function($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
            ->whereDate('fecha_vencimiento', '<', Carbon::now()->toDateString())
            ->get();
        
        return view('tarjetera.documentos.index', compact('documentos', 'proximos', 'vencidos'));
    }
    
    public function vehicular()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $conductores = User::with(['documentos', 'unidad'])
            ->where('punto_id', $puntoId)
            ->where('rol_id', 3)
            ->where('activo', 1)
            ->orderBy('nombres')
            ->get();
        
        return view('tarjetera.documentos.vehicular', compact('conductores'));
    }
    
    public function validar(Request $request, $id)
    {
        $documento = Documento::findOrFail($id);
        
        $documento->estado = 'vigente';
        $documento->updated_at = now();
        $documento->save();
        
        return redirect()->back()->with('success', 'Documento validado correctamente.');
    }
}