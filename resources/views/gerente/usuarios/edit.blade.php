@extends('layouts.gerente')

@section('title', 'Editar Usuario')
@section('header', 'Editar Usuario')

@section('content')
<style>
.form-card { background: linear-gradient(145deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0; border-radius: 20px; }
.form-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; display: block; }
.form-input { width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; transition: all 0.2s ease; background: #f8fafc; }
.form-input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1); }
.form-select { width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; transition: all 0.2s ease; background: #f8fafc; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 20px; }
.form-select:focus { border-color: #059669; background-color: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1); }
.btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 14px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.btn-primary:hover { background: linear-gradient(135deg, #047857, #065f46); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3); }
.btn-secondary { display: inline-flex; align-items: center; gap: 8px; padding: 14px 24px; background: #f1f5f9; color: #475569; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.btn-secondary:hover { background: #e2e8f0; }
.alert-success { background: linear-gradient(135deg, #d1fae5, #a7f3d0); border: 1px solid #6ee7b7; border-radius: 16px; padding: 20px; }
.alert-error { background: linear-gradient(135deg, #fee2e2, #fecaca); border: 1px solid #fca5a5; border-radius: 16px; padding: 20px; }
.required { color: #ef4444; }
.readonly-field { padding: 14px 16px; background: #f1f5f9; border: 2px solid #e2e8f0; border-radius: 12px; }
.checkbox-wrapper { display: flex; align-items: center; gap: 12px; padding: 16px; background: #f8fafc; border-radius: 12px; border: 2px solid #e2e8f0; }
.checkbox-wrapper input { width: 20px; height: 20px; accent-color: #059669; }
</style>

<div class="fade-in">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('gerente.usuarios.index') }}" style="padding:14px 18px;background:linear-gradient(135deg,#059669,#047857);color:white;border-radius:12px;display:flex;align-items:center;gap:10px;font-weight:600;font-size:14px;box-shadow:0 4px 12px rgba(5,150,105,0.3);transition:all 0.2s" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(5,150,105,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(5,150,105,0.3)'">
                <i class="fas fa-arrow-left" style="font-size:16px"></i> Volver
            </a>
            <div>
                <h2 style="font-size:24px;font-weight:800;color:#1e293b;margin:0;">Editar Usuario</h2>
                <p style="font-size:13px;color:#94a3b8;margin:4px 0 0 0;">Modificar datos del usuario</p>
            </div>
        </div>
        <a href="{{ route('gerente.usuarios.show', $usuario->usuario_id) }}" style="padding:12px 20px;background:white;border:2px solid #e2e8f0;color:#64748b;border-radius:12px;display:flex;align-items:center;gap:8px;font-weight:600;font-size:14px;">
            <i class="fas fa-eye"></i> Ver Detalle
        </a>
    </div>

    @if(session('success'))
        <div style="margin-bottom:24px;padding:20px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);border:1px solid #6ee7b7;border-radius:16px;display:flex;align-items:center;gap:12px;">
            <div style="width:44px;height:44px;background:white;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                <i class="fas fa-check" style="color:#059669;font-size:20px"></i>
            </div>
            <span style="font-weight:600;color:#065f46;">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="margin-bottom:24px;padding:24px;background:linear-gradient(135deg,#fee2e2,#fecaca);border:1px solid #fca5a5;border-radius:16px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <i class="fas fa-exclamation-circle" style="color:#dc2626;font-size:20px"></i>
                <p style="font-weight:700;color:#991b1b;margin:0;">Corrija los siguientes errores:</p>
            </div>
            <ul style="list-style:disc inside;font-size:14px;color:#b91c1c;margin:0;padding-left:8px;">
                @foreach($errors->all() as $error)
                    <li style="margin-bottom:4px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background:white;border-radius:20px;padding:28px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.08);max-width:800px;margin:0 auto;">
        <form action="{{ route('gerente.usuarios.update', $usuario->usuario_id) }}" method="POST" x-data="{ rolId: '{{ $usuario->rol_id }}' }">
            @csrf
            @method('PUT')

            <div style="display:flex;flex-direction:column;gap:18px;">
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-id-card" style="color:#6366f1;margin-right:6px;"></i> Cédula <span style="color:#ef4444;">*</span>
                    </label>
                    <div style="padding:14px 16px;background:#f1f5f9;border:2px solid #e2e8f0;border-radius:12px;display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:16px;font-weight:600;font-family:monospace;color:#1e293b;">{{ $usuario->cedula }}</span>
                        <span style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px;"><i class="fas fa-lock"></i> No editable</span>
                    </div>
                    <input type="hidden" name="cedula" value="{{ $usuario->cedula }}">
                </div>

                <div>
                    <label for="nombres" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-user" style="color:#6366f1;margin-right:6px;"></i> Nombres <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $usuario->nombres) }}" required style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;transition:all 0.2s ease;background:#f8fafc;text-transform:uppercase;" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="apellidos" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-user-tag" style="color:#6366f1;margin-right:6px;"></i> Apellidos <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}" required style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;transition:all 0.2s ease;background:#f8fafc;text-transform:uppercase;" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="telefono" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-mobile-alt" style="color:#6366f1;margin-right:6px;"></i> Teléfono Celular
                    </label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" pattern="09[0-9]{8}" inputmode="numeric" maxlength="10" placeholder="0991234567" style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;transition:all 0.2s ease;background:#f8fafc;" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="correo" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-envelope" style="color:#6366f1;margin-right:6px;"></i> Correo
                    </label>
                    <input type="email" id="correo" name="correo" value="{{ old('correo', $usuario->correo) }}" style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;transition:all 0.2s ease;background:#f8fafc;" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="rol_id" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-briefcase" style="color:#6366f1;margin-right:6px;"></i> Rol <span style="color:#ef4444;">*</span>
                    </label>
                    <select id="rol_id" name="rol_id" required x-model="rolId" style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;transition:all 0.2s ease;background:#f8fafc;cursor:pointer;" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                        @foreach($roles as $rol)
                            <option value="{{ $rol->rol_id }}" {{ old('rol_id', $usuario->rol_id) == $rol->rol_id ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div :class="{ 'opacity-50 pointer-events-none': rolId == 1 || rolId == '' }">
                    <label for="punto_id" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-map-marker-alt" style="color:#6366f1;margin-right:6px;"></i> Punto de Operación
                    </label>
                    <select id="punto_id" name="punto_id" :disabled="rolId == 1 || rolId == ''" style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;transition:all 0.2s ease;background:#f8fafc;cursor:pointer;" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                        <option value="">Ninguno</option>
                        @foreach($puntos as $punto)
                            <option value="{{ $punto->punto_id }}" {{ old('punto_id', $usuario->punto_id) == $punto->punto_id ? 'selected' : '' }}>
                                {{ $punto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div :class="{ 'opacity-50 pointer-events-none': rolId != 3 }">
                    <label for="unidad_id" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-car" style="color:#6366f1;margin-right:6px;"></i> Unidad Asignada
                    </label>
                    <select id="unidad_id" name="unidad_id" :disabled="rolId != 3" style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;transition:all 0.2s ease;background:#f8fafc;cursor:pointer;" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                        <option value="">Ninguna</option>
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad->unidad_id }}" {{ old('unidad_id', $usuario->unidad_id) == $unidad->unidad_id ? 'selected' : '' }}>
                                {{ $unidad->placa }} - {{ $unidad->numero_interno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-top:8px;">
                    <label style="display:flex;align-items:center;gap:12px;padding:16px;background:#f8fafc;border-radius:12px;border:2px solid #e2e8f0;cursor:pointer;">
                        <input type="checkbox" name="activo" value="1" {{ $usuario->activo ? 'checked' : '' }} style="width:20px;height:20px;accent-color:#059669;cursor:pointer;">
                        <span style="font-weight:600;color:#1e293b;">Usuario activo</span>
                    </label>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:28px;padding-top:20px;border-top:1px solid #e2e8f0;">
                <a href="{{ route('gerente.usuarios.index') }}" style="padding:14px 24px;background:#f1f5f9;color:#475569;border:none;border-radius:12px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.2s ease;">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" style="padding:14px 24px;background:linear-gradient(135deg,#059669,#047857);color:white;border:none;border-radius:12px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.2s ease;box-shadow:0 4px 12px rgba(5,150,105,0.3);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(5,150,105,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(5,150,105,0.3)'">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection