<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cedula' => ['required', 'string', 'size:10'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $cedula = $this->input('cedula');
        $password = $this->input('password');

        $user = User::where('cedula', $cedula)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'cedula' => ['Usuario no encontrado.'],
            ]);
        }

        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'cedula' => ['Contraseña incorrecta.'],
            ]);
        }

        if (! $user->activo) {
            throw ValidationException::withMessages([
                'cedula' => ['Usuario inactivo.'],
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
    }
}
