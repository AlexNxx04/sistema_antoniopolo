@extends('layouts.gerente')

@section('title', 'Parámetros')
@section('header', 'Parámetros del Sistema')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.filter-card { padding: 20px 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.filter-card label { font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px; }
.filter-input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.filter-input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.btn-save { padding: 14px 28px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 15px rgba(5,150,105,0.3); display: inline-flex; align-items: center; gap: 10px; }
.btn-save:hover { background: linear-gradient(135deg, #047857, #065f46); transform: translateY(-2px); }
.card-tabs { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 24px; }
.tabs-header { display: flex; border-bottom: 1px solid #e2e8f0; }
.tab-btn { flex: 1; padding: 18px 24px; text-align: center; font-weight: 600; cursor: pointer; transition: all 0.3s; border-bottom: 3px solid transparent; }
.tab-btn.active { background: linear-gradient(135deg, #ecfdf5, #d1fae5); color: #059669; border-bottom-color: #059669; }
.tab-btn.inactive { color: #64748b; border-bottom-color: transparent; }
.tab-btn.inactive:hover { background: #f8fafc; color: #059669; }
.tab-content { padding: 24px; }
.tab-content.hidden { display: none; }
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.param-card { background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0; transition: all 0.3s; }
.param-card:hover { border-color: #059669; box-shadow: 0 4px 15px rgba(5,150,105,0.15); }
.param-card label { font-size: 15px; font-weight: 600; color: #475569; display: block; margin-bottom: 12px; }
.param-card label i { color: #059669; margin-right: 8px; }
.param-card input { width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; background: #f8fafc; transition: all 0.2s; }
.param-card input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.form-group label { font-size: 14px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px; }
.form-group label i { color: #059669; margin-right: 8px; }
.form-group input { width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.form-group input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.form-group p { font-size: 12px; color: #94a3b8; margin-top: 6px; }
.actions-bar { display: flex; justify-content: flex-end; padding-top: 20px; margin-top: 20px; border-top: 1px solid #e2e8f0; }
.input-card { padding: 24px; border-radius: 20px; position: relative; overflow: hidden; }
.input-card.green { background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 10px 25px rgba(5,150,105,0.35); }
.input-card.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; box-shadow: 0 10px 25px rgba(59,130,246,0.35); }
.input-card.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; box-shadow: 0 10px 25px rgba(139,92,246,0.35); }
.info-box { padding: 20px; background: linear-gradient(145deg, #f8fafc, #f1f5f9); border-radius: 16px; border: 1px solid #e2e8f0; margin-top: 24px; }
.info-box h3 { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
.info-box h3 i { color: #059669; }
.info-box p { font-size: 12px; color: #64748b; line-height: 1.6; }
.empty-state { padding: 40px; text-align: center; color: #64748b; }
.empty-state i { font-size: 40px; color: #d1fae5; margin-bottom: 12px; display: block; }
</style>

<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-cogs"></i>Parámetros del Sistema</h2>
            <p>Configuración general y por punto</p>
        </div>
    </div>

    @if(session('success'))
    <div style="margin-bottom:24px;padding:16px 24px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);border-radius:14px;border:1px solid #059669;color:#065f46;font-weight:600;display:flex;align-items:center;gap:12px;">
        <i class="fas fa-check-circle" style="font-size:20px;"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="card-tabs">
        <div class="tabs-header">
            <div onclick="showTab('globales')" id="tab-globales" class="tab-btn active">
                <i class="fas fa-cogs mr-2"></i>Parámetros Globales
            </div>
            <div onclick="showTab('puntos')" id="tab-puntos" class="tab-btn inactive">
                <i class="fas fa-map-marker-alt mr-2"></i>Configuración por Punto
            </div>
        </div>

        <div id="content-globales" class="tab-content">
            <form method="POST" action="{{ route('gerente.parametros.update') }}">
                @csrf
                <div class="form-grid">
                    @foreach($parametros as $clave => $valor)
                    <div class="param-card">
                        @php
                            $labelText = ucfirst(str_replace('_', ' ', $clave));
                            if($clave == 'tiempo_estimado_ruta') $labelText = 'Tiempo estimado ruta (minutos)';
                            if($clave == 'multa_por_minuto_retraso') $labelText = 'Multa por minuto retraso (dólares)';
                            if($clave == 'hora_inicio_operacion') $labelText = 'Hora inicio operación';
                            if($clave == 'hora_fin_operacion') $labelText = 'Hora fin operación';
                            if($clave == 'whatsapp_empresa') $labelText = 'WhatsApp Empresa (celular)';
                            if($clave == 'telefono_empresa') $labelText = 'Teléfono Empresa (convencional)';
                        @endphp
                        <label><i class="fas fa-cog"></i> {{ $labelText }}</label>
                        @php
                            $inputType = 'text';
                            $inputAttrs = '';
                            $onInput = '';
                            if(in_array($clave, ['hora_inicio_operacion', 'hora_fin_operacion'])) {
                                $inputType = 'time';
                            } elseif(in_array($clave, ['tiempo_estimado_ruta'])) {
                                $inputType = 'number';
                                $inputAttrs = 'min="1" max="500"';
                            } elseif(in_array($clave, ['multa_por_minuto_retraso'])) {
                                $inputType = 'number';
                                $inputAttrs = 'step="0.01" min="0"';
                            } elseif($clave == 'whatsapp_empresa') {
                                $inputType = 'tel';
                                $inputAttrs = 'required maxlength="10"';
                                $onInput = 'oninput="let v=this.value.replace(/[^0-9]/g,\'\'); this.value=v.slice(0,10)" placeholder="10 dígitos"';
                            } elseif($clave == 'telefono_empresa') {
                                $inputType = 'tel';
                                $inputAttrs = 'required maxlength="9"';
                                $onInput = 'oninput="let v=this.value.replace(/[^0-9]/g,\'\'); this.value=v.slice(0,9)" placeholder="9 dígitos"';
                            } elseif(strpos($clave, 'telefono') !== false) {
                                $inputType = 'tel';
                                $inputAttrs = 'maxlength="15"';
                            } elseif(strpos($clave, 'zona') !== false) {
                                $inputType = 'number';
                                $inputAttrs = 'min="1" max="99"';
                            }
                        @endphp
                        <input type="{{ $inputType }}" name="{{ $clave }}" value="{{ $valor }}" {!! $inputAttrs !!} {!! $onInput !!} placeholder="{{ $clave }}">
                    </div>
                    @endforeach
                </div>
                <div class="actions-bar">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        <div id="content-puntos" class="tab-content hidden">
            <div style="margin-bottom:24px;">
                <label style="font-size:14px;font-weight:600;color:#475569;display:block;margin-bottom:8px;">
                    <i class="fas fa-map-pin" style="color:#059669;margin-right:8px;"></i> Seleccionar Punto
                </label>
                <select onchange="window.location.href='{{ route('gerente.parametros.index') }}?punto_id='+this.value" class="filter-input" style="max-width:400px;">
                    @foreach($puntos as $punto)
                        <option value="{{ $punto->punto_id }}" {{ $puntoId == $punto->punto_id ? 'selected' : '' }}>
                            {{ $punto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($puntoSelect)
            <form method="POST" action="{{ route('gerente.parametros.updatePunto') }}">
                @csrf
                <input type="hidden" name="punto_id" value="{{ $puntoSelect->punto_id }}">
                <input type="hidden" name="old_radio" value="{{ $puntoSelect->radio_geofence_m }}">

                <div class="form-grid">
                    <div class="param-card">
                        <label><i class="fas fa-broadcast-tower"></i> Radio Geofence (metros)</label>
                        <input type="number" name="radio_geofence_m" value="{{ $puntoSelect->radio_geofence_m }}" min="10" max="1000" placeholder="Ej: 50">
                    </div>
                    <div class="param-card">
                        <label><i class="fas fa-map-marker-alt"></i> Latitud</label>
                        <input type="number" name="latitud" value="{{ $puntoSelect->latitud }}" step="0.00000001" placeholder="-1.60330000">
                    </div>
                    <div class="param-card">
                        <label><i class="fas fa-map-marker-alt"></i> Longitud</label>
                        <input type="number" name="longitud" value="{{ $puntoSelect->longitud }}" step="0.00000001" placeholder="-79.00180000">
                    </div>
                </div>
                <div class="actions-bar">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Guardar Punto
                    </button>
                </div>
            </form>
            @else
            <div class="empty-state">
                <i class="fas fa-info-circle"></i>
                <p>Seleccione un punto para configurar</p>
            </div>
            @endif
        </div>
    </div>

    <div class="info-box">
        <h3><i class="fas fa-calculator"></i> Cálculo Automático de Multas</h3>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
        <div style="padding:24px;background:linear-gradient(135deg,#10b981,#059669);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(5,150,105,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-dollar-sign" style="font-size:32px;margin-bottom:12px;display:block;"></i>
            <p style="font-size:32px;font-weight:800;line-height:1.2;color:#fff;">${{ number_format($parametros['multa_por_minuto_retraso'] ?? 1.00, 2) }}</p>
            <p style="font-size:14px;font-weight:600;margin-top:8px;color:#fff;">Multa por minuto (dólares)</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(59,130,246,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-clock" style="font-size:32px;margin-bottom:12px;display:block;"></i>
            <p style="font-size:32px;font-weight:800;line-height:1.2;color:#fff;">{{ $parametros['tiempo_estimado_ruta'] ?? 120 }}</p>
            <p style="font-size:14px;font-weight:600;margin-top:8px;color:#fff;">Tiempo estimado ruta (minutos)</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(139,92,246,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-sun" style="font-size:32px;margin-bottom:12px;display:block;"></i>
            <p style="font-size:24px;font-weight:800;line-height:1.2;color:#fff;">{{ $parametros['hora_inicio_operacion'] ?? '05:30' }} - {{ $parametros['hora_fin_operacion'] ?? '19:30' }}</p>
            <p style="font-size:14px;font-weight:600;margin-top:8px;color:#fff;">Hora operación</p>
        </div>
    </div>
        <p style="margin-top:16px;font-size:13px;color:#64748b;"><i class="fas fa-info-circle mr-2" style="color:#059669;"></i>
            El sistema calcula la multa automáticamente: (Tiempo real de llegada - Tiempo límite) × Valor por minuto = Multa total
        </p>
    </div>
</div>

<script>
function showTab(tab) {
    document.getElementById('content-globales').classList.add('hidden');
    document.getElementById('content-puntos').classList.add('hidden');
    document.getElementById('tab-globales').classList.remove('active');
    document.getElementById('tab-globales').classList.add('inactive');
    document.getElementById('tab-puntos').classList.remove('active');
    document.getElementById('tab-puntos').classList.add('inactive');

    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('active');
    document.getElementById('tab-' + tab).classList.remove('inactive');
}
</script>
@endsection
