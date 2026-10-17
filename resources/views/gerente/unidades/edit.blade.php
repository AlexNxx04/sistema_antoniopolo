@extends('layouts.gerente')

@section('title', 'Editar Unidad')
@section('header', 'Editar Unidad')

@section('content')
<div class="fade-in">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('gerente.unidades.index') }}" style="padding:14px 18px;background:linear-gradient(135deg,#059669,#047857);color:white;border-radius:12px;display:flex;align-items:center;gap:10px;font-weight:600;font-size:14px;box-shadow:0 4px 12px rgba(5,150,105,0.3);transition:all 0.2s" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(5,150,105,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(5,150,105,0.3)'">
                <i class="fas fa-arrow-left" style="font-size:16px"></i> Volver
            </a>
            <div>
                <h2 style="font-size:24px;font-weight:800;color:#1e293b;margin:0;">Editar Unidad</h2>
                <p style="font-size:13px;color:#94a3b8;margin:4px 0 0 0;">Modificar datos de la unidad</p>
            </div>
        </div>
        <a href="{{ route('gerente.unidades.show', $unidad->unidad_id) }}" style="padding:12px 20px;background:white;border:2px solid #e2e8f0;color:#64748b;border-radius:12px;display:flex;align-items:center;gap:8px;font-weight:600;font-size:14px;">
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
        <form action="{{ route('gerente.unidades.update', $unidad->unidad_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display:flex;flex-direction:column;gap:18px;">
                <div>
                    <label for="placa" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-car" style="color:#6366f1;margin-right:6px;"></i> Placa <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" name="placa" id="placa" value="{{ old('placa', $unidad->placa) }}" required
                           style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;background:#f8fafc;text-transform:uppercase;"
                           onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="numero_interno" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-hashtag" style="color:#6366f1;margin-right:6px;"></i> Número Interno <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="number" name="numero_interno" id="numero_interno" value="{{ old('numero_interno', $unidad->numero_interno) }}" required
                           style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;background:#f8fafc;"
                           onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="celular_gps" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-mobile-alt" style="color:#6366f1;margin-right:6px;"></i> Celular GPS
                    </label>
                    <input type="text" name="celular_gps" id="celular_gps" value="{{ old('celular_gps', $unidad->celular_gps) }}"
                           style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;background:#f8fafc;"
                           placeholder="0991234567" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                    <p style="margin-top:6px;font-size:12px;color:#94a3b8;">Celular dedicado para geolocalización</p>
                </div>

                <div>
                    <label for="punto_id" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-map-marker-alt" style="color:#6366f1;margin-right:6px;"></i> Punto de Operación <span style="color:#ef4444;">*</span>
                    </label>
                    <select id="punto_id" name="punto_id" required style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;background:#f8fafc;cursor:pointer;" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                        @foreach($puntos as $punto)
                            <option value="{{ $punto->punto_id }}" {{ old('punto_id', $unidad->punto_id) == $punto->punto_id ? 'selected' : '' }}>
                                {{ $punto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="estado" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;display:block;">
                        <i class="fas fa-toggle-on" style="color:#6366f1;margin-right:6px;"></i> Estado <span style="color:#ef4444;">*</span>
                    </label>
                    <select id="estado" name="estado" required style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;background:#f8fafc;cursor:pointer;" onfocus="this.style.borderColor='#059669';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(5,150,105,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'">
                        <option value="activo" {{ old('estado', $unidad->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="dado_baja" {{ old('estado', $unidad->estado) === 'dado_baja' ? 'selected' : '' }}>Dado de Baja</option>
                    </select>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:28px;padding-top:20px;border-top:1px solid #e2e8f0;">
                <a href="{{ route('gerente.unidades.index') }}" style="padding:14px 24px;background:#f1f5f9;color:#475569;border:none;border-radius:12px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.2s ease;">
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