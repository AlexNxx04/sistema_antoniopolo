<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidarUsuarioRequest;
use App\Models\HistorialCambio;
use App\Models\Punto;
use App\Models\Rol;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['rol', 'punto', 'unidad']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('cedula', 'like', "%{$buscar}%")
                    ->orWhere('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('rol') && $request->rol != '') {
            $query->where('rol_id', $request->rol);
        }

        if ($request->filled('estado') && $request->estado != '') {
            $activo = $request->estado === 'activo' ? 1 : 0;
            $query->where('activo', $activo);
        }

        $usuarios = $query->orderBy('rol_id')->orderBy('punto_id')->orderBy('nombres')->get();
        $roles = Rol::where('activo', 1)->get();

        return view('gerente.usuarios.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Rol::where('activo', 1)->get();
        $puntos = Punto::where('activo', 1)->get();
        $unidades = Unidad::where('estado', 'activo')->get();

        return view('gerente.usuarios.create', compact('roles', 'puntos', 'unidades'));
    }

    public function store(ValidarUsuarioRequest $request)
    {
        $validated = $request->validated();

        $gerente = Auth::user();
        $username = strtolower(substr($request->nombres, 0, 1).substr($request->apellidos, 0, 1).substr($request->cedula, -4));
        $passwordTemporal = Str::random(8);
        $passwordHash = Hash::make($passwordTemporal);

        $usuarioId = DB::table('usuarios')->insertGetId([
            'cedula' => $request->cedula,
            'nombres' => ucwords(strtolower($request->nombres)),
            'apellidos' => ucwords(strtolower($request->apellidos)),
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'username' => $username,
            'password' => $passwordHash,
            'rol_id' => $request->rol_id,
            'punto_id' => $request->punto_id ?: null,
            'unidad_id' => $request->unidad_id ?: null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        HistorialCambio::registrar(
            'usuarios',
            $usuarioId,
            'crear',
            $gerente->usuario_id,
            'usuario',
            null,
            json_encode(['nombres' => ucwords(strtolower($request->nombres)), 'cedula' => $request->cedula]),
            $request->ip()
        );

        return redirect()->route('gerente.usuarios.show', $usuarioId)
            ->with('password_temporal', $passwordTemporal)
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show($id)
    {
        $usuario = User::with(['rol', 'punto', 'unidad', 'creador'])->findOrFail($id);

        $cambios = HistorialCambio::where('tabla_afectada', 'usuarios')
            ->where('registro_id', $id)
            ->orderBy('timestamp', 'desc')
            ->limit(10)
            ->get();

        return view('gerente.usuarios.show', compact('usuario', 'cambios'));
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $roles = Rol::where('activo', 1)->get();
        $puntos = Punto::where('activo', 1)->get();
        $unidades = Unidad::where('estado', 'activo')->get();

        return view('gerente.usuarios.edit', compact('usuario', 'roles', 'puntos', 'unidades'));
    }

    public function update(ValidarUsuarioRequest $request, $id)
    {
        $usuario = User::findOrFail($id);
        $gerente = Auth::user();

        $validated = $request->validated();

        $campos = ['cedula', 'nombres', 'apellidos', 'telefono', 'correo', 'rol_id', 'punto_id', 'unidad_id', 'activo'];

        foreach ($campos as $campo) {
            $valorNuevo = $campo == 'punto_id' || $campo == 'unidad_id'
                ? ($request->$campo ?: null)
                : ($campo == 'activo' ? ($request->$campo ? 1 : 0) : ($campo == 'nombres' || $campo == 'apellidos' ? ucwords(strtolower($request->$campo)) : $request->$campo));

            if ($valorNuevo != $usuario->$campo) {
                HistorialCambio::registrar(
                    'usuarios',
                    $usuario->usuario_id,
                    'modificar',
                    $gerente->usuario_id,
                    $campo,
                    $usuario->$campo,
                    $valorNuevo,
                    $request->ip()
                );
            }
        }

        DB::table('usuarios')
            ->where('usuario_id', $id)
            ->update([
                'cedula' => $request->cedula,
                'nombres' => ucwords(strtolower($request->nombres)),
                'apellidos' => ucwords(strtolower($request->apellidos)),
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'rol_id' => $request->rol_id,
                'punto_id' => $request->punto_id ?: null,
                'unidad_id' => $request->unidad_id ?: null,
                'activo' => $request->activo ? 1 : 0,
                'updated_at' => now(),
            ]);

        return redirect()->route('gerente.usuarios.show', $id)
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        $gerente = Auth::user();

        if ($usuario->usuario_id === $gerente->usuario_id) {
            return redirect()->back()
                ->with('error', 'No puede eliminarse a sí mismo.');
        }

        HistorialCambio::registrar(
            'usuarios',
            $usuario->usuario_id,
            'modificar',
            $gerente->usuario_id,
            'activo',
            $usuario->activo,
            0,
            $request->ip()
        );

        DB::table('usuarios')
            ->where('usuario_id', $id)
            ->update([
                'activo' => 0,
                'updated_at' => now(),
            ]);

        return redirect()->route('gerente.usuarios.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }

    public function restore($id)
    {
        $usuario = User::findOrFail($id);
        $gerente = Auth::user();

        HistorialCambio::registrar(
            'usuarios',
            $usuario->usuario_id,
            'modificar',
            $gerente->usuario_id,
            'activo',
            $usuario->activo,
            1,
            request()->ip()
        );

        DB::table('usuarios')
            ->where('usuario_id', $id)
            ->update([
                'activo' => 1,
                'updated_at' => now(),
            ]);

        return redirect()->route('gerente.usuarios.index')
            ->with('success', 'Usuario reactivado correctamente.');
    }

    public function resetPassword(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        $gerente = Auth::user();

        $nuevaPassword = Str::random(8);
        $passwordHash = Hash::make($nuevaPassword);

        DB::table('usuarios')
            ->where('usuario_id', $id)
            ->update([
                'password' => $passwordHash,
                'updated_at' => now(),
            ]);

        HistorialCambio::registrar(
            'usuarios',
            $usuario->usuario_id,
            'modificar',
            $gerente->usuario_id,
            'password',
            '***',
            '***',
            $request->ip()
        );

        return redirect()->route('gerente.usuarios.show', $usuario->usuario_id)
            ->with('password_temporal', $nuevaPassword)
            ->with('success', 'Contraseña reseteada correctamente.');
    }
}
