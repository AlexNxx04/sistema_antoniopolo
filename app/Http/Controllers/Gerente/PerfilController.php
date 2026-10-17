<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\HistorialCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load('punto');

        return view('gerente.perfil.index', compact('user'));
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

        $oldData = [
            'nombres' => $user->nombres,
            'apellidos' => $user->apellidos,
            'correo' => $user->correo,
            'telefono' => $user->telefono,
        ];

        DB::table('usuarios')
            ->where('usuario_id', $user->usuario_id)
            ->update([
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'correo' => $validated['correo'],
                'telefono' => $validated['telefono'] ?? null,
                'updated_at' => now(),
            ]);

        foreach ($oldData as $campo => $valorAnterior) {
            if (isset($validated[$campo]) && $valorAnterior != $validated[$campo]) {
                HistorialCambio::registrar(
                    'usuarios',
                    $user->usuario_id,
                    'modificar',
                    $user->usuario_id,
                    $campo,
                    $valorAnterior,
                    $validated[$campo],
                    $request->ip()
                );
            }
        }

        return redirect()->route('gerente.perfil.index')->with('success', 'Perfil actualizado correctamente.');
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
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un símbolo.',
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

        HistorialCambio::registrar(
            'usuarios',
            $user->usuario_id,
            'modificar',
            $user->usuario_id,
            'password',
            '***',
            '***',
            $request->ip()
        );

        return redirect()->route('gerente.perfil.index')->with('success', 'Contraseña actualizada correctamente.');
    }
}
