@extends('layouts.gerente')

@section('title', 'Usuarios')
@section('header', 'Gestión de Usuarios')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.btn-new-user { display: inline-flex; align-items: center; gap: 10px; padding: 14px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(5,150,105,0.3); }
.btn-new-user:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5,150,105,0.4); }
.filter-card { padding: 20px 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.filter-card label { font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px; }
.filter-input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.filter-input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.btn-search { padding: 12px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-search:hover { background: linear-gradient(135deg, #047857, #065f46); }
.btn-clean { padding: 12px 24px; background: #f1f5f9; color: #475569; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-clean:hover { background: #e2e8f0; }
.table-container { background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #e2e8f0; }
.table-header h3 { font-size: 18px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 10px; }
.table-header h3 i { color: #059669; }
.user-row { display: flex; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f1f5f9; transition: all 0.2s; }
.user-row:hover { background: linear-gradient(90deg, #ecfdf5, #d1fae5); }
.user-row:last-child { border-bottom: none; }
.user-avatar { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: white; }
.user-info { flex: 1; }
.user-info .name { font-weight: 700; color: #1e293b; font-size: 15px; }
.user-info .email { font-size: 12px; color: #94a3b8; }
.user-cedula { padding: 8px 14px; background: #f1f5f9; border-radius: 10px; font-family: monospace; font-size: 13px; font-weight: 600; color: #475569; }
.user-phone { font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 6px; }
.role-badge { padding: 8px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
.role-gerente { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #7c3aed; }
.role-tarjetera { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
.role-conductor { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; }
.status-active { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; }
.status-inactive { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
.action-btn { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-view { background: #dbeafe; color: #2563eb; }
.action-view:hover { background: #2563eb; color: white; }
.action-edit { background: #d1fae5; color: #059669; }
.action-edit:hover { background: #059669; color: white; }
.action-delete { background: #fee2e2; color: #dc2626; }
.action-delete:hover { background: #dc2626; color: white; }
.empty-state { padding: 60px; text-align: center; }
.empty-state i { font-size: 64px; color: #d1fae5; margin-bottom: 16px; }
.empty-state p { color: #64748b; font-size: 16px; margin-bottom: 20px; }
.pagination-box { padding: 20px; border-top: 1px solid #f1f5f9; background: #f8fafc; }
</style>

<div x-data="{ confirmOpen: false, confirmUrl: '', confirmMessage: '' }">
    
    {{-- Modal de Confirmación --}}
    <template x-teleport="body">
        <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" @keydown.escape.window="confirmOpen = false">
            <div x-show="confirmOpen" class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Confirmar Acción</h3>
                    <p class="text-gray-500 mb-6" x-text="confirmMessage"></p>
                    <div class="flex gap-3">
                        <button @click="confirmOpen = false" class="flex-1 px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">Cancelar</button>
                        <form x-bind:action="confirmUrl" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-5 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 shadow-lg">Desactivar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Encabezado --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-users"></i>Gestión de Usuarios</h2>
            <p>Administra todos los usuarios del sistema</p>
        </div>
        <a href="{{ route('gerente.usuarios.create') }}" class="btn-new-user">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
    <div class="mb-4 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-center gap-3">
        <i class="fas fa-check-circle text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filtros --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('gerente.usuarios.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[250px]">
                <label><i class="fas fa-search mr-2 text-gray-400"></i> Filtrar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Cédula, nombre o teléfono..." class="filter-input">
            </div>
            <div class="w-44">
                <label><i class="fas fa-briefcase mr-2 text-gray-400"></i>Rol</label>
                <select name="rol" class="filter-input">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $rol)
                    <option value="{{ $rol->rol_id }}" {{ request('rol') == $rol->rol_id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label><i class="fas fa-toggle-on mr-2 text-gray-400"></i>Estado</label>
                <select name="estado" class="filter-input">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-filter mr-2"></i> Filtrar</button>
            <a href="{{ route('gerente.usuarios.index') }}" class="btn-clean">Limpiar</a>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="table-container">
        <div class="table-header">
            <h3><i class="fas fa-users"></i>Lista de Usuarios</h3>
            <span class="text-sm text-gray-500">{{ count($usuarios) }} usuarios</span>
        </div>
        
        <div style="display:flex;flex-direction:column;gap:24px;padding:20px;">
            @php
                $gerentes = $usuarios->filter(function($u) { return $u->rol_id == 1; });
                $tarjeteras = $usuarios->filter(function($u) { return $u->rol_id == 2; });
                $conductores = $usuarios->filter(function($u) { return $u->rol_id == 3; });
            @endphp
            
            @if($gerentes->count() > 0)
            <div>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);border-radius:12px;margin-bottom:16px;">
                    <i class="fas fa-crown" style="color:white;font-size:18px;"></i>
                    <span style="color:white;font-weight:700;font-size:16px;">Gerentes</span>
                    <span style="margin-left:auto;background:rgba(255,255,255,0.2);padding:4px 12px;border-radius:20px;color:white;font-size:13px;font-weight:600;">{{ $gerentes->count() }}</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
                @foreach($gerentes as $usuario)
                @php
                    $rolBg = '#ede9fe'; $rolColor = '#7c3aed'; $rolIcon = 'fa-crown';
                    $avatarColor = 'linear-gradient(135deg,#8b5cf6,#6d28d9)';
                    $headerColor = 'linear-gradient(90deg,#8b5cf6,#6d28d9)';
                    $statusBg = $usuario->activo ? 'linear-gradient(135deg,#d1fae5,#a7f3d0)' : 'linear-gradient(135deg,#fee2e2,#fecaca)';
                    $statusColor = $usuario->activo ? '#047857' : '#991b1b';
                    $statusIcon = $usuario->activo ? 'fa-check' : 'fa-times';
                @endphp
                <div style="background:white;border-radius:16px;padding:16px;border:1px solid #e2e8f0;transition:all 0.3s;position:relative;overflow:hidden;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                    <div style="position:absolute;top:0;left:0;right:0;height:4px;background:{{ $headerColor }}"></div>
                    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
                        <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:white;background:{{ $avatarColor }};box-shadow:0 4px 12px rgba(0,0,0,0.2);flex-shrink:0;">
                            {{ substr($usuario->nombres, 0, 1) }}{{ substr($usuario->apellidos, 0, 1) }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-weight:700;color:#1e293b;font-size:15px;display:flex;align-items:center;gap:8px;margin:0 0 4px 0;">
                                {{ $usuario->nombres }} {{ $usuario->apellidos }}
                            </p>
                            <p style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:8px;margin:0;">
                                <i class="fas fa-envelope" style="font-size:11px"></i> {{ $usuario->correo ?? 'Sin correo' }}
                            </p>
                        </div>
                        <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;background:{{ $statusBg }};color:{{ $statusColor }};display:flex;align-items:center;gap:4px;white-space:nowrap;">
                            <i class="fas {{ $statusIcon }}" style="font-size:9px"></i> {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;">
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas fa-id-card" style="font-size:11px;color:#64748b"></i> {{ $usuario->cedula }}
                        </span>
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas fa-phone" style="font-size:11px;color:#64748b"></i> {{ $usuario->telefono ?? 'Sin teléfono' }}
                        </span>
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:{{ $rolBg }};color:{{ $rolColor }};display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas {{ $rolIcon }}" style="font-size:11px"></i> {{ $usuario->rol_nombre }}
                        </span>
                        @if($usuario->punto_nombre)
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:#d1fae5;color:#047857;display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas fa-map-marker-alt" style="font-size:11px"></i> {{ $usuario->punto_nombre }}
                        </span>
                        @endif
                    </div>
                    <div style="display:flex;gap:6px;border-top:1px solid #f1f5f9;padding-top:12px;">
                        <a href="{{ route('gerente.usuarios.show', $usuario->usuario_id) }}" style="flex:1;padding:10px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#2563eb;border-radius:10px;text-align:center;font-weight:600;font-size:11px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(37,99,235,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('gerente.usuarios.edit', $usuario->usuario_id) }}" style="flex:1;padding:12px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;border-radius:12px;text-align:center;font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(5,150,105,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        @if($usuario->activo)
                        <button @click="confirmOpen = true; confirmUrl = '{{ route('gerente.usuarios.destroy', $usuario->usuario_id) }}'; confirmMessage = '¿Desactivar a {{ $usuario->nombres }} {{ $usuario->apellidos }}? El usuario no podrá acceder al sistema hasta que sea reactivado.'" style="flex:1;padding:12px;background:linear-gradient(135deg,#fee2e2,#fecaca);color:#dc2626;border-radius:12px;text-align:center;font-weight:600;font-size:13px;cursor:pointer;border:none;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(220,38,38,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                            <i class="fas fa-user-slash"></i> Desactivar
                        </button>
                        @else
                        <form action="{{ route('gerente.usuarios.restore', $usuario->usuario_id) }}" method="POST" style="flex:1;">
                            @csrf
                            <button type="submit" style="width:100%;padding:12px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;border-radius:12px;text-align:center;font-weight:600;font-size:13px;cursor:pointer;border:none;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(5,150,105,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                                <i class="fas fa-user-check"></i> Reactivar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
                </div>
            </div>
            @endif

            @if($tarjeteras->count() > 0)
            <div>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:12px;margin-bottom:16px;">
                    <i class="fas fa-credit-card" style="color:white;font-size:18px;"></i>
                    <span style="color:white;font-weight:700;font-size:16px;">Tarjeteras</span>
                    <span style="margin-left:auto;background:rgba(255,255,255,0.2);padding:4px 12px;border-radius:20px;color:white;font-size:13px;font-weight:600;">{{ $tarjeteras->count() }}</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
                @foreach($tarjeteras as $usuario)
                @php
                    $rolBg = '#fef3c7'; $rolColor = '#d97706'; $rolIcon = 'fa-credit-card';
                    $avatarColor = 'linear-gradient(135deg,#f59e0b,#d97706)';
                    $headerColor = 'linear-gradient(90deg,#f59e0b,#d97706)';
                    $statusBg = $usuario->activo ? 'linear-gradient(135deg,#d1fae5,#a7f3d0)' : 'linear-gradient(135deg,#fee2e2,#fecaca)';
                    $statusColor = $usuario->activo ? '#047857' : '#991b1b';
                    $statusIcon = $usuario->activo ? 'fa-check' : 'fa-times';
                @endphp
                <div style="background:white;border-radius:16px;padding:16px;border:1px solid #e2e8f0;transition:all 0.3s;position:relative;overflow:hidden;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                    <div style="position:absolute;top:0;left:0;right:0;height:4px;background:{{ $headerColor }}"></div>
                    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
                        <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:white;background:{{ $avatarColor }};box-shadow:0 4px 12px rgba(0,0,0,0.2);flex-shrink:0;">
                            {{ substr($usuario->nombres, 0, 1) }}{{ substr($usuario->apellidos, 0, 1) }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-weight:700;color:#1e293b;font-size:15px;display:flex;align-items:center;gap:8px;margin:0 0 4px 0;">
                                {{ $usuario->nombres }} {{ $usuario->apellidos }}
                            </p>
                            <p style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:8px;margin:0;">
                                <i class="fas fa-envelope" style="font-size:11px"></i> {{ $usuario->correo ?? 'Sin correo' }}
                            </p>
                        </div>
                        <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;background:{{ $statusBg }};color:{{ $statusColor }};display:flex;align-items:center;gap:4px;white-space:nowrap;">
                            <i class="fas {{ $statusIcon }}" style="font-size:9px"></i> {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;">
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas fa-id-card" style="font-size:11px;color:#64748b"></i> {{ $usuario->cedula }}
                        </span>
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas fa-phone" style="font-size:11px;color:#64748b"></i> {{ $usuario->telefono ?? 'Sin teléfono' }}
                        </span>
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:{{ $rolBg }};color:{{ $rolColor }};display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas {{ $rolIcon }}" style="font-size:11px"></i> {{ $usuario->rol_nombre }}
                        </span>
                        @if($usuario->punto_nombre)
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:#d1fae5;color:#047857;display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas fa-map-marker-alt" style="font-size:11px"></i> {{ $usuario->punto_nombre }}
                        </span>
                        @endif
                    </div>
                    <div style="display:flex;gap:10px;border-top:1px solid #f1f5f9;padding-top:16px;">
                        <a href="{{ route('gerente.usuarios.show', $usuario->usuario_id) }}" style="flex:1;padding:12px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#2563eb;border-radius:12px;text-align:center;font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(37,99,235,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('gerente.usuarios.edit', $usuario->usuario_id) }}" style="flex:1;padding:12px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;border-radius:12px;text-align:center;font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(5,150,105,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        @if($usuario->activo)
                        <button @click="confirmOpen = true; confirmUrl = '{{ route('gerente.usuarios.destroy', $usuario->usuario_id) }}'; confirmMessage = '¿Desactivar a {{ $usuario->nombres }} {{ $usuario->apellidos }}? El usuario no podrá acceder al sistema hasta que sea reactivado.'" style="flex:1;padding:12px;background:linear-gradient(135deg,#fee2e2,#fecaca);color:#dc2626;border-radius:12px;text-align:center;font-weight:600;font-size:13px;cursor:pointer;border:none;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(220,38,38,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                            <i class="fas fa-user-slash"></i> Desactivar
                        </button>
                        @else
                        <form action="{{ route('gerente.usuarios.restore', $usuario->usuario_id) }}" method="POST" style="flex:1;">
                            @csrf
                            <button type="submit" style="width:100%;padding:12px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;border-radius:12px;text-align:center;font-weight:600;font-size:13px;cursor:pointer;border:none;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(5,150,105,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                                <i class="fas fa-user-check"></i> Reactivar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
                </div>
            </div>
            @endif

            @if($conductores->count() > 0)
            <div>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:linear-gradient(135deg,#10b981,#059669);border-radius:12px;margin-bottom:16px;">
                    <i class="fas fa-id-card" style="color:white;font-size:18px;"></i>
                    <span style="color:white;font-weight:700;font-size:16px;">Conductores</span>
                    <span style="margin-left:auto;background:rgba(255,255,255,0.2);padding:4px 12px;border-radius:20px;color:white;font-size:13px;font-weight:600;">{{ $conductores->count() }}</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
                @foreach($conductores as $usuario)
                @php
                    $rolBg = '#d1fae5'; $rolColor = '#047857'; $rolIcon = 'fa-id-card';
                    $avatarColor = 'linear-gradient(135deg,#10b981,#059669)';
                    $headerColor = 'linear-gradient(90deg,#10b981,#059669)';
                    $statusBg = $usuario->activo ? 'linear-gradient(135deg,#d1fae5,#a7f3d0)' : 'linear-gradient(135deg,#fee2e2,#fecaca)';
                    $statusColor = $usuario->activo ? '#047857' : '#991b1b';
                    $statusIcon = $usuario->activo ? 'fa-check' : 'fa-times';
                @endphp
                <div style="background:white;border-radius:16px;padding:16px;border:1px solid #e2e8f0;transition:all 0.3s;position:relative;overflow:hidden;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                    <div style="position:absolute;top:0;left:0;right:0;height:4px;background:{{ $headerColor }}"></div>
                    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
                        <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:white;background:{{ $avatarColor }};box-shadow:0 4px 12px rgba(0,0,0,0.2);flex-shrink:0;">
                            {{ substr($usuario->nombres, 0, 1) }}{{ substr($usuario->apellidos, 0, 1) }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-weight:700;color:#1e293b;font-size:15px;display:flex;align-items:center;gap:8px;margin:0 0 4px 0;">
                                {{ $usuario->nombres }} {{ $usuario->apellidos }}
                            </p>
                            <p style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:8px;margin:0;">
                                <i class="fas fa-envelope" style="font-size:11px"></i> {{ $usuario->correo ?? 'Sin correo' }}
                            </p>
                        </div>
                        <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;background:{{ $statusBg }};color:{{ $statusColor }};display:flex;align-items:center;gap:4px;white-space:nowrap;">
                            <i class="fas {{ $statusIcon }}" style="font-size:9px"></i> {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;">
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas fa-id-card" style="font-size:11px;color:#64748b"></i> {{ $usuario->cedula }}
                        </span>
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:#f1f5f9;color:#475569;display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas fa-phone" style="font-size:11px;color:#64748b"></i> {{ $usuario->telefono ?? 'Sin teléfono' }}
                        </span>
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:{{ $rolBg }};color:{{ $rolColor }};display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas {{ $rolIcon }}" style="font-size:11px"></i> {{ $usuario->rol_nombre }}
                        </span>
                        @if($usuario->punto_nombre)
                        <span style="padding:6px 10px;border-radius:8px;font-size:11px;font-weight:600;background:#d1fae5;color:#047857;display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:100px;">
                            <i class="fas fa-map-marker-alt" style="font-size:11px"></i> {{ $usuario->punto_nombre }}
                        </span>
                        @endif
                    </div>
                    <div style="display:flex;gap:6px;border-top:1px solid #f1f5f9;padding-top:12px;">
                        <a href="{{ route('gerente.usuarios.show', $usuario->usuario_id) }}" style="flex:1;padding:10px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#2563eb;border-radius:10px;text-align:center;font-weight:600;font-size:11px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(37,99,235,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('gerente.usuarios.edit', $usuario->usuario_id) }}" style="flex:1;padding:10px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;border-radius:10px;text-align:center;font-weight:600;font-size:11px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(5,150,105,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        @if($usuario->activo)
                        <button @click="confirmOpen = true; confirmUrl = '{{ route('gerente.usuarios.destroy', $usuario->usuario_id) }}'; confirmMessage = '¿Desactivar a {{ $usuario->nombres }} {{ $usuario->apellidos }}? El usuario no podrá acceder al sistema hasta que sea reactivado.'" style="flex:1;padding:10px;background:linear-gradient(135deg,#fee2e2,#fecaca);color:#dc2626;border-radius:10px;text-align:center;font-weight:600;font-size:11px;cursor:pointer;border:none;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(220,38,38,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                            <i class="fas fa-user-slash"></i> Desactivar
                        </button>
                        @else
                        <form action="{{ route('gerente.usuarios.restore', $usuario->usuario_id) }}" method="POST" style="flex:1;">
                            @csrf
                            <button type="submit" style="width:100%;padding:10px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;border-radius:10px;text-align:center;font-weight:600;font-size:11px;cursor:pointer;border:none;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(5,150,105,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                                <i class="fas fa-user-check"></i> Reactivar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
                </div>
            </div>
            @endif

            @if($usuarios->count() == 0)
            <div style="text-align:center;padding:60px;">
                <i class="fas fa-users" style="font-size:64px;color:#d1fae5;margin-bottom:16px;"></i>
                <p style="color:#64748b;font-size:16px;margin-bottom:20px;">No hay usuarios</p>
                <a href="{{ route('gerente.usuarios.create') }}" class="btn-new-user">
                    <i class="fas fa-plus"></i> Crear usuario
                </a>
            </div>
            @endif
        </div>
</div>
@endsection