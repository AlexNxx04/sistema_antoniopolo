@extends('layouts.gerente')

@section('title', 'Mi Perfil')
@section('header', 'Mi Perfil')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Mi Perfil</h2>
        <p class="text-sm text-gray-500">Gestiona tu información personal</p>
    </div>
</div>

{{-- Alerta de éxito --}}
@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-center gap-3">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- Errores --}}
@if($errors->any())
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-semibold text-red-800">Corrija los siguientes errores:</p>
    </div>
    <ul class="list-disc list-inside text-sm text-red-700">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Panel izquierdo - Info del perfil --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="text-center mb-6">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4">
                    {{ substr($user->nombres, 0, 1) }}{{ substr($user->apellidos, 0, 1) }}
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $user->nombres }} {{ $user->apellidos }}</h2>
                <p class="text-gray-500 text-sm">{{ $user->username }}</p>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Rol</span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($user->rol_id == 1) bg-purple-100 text-purple-700
                        @elseif($user->rol_id == 2) bg-blue-100 text-blue-700
                        @else bg-emerald-100 text-emerald-700 @endif">
                        {{ $user->rol_nombre }}
                    </span>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Punto</span>
                    <span class="text-gray-900 text-sm">{{ $user->punto_nombre ?? 'N/A' }}</span>
                </div>

                @if($user->unidad)
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Unidad</span>
                    <span class="text-gray-900 text-sm">{{ $user->unidad->placa }}</span>
                </div>
                @endif

                <div class="flex items-center justify-between py-2">
                    <span class="text-gray-500 text-sm">Estado</span>
                    @if($user->activo)
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Activo</span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Inactivo</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Panel derecho - Formularios --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Datos bloqueados (solo lectura) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Datos del Sistema (Solo lectura)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Cédula</label>
                    <input type="text" value="{{ $user->cedula }}" disabled
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nombres</label>
                    <input type="text" value="{{ $user->nombres }}" disabled
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Apellidos</label>
                    <input type="text" value="{{ $user->apellidos }}" disabled
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Usuario del Sistema</label>
                    <input type="text" value="{{ $user->username }}" disabled
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                </div>
            </div>
        </div>

        {{-- Editar teléfono y correo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar Información de Contacto
            </h3>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="telefono" class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}"
                               placeholder="0991234567"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" value="{{ old('correo', $user->correo) }}"
                               placeholder="correo@ejemplo.com"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- Cambiar contraseña --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Cambiar Contraseña
            </h3>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="password_actual" class="block text-sm font-semibold text-gray-700 mb-2">Contraseña Actual</label>
                        <input type="password" id="password_actual" name="password_actual"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="Ingrese su contraseña actual">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password_nueva" class="block text-sm font-semibold text-gray-700 mb-2">Nueva Contraseña</label>
                            <input type="password" id="password_nueva" name="password_nueva"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Mínimo 6 caracteres">
                        </div>

                        <div>
                            <label for="password_nueva_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                            <input type="password" id="password_nueva_confirmation" name="password_nueva_confirmation"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Repita la nueva contraseña">
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-colors shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
