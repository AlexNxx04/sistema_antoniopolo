<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('tarjetera.perfil.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'email', 'max:150', 'unique:usuarios,correo,'.$user->usuario_id.',usuario_id'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        DB::table('usuarios')
            ->where('usuario_id', $user->usuario_id)
            ->update([
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'correo' => $validated['correo'],
                'telefono' => $validated['telefono'] ?? null,
                'updated_at' => now(),
            ]);

        return redirect()->route('tarjetera.perfil.index')->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required', 
                'string', 
                'min:8',
                'confirmed',
            ],
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $newPassword = Hash::make($request->password);

        DB::table('usuarios')
            ->where('usuario_id', $user->usuario_id)
            ->update([
                'password' => $newPassword,
                'updated_at' => now(),
            ]);

        return redirect()->route('tarjetera.perfil.index')->with('success', 'Contraseña actualizada correctamente.');
    }
}