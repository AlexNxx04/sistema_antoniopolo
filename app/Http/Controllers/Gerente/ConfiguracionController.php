<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\HistorialCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('gerente.configuracion.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:15',
            'correo' => 'nullable|email|max:150|unique:usuarios,correo,'.$user->usuario_id.',usuario_id',
            'password_actual' => 'required_with:password_nueva',
            'password_nueva' => 'nullable|min:6|confirmed',
        ], [
            'nombres.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'correo.email' => 'El correo debe ser válido.',
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

            $user->password = Hash::make($request->password_nueva);
        }

        if ($request->nombres != $user->nombres) {
            HistorialCambio::registrar(
                'usuarios',
                $user->usuario_id,
                'modificar',
                $user->usuario_id,
                'nombres',
                $user->nombres,
                $request->nombres,
                $request->ip()
            );
            $user->nombres = $request->nombres;
        }

        if ($request->apellidos != $user->apellidos) {
            HistorialCambio::registrar(
                'usuarios',
                $user->usuario_id,
                'modificar',
                $user->usuario_id,
                'apellidos',
                $user->apellidos,
                $request->apellidos,
                $request->ip()
            );
            $user->apellidos = $request->apellidos;
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
        $user->save();

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }
}
