@extends('layouts.gerente')

@section('title', 'Configuración')
@section('header', 'Configuración del Sistema')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Profile Settings --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-600 to-emerald-700">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Mi Perfil
                </h3>
                <p class="text-emerald-100 text-sm mt-1">Actualiza tu información personal</p>
            </div>

            <form action="{{ route('gerente.configuracion.update') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombres" class="block text-sm font-medium text-gray-700 mb-1.5">Nombres</label>
                        <input type="text" name="nombres" id="nombres" value="{{ old('nombres', $user->nombres) }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        @error('nombres')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos', $user->apellidos) }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        @error('apellidos')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1.5">Cédula de Identidad</label>
                        <input type="text" value="{{ $user->cedula }}" disabled
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-500 text-sm">
                        <p class="mt-1 text-xs text-gray-400">La cédula no se puede modificar</p>
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono / Celular</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $user->telefono) }}"
                               placeholder="0991234567"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-1.5">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" value="{{ old('correo', $user->correo) }}"
                               placeholder="correo@ejemplo.com"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        @error('correo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-colors shadow-lg">
                        <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Cambios del Perfil
                    </button>
                </div>
            </form>
        </div>

        {{-- Account Information --}}
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Información de la Cuenta
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Rol en el Sistema</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-100 text-purple-700">
                        {{ $user->rol_nombre ?? 'Gerente' }}
                    </span>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Punto de Operación</p>
                    <p class="font-semibold text-gray-800">{{ $user->punto_nombre ?? 'No asignado' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Estado de Cuenta</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                        Activo
                    </span>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Usuario desde</p>
                    <p class="font-semibold text-gray-800">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-1 space-y-6">
        
        {{-- Password Change --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-gray-700 to-gray-800">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Cambiar Contraseña
                </h3>
            </div>

            <form action="{{ route('gerente.configuracion.update') }}" method="POST" class="p-4">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="password_actual" class="block text-xs font-medium text-gray-700 mb-1">Contraseña Actual</label>
                        <input type="password" name="password_actual" id="password_actual"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                               placeholder="••••••••">
                        @error('password_actual')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_nueva" class="block text-xs font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                        <input type="password" name="password_nueva" id="password_nueva"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                               placeholder="Mínimo 6 caracteres">
                        @error('password_nueva')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_nueva_confirmation" class="block text-xs font-medium text-gray-700 mb-1">Confirmar Nueva</label>
                        <input type="password" name="password_nueva_confirmation" id="password_nueva_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                               placeholder="Repite la nueva contraseña">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="w-full px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-sm font-medium transition-colors">
                        Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>

        {{-- Quick Links --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800">Accesos Rápidos</h3>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('gerente.auditoria.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">Historial de Cambios</p>
                        <p class="text-xs text-gray-500">Ver todas las modificaciones</p>
                    </div>
                </a>

                <a href="{{ route('gerente.usuarios.show', $user->usuario_id) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">Mi Perfil Completo</p>
                        <p class="text-xs text-gray-500">Ver todos mis datos</p>
                    </div>
                </a>

                <a href="{{ route('gerente.reportes.conductores') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">Estado Conductores</p>
                        <p class="text-xs text-gray-500">Documentos y turnos</p>
                    </div>
                </a>

                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">Configuración General</p>
                        <p class="text-xs text-gray-500">Más opciones del sistema</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- System Info --}}
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl shadow-lg p-5 text-white">
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ asset('images/logo-antonio-polo.png') }}" alt="Logo" class="w-10 h-10 object-contain bg-white rounded-lg p-1">
                <div>
                    <h4 class="font-bold">SGOA-AP</h4>
                    <p class="text-xs text-emerald-200">Sistema de Gestión Operativa</p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-emerald-200">Empresa:</span>
                    <span class="font-medium">Antonio Polo S.A.</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-emerald-200">Ruta:</span>
                    <span class="font-medium">Salinas ↔ Guaranda</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-emerald-200">Versión:</span>
                    <span class="font-medium">1.0.0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-emerald-200">Fecha:</span>
                    <span class="font-medium">{{ now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
