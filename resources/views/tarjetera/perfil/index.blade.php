@extends('layouts.tarjetera')

@section('title', 'Mi Perfil')
@section('header', 'Mi Perfil')

@section('content')
<style>
.perfil-card { background: linear-gradient(145deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0; border-radius: 20px; transition: all 0.3s ease; overflow: hidden; position: relative; }
.perfil-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.perfil-card:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
.input-modern { border: 2px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; width: 100%; font-size: 14px; transition: all 0.2s ease; background: #f8fafc; }
.input-modern:focus { border-color: #f59e0b; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(245,158,11,0.1); }
.input-modern.pr-10 { padding-right: 45px; position: relative; }
.toggle-password { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #9ca3af; transition: color 0.2s; font-size: 14px; }
.toggle-password:hover { color: #f59e0b; }
.strength-bar { height: 4px; border-radius: 2px; background: #e5e7eb; margin-top: 8px; overflow: hidden; }
.strength-fill { height: 100%; border-radius: 2px; transition: all 0.3s ease; width: 0%; }
.strength-weak { width: 33%; background: linear-gradient(90deg, #ef4444, #dc2626); }
.strength-medium { width: 66%; background: linear-gradient(90deg, #f59e0b, #d97706); }
.strength-strong { width: 100%; background: linear-gradient(90deg, #10b981, #059669); }
.strength-text { font-size: 12px; font-weight: 600; margin-top: 4px; }
.btn-modern { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; border-radius: 12px; padding: 14px 20px; color: white; font-weight: 600; font-size: 14px; transition: all 0.2s ease; width: 100%; box-shadow: 0 2px 8px rgba(245,158,11,0.3); }
.btn-modern:hover { background: linear-gradient(135deg, #d97706, #b45309); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(245,158,11,0.4); }
</style>

<div class="max-w-5xl mx-auto" x-data="passwordForm()">

    @if(session('success'))
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-3">
        <i class="fas fa-check-circle text-amber-500"></i>
        <span class="text-amber-700 font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="perfil-card mb-6">
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-8 py-10">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-4xl font-bold text-white shadow-lg">
                    {{ substr($user->nombres, 0, 1) }}{{ substr($user->apellidos, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-white">{{ $user->nombres }} {{ $user->apellidos }}</h2>
                    <p class="text-amber-100/80 flex items-center gap-3 mt-2">
                        <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">Tarjetera</span>
                        <span class="text-sm"><i class="fas fa-id-card mr-1"></i>{{ $user->cedula }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        
        <div class="perfil-card">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-user text-amber-600"></i>
                    Datos Personales
                </h3>
            </div>
            <form action="{{ route('tarjetera.perfil.update') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-5">
                    <div>
                        <label for="nombres" class="block text-sm font-semibold text-gray-700 mb-2">Nombres</label>
                        <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $user->nombres) }}" required class="input-modern">
                        @error('nombres')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="apellidos" class="block text-sm font-semibold text-gray-700 mb-2">Apellidos</label>
                        <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}" required class="input-modern">
                        @error('apellidos')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" value="{{ old('correo', $user->correo) }}" required class="input-modern">
                        @error('correo')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="telefono" class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}" placeholder="Ej: 0991234567" class="input-modern">
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="btn-modern flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        <div class="perfil-card">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-lock text-amber-600"></i>
                    Cambiar Contraseña
                </h3>
            </div>
            <form action="{{ route('tarjetera.perfil.password') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-5">
                    <div class="mb-5">
                        <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Contraseña Actual</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" required
                                class="input-modern pr-10" 
                                :type="showPassword1 ? 'text' : 'password'">
                            <i class="toggle-password fas absolute right-4" :class="showPassword1 ? 'fa-eye-slash' : 'fa-eye'" @click="showPassword1 = !showPassword1" style="top: 50%; transform: translateY(-50%);"></i>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Nueva Contraseña</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required minlength="8"
                                class="input-modern pr-10"
                                x-model="password"
                                :type="showPassword2 ? 'text' : 'password'"
                                @input="updateStrength()">
                            <i class="toggle-password fas absolute right-4" :class="showPassword2 ? 'fa-eye-slash' : 'fa-eye'" @click="showPassword2 = !showPassword2" style="top: 50%; transform: translateY(-50%);"></i>
                        </div>
                        
                        <div class="strength-bar">
                            <div class="strength-fill" :class="strengthClass" :style="'width: ' + strengthPercent + '%'"></div>
                        </div>
                        <p class="strength-text" :class="strengthTextClass" x-text="strengthText"></p>
                        <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-5">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="input-modern pr-10"
                                :type="showPassword3 ? 'text' : 'password'"
                                @input="checkConfirm()">
                            <i class="toggle-password fas absolute right-4" :class="showPassword3 ? 'fa-eye-slash' : 'fa-eye'" @click="showPassword3 = !showPassword3" style="top: 50%; transform: translateY(-50%);"></i>
                        </div>
                        <p x-show="!confirmMatch" class="mt-1 text-sm text-red-500">
                            <i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden
                        </p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="btn-modern flex items-center justify-center gap-2">
                        <i class="fas fa-key"></i>
                        Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-6 perfil-card p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-amber-600"></i>
            Información de la Cuenta
        </h4>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 text-sm">
            <div class="flex items-center gap-3">
                <span class="text-gray-500"><i class="fas fa-id-card"></i> Cédula:</span>
                <span class="font-semibold text-gray-900">{{ $user->cedula }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-gray-500"><i class="fas fa-user"></i> Usuario:</span>
                <span class="font-semibold text-gray-900">{{ $user->username ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-gray-500"><i class="fas fa-map-marker-alt"></i> Punto:</span>
                <span class="font-semibold text-gray-900">
                    @if($user->punto_id == 1)
                        Guaranda
                    @elseif($user->punto_id == 2)
                        Salinas
                    @else
                        Sin asignar
                    @endif
                </span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-gray-500"><i class="fas fa-toggle-on"></i> Estado:</span>
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium {{ $user->activo ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    <span class="w-2 h-2 rounded-full {{ $user->activo ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    {{ $user->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-gray-500"><i class="fas fa-calendar-plus"></i> Creado:</span>
                <span class="font-semibold text-gray-900">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-gray-500"><i class="fas fa-envelope"></i> Correo:</span>
                <span class="font-semibold text-gray-900">{{ $user->correo }}</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('passwordForm', () => ({
        showPassword1: false,
        showPassword2: false,
        showPassword3: false,
        password: '',
        strengthPercent: 0,
        strengthClass: '',
        strengthText: '',
        strengthTextClass: '',
        confirmMatch: true,
        
        checkConfirm() {
            const p1 = document.getElementById('password').value;
            const p2 = document.getElementById('password_confirmation').value;
            this.confirmMatch = p2 === '' || p1 === p2;
        },
        
        updateStrength() {
            const password = this.password;
            let strength = 0;
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^A-Za-z0-9]/)) strength += 1;
            
            this.strengthPercent = strength * 25;
            
            if (strength <= 1) {
                this.strengthClass = 'strength-weak';
                this.strengthTextClass = 'text-red-500';
                this.strengthText = 'Débil';
            } else if (strength <= 2) {
                this.strengthClass = 'strength-medium';
                this.strengthTextClass = 'text-amber-500';
                this.strengthText = 'Media';
            } else {
                this.strengthClass = 'strength-strong';
                this.strengthTextClass = 'text-emerald-600';
                this.strengthText = 'Fuerte';
            }
        }
    }));
});
</script>
@endsection