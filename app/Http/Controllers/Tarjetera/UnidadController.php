<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnidadController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $unidades = Unidad::with('punto')
            ->where('punto_id', $puntoId)
            ->where('estado', 'activo')
            ->orderBy('numero_interno')
            ->get();
        
        return view('tarjetera.unidades.index', compact('unidades'));
    }
}