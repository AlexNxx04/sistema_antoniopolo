@extends('layouts.gerente')

@section('title', 'Ver Usuario')
@section('header', 'Detalle de Usuario')

@section('content')
<div class="fade-in" x-data="{ confirmOpen: false, confirmUrl: '', confirmMessage: '' }">
    <template x-teleport="body">
        <div x-show="confirmOpen" x-cloak style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);" @keydown.escape.window="confirmOpen = false">
            <div x-show="confirmOpen" style="background:white;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.3);width:100%;max-width:420px;margin:20px;overflow:hidden;">
                <div style="padding:32px;text-align:center;">
                    <div style="width:80px;height:80px;margin:0 auto 20px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-exclamation-triangle" style="font-size:36px;color:#dc2626;"></i>
                    </div>
                    <h3 style="font-size:22px;font-weight:700;color:#1e293b;margin:0 0 12px 0;">Confirmar Acción</h3>
                    <p style="color:#64748b;margin:0 0 24px 0;font-size:14px;line-height:1.5;" x-text="confirmMessage"></p>
                    <div style="display:flex;gap:12px;">
                        <button @click="confirmOpen = false" style="flex:1;padding:14px 20px;background:#f1f5f9;color:#475569;border-radius:12px;font-weight:600;font-size:14px;border:none;cursor:pointer;transition:all 0.2s;">Cancelar</button>
                        <form x-bind:action="confirmUrl" method="POST" style="flex:1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="width:100%;padding:14px 20px;background:#dc2626;color:white;border-radius:12px;font-weight:600;font-size:14px;border:none;cursor:pointer;transition:all 0.2s;">Desactivar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('gerente.usuarios.index') }}" style="padding:14px 18px;background:linear-gradient(135deg,#059669,#047857);color:white;border-radius:12px;display:flex;align-items:center;gap:10px;font-weight:600;font-size:14px;box-shadow:0 4px 12px rgba(5,150,105,0.3);transition:all 0.2s" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(5,150,105,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(5,150,105,0.3)'">
                <i class="fas fa-arrow-left" style="font-size:16px"></i> Volver
            </a>
            <div>
                <h2 style="font-size:24px;font-weight:800;color:#1e293b;margin:0;">Detalle de Usuario</h2>
                <p style="font-size:13px;color:#94a3b8;margin:4px 0 0 0;">Información completa del usuario</p>
            </div>
        </div>
    </div>

    @if(session('password_temporal'))
    <div style="margin-bottom:24px;padding:24px;background:linear-gradient(135deg,#fef3c7,#fde68a);border:1px solid #fbbf24;border-radius:16px;display:flex;align-items:center;gap:16px;">
        <div style="width:56px;height:56px;background:white;border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.1);flex-shrink:0;">
            <i class="fas fa-key" style="color:#d97706;font-size:24px"></i>
        </div>
        <div>
            <p style="font-size:16px;font-weight:700;color:#92400e;margin:0 0 4px 0;">Contraseña Temporal Generada</p>
            <p style="font-size:13px;color:#92400e;margin:0 0 8px 0;">Cédula: <strong style="font-family:monospace;">{{ $usuario->cedula }}</strong></p>
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="padding:10px 16px;background:white;border-radius:10px;font-size:18px;font-weight:700;font-family:monospace;color:#b45309;">{{ session('password_temporal') }}</span>
                <span style="font-size:12px;color:#92400e;">Entregue esta contraseña al usuario</span>
            </div>
        </div>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr;gap:20px;margin-bottom:20px;">
        <div style="background:white;border-radius:20px;padding:28px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                <div style="width:90px;height:90px;border-radius:20px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:28px;color:white;background:linear-gradient(135deg,#6366f1,#8b5cf6);box-shadow:0 8px 24px rgba(99,102,241,0.4);flex-shrink:0;">
                    {{ substr($usuario->nombres, 0, 1) }}{{ substr($usuario->apellidos, 0, 1) }}
                </div>
                <div style="flex:1;min-width:200px;">
                    <h3 style="font-size:22px;font-weight:800;color:#1e293b;margin:0 0 8px 0;display:flex;align-items:center;gap:12px;">
                        {{ $usuario->nombres }} {{ $usuario->apellidos }}
                        @if($usuario->rol_id == 1)<i class="fas fa-crown" style="color:#8b5cf6;font-size:18px" title="Gerente"></i>@endif
                    </h3>
                    <span style="padding:8px 16px;border-radius:20px;font-size:13px;font-weight:600;background:#d1fae5;color:#047857;display:inline-flex;align-items:center;gap:8px;">
                        <i class="fas fa-user-tag"></i> {{ $usuario->rol->nombre ?? 'Sin rol' }}
                    </span>
                </div>
                <span style="padding:10px 18px;border-radius:20px;font-size:13px;font-weight:600;@if($usuario->activo) background:#d1fae5;color:#059669; @else background:#fee2e2;color:#dc2626; @endif display:flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas {{ $usuario->activo ? 'fa-check-circle' : 'fa-times-circle' }}"></i> {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr;gap:20px;">
        <div style="background:white;border-radius:20px;padding:24px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 20px 0;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-id-card" style="color:#6366f1;font-size:18px"></i> Información Personal
            </h3>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
                <div style="padding:16px;background:#f8fafc;border-radius:14px;display:flex;align-items:center;gap:14px;">
                    <div style="width:44px;height:44px;border-radius:12px;background:#e0e7ff;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-id-card" style="color:#6366f1;font-size:18px"></i>
                    </div>
                    <div>
                        <p style="font-size:12px;color:#64748b;margin:0;">Cédula</p>
                        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:4px 0 0 0;font-family:monospace;">{{ $usuario->cedula }}</p>
                    </div>
                </div>
                <div style="padding:16px;background:#f8fafc;border-radius:14px;display:flex;align-items:center;gap:14px;">
                    <div style="width:44px;height:44px;border-radius:12px;background:#d1fae5;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-phone" style="color:#059669;font-size:18px"></i>
                    </div>
                    <div>
                        <p style="font-size:12px;color:#64748b;margin:0;">Teléfono</p>
                        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:4px 0 0 0;">{{ $usuario->telefono ?? 'Sin registro' }}</p>
                    </div>
                </div>
                <div style="grid-column:1 / -1;padding:16px;background:#f8fafc;border-radius:14px;display:flex;align-items:center;gap:14px;">
                    <div style="width:44px;height:44px;border-radius:12px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-envelope" style="color:#d97706;font-size:18px"></i>
                    </div>
                    <div style="min-width:0;">
                        <p style="font-size:12px;color:#64748b;margin:0;">Correo</p>
                        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:4px 0 0 0;word-break:break-all;">{{ $usuario->correo ?? 'Sin registro' }}</p>
                    </div>
                </div>
                <div style="padding:16px;background:#f8fafc;border-radius:14px;display:flex;align-items:center;gap:14px;">
                    <div style="width:44px;height:44px;border-radius:12px;background:#e0e7ff;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-building" style="color:#6366f1;font-size:18px"></i>
                    </div>
                    <div>
                        <p style="font-size:12px;color:#64748b;margin:0;">Punto</p>
                        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:4px 0 0 0;">{{ $usuario->punto->nombre ?? 'Sin asignar' }}</p>
                    </div>
                </div>
                <div style="padding:16px;background:#f8fafc;border-radius:14px;display:flex;align-items:center;gap:14px;">
                    <div style="width:44px;height:44px;border-radius:12px;background:#fce7f3;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-car" style="color:#db2777;font-size:18px"></i>
                    </div>
                    <div>
                        <p style="font-size:12px;color:#64748b;margin:0;">Unidad</p>
                        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:4px 0 0 0;">{{ $usuario->unidad->placa ?? 'Sin asignar' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr;gap:20px;margin-top:20px;">
        <div style="background:white;border-radius:20px;padding:24px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 20px 0;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-cogs" style="color:#6366f1;font-size:18px"></i> Acciones
            </h3>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <form action="{{ route('gerente.usuarios.resetPassword', $usuario->usuario_id) }}" method="POST" style="flex:1;min-width:150px;">
                    @csrf
                    <button type="submit" style="width:100%;padding:16px;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border-radius:14px;text-align:center;font-weight:600;font-size:14px;display:flex;align-items:center;justify-content:center;gap:10px;cursor:pointer;border:none;transition:all 0.2s;box-shadow:0 4px 16px rgba(245,158,11,0.3)" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(245,158,11,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 16px rgba(245,158,11,0.3)'">
                        <i class="fas fa-key"></i> Restablecer Contraseña
                    </button>
                </form>
                @if($usuario->activo)
                <button @click="confirmOpen = true; confirmUrl = '{{ route('gerente.usuarios.destroy', $usuario->usuario_id) }}'; confirmMessage = '¿Desactivar a {{ $usuario->nombres }} {{ $usuario->apellidos }}? El usuario no podrá acceder al sistema hasta que sea reactivado.'" style="flex:1;min-width:150px;padding:16px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border-radius:14px;text-align:center;font-weight:600;font-size:14px;display:flex;align-items:center;justify-content:center;gap:10px;cursor:pointer;border:none;transition:all 0.2s;box-shadow:0 4px 16px rgba(239,68,68,0.3)" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(239,68,68,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 16px rgba(239,68,68,0.3)'">
                    <i class="fas fa-user-slash"></i> Desactivar
                </button>
                @else
                <form action="{{ route('gerente.usuarios.restore', $usuario->usuario_id) }}" method="POST" style="flex:1;min-width:150px;">
                    @csrf
                    <button type="submit" style="width:100%;padding:16px;background:linear-gradient(135deg,#10b981,#059669);color:white;border-radius:14px;text-align:center;font-weight:600;font-size:14px;display:flex;align-items:center;justify-content:center;gap:10px;cursor:pointer;border:none;transition:all 0.2s;box-shadow:0 4px 16px rgba(16,185,129,0.3)" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(16,185,129,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 16px rgba(16,185,129,0.3)'">
                        <i class="fas fa-user-check"></i> Reactivar
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr;gap:20px;margin-top:20px;">
        <div style="background:white;border-radius:20px;padding:24px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 20px 0;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-history" style="color:#6366f1;font-size:18px"></i> Historial de Cambios
            </h3>
            <div style="display:flex;flex-direction:column;gap:12px;">
                @forelse($cambios as $cambio)
                    <div style="padding:14px 18px;background:#f8fafc;border-radius:12px;display:flex;align-items:center;gap:14px;">
                        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:white;flex-shrink:0;">
                            {{ substr($cambio->usuario->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div style="flex:1;">
                            <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $cambio->campo_modificado ?? $cambio->accion }}</p>
                            <p style="font-size:12px;color:#64748b;margin:4px 0 0 0;">{{ $cambio->timestamp?->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:40px;">
                        <i class="fas fa-history" style="font-size:48px;color:#e2e8f0;margin-bottom:12px;"></i>
                        <p style="font-size:14px;color:#94a3b8;margin:0;">No hay cambios registrados</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>
@endsection