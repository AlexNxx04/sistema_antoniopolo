<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConductorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $puntoId = $user->punto_id;
        
        $conductores = User::with('unidad')
            ->where('punto_id', $puntoId)
            ->where('rol_id', 3)
            ->where('activo', 1)
            ->orderBy('nombres')
            ->get();
        
        return view('tarjetera.conductores.index', compact('conductores'));
    }
}