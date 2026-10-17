<?php

namespace App\Http\Controllers;

use App\Models\HistorialCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'telefono' => 'nullable|string|max:15',
            'correo' => 'nullable|email|max:150|unique:usuarios,correo,'.$user->usuario_id.',usuario_id',
            'password_actual' => 'required_with:password_nueva',
            'password_nueva' => 'nullable|min:6|confirmed',
        ], [
            'correo.unique' => 'Este correo ya está registrado por otro usuario.',
            'password_nueva.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password_nueva.confirmed' => 'La confirmación de contraseña no coincide.',
            'password_actual.required_with' => 'Debe ingresar la contraseña actual para cambiar la contraseña.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->filled('password_nueva')) {
            if (! Hash::check($request->password_actual, $user->password)) {
                return redirect()->back()
                    ->withErrors(['password_actual' => 'La contraseña actual es incorrecta.'])
                    ->withInput();
            }

            $user->password = Hash::make($request->password_nueva);

            HistorialCambio::registrar(
                'usuarios',
                $user->usuario_id,
                'modificar',
                $user->usuario_id,
                'password',
                '****',
                '****',
                $request->ip()
            );
        }

        if ($request->telefono != $user->telefono) {
            HistorialCambio::registrar(
                'usuarios',
                $user->usuario_id,
                'modificar',
                $user->usuario_id,
                'telefono',
                $user->telefono,
                $request->telefono,
                $request->ip()
            );
            $user->telefono = $request->telefono;
        }

        if ($request->correo != $user->correo) {
            HistorialCambio::registrar(
                'usuarios',
                $user->usuario_id,
                'modificar',
                $user->usuario_id,
                'correo',
                $user->correo,
                $request->correo,
                $request->ip()
            );
            $user->correo = $request->correo;
        }

        $user->updated_at = now();
        $user->updated_by = $user->usuario_id;
        $user->save();

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }
}
