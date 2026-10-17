@extends('layouts.gerente')

@section('title', 'GPS')
@section('header', 'GPS en Tiempo Real')

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
.btn-search { padding: 12px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-search:hover { background: linear-gradient(135deg, #047857, #065f46); }
.btn-mapa { padding: 12px 20px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.table-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ecfdf5, #d1fae5); }
.table-header h3 { font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 10px; }
.table-header h3 i { color: #059669; }
.table-row { display: flex; align-items: center; padding: 14px 24px; border-bottom: 1px solid #f1f5f9; transition: all 0.2s; }
.table-row:hover { background: linear-gradient(90deg, #ecfdf5, #d1fae5); }
.table-row:last-child { border-bottom: none; }
.table-cell { flex: 1; }
.table-cell small { display: block; font-size: 11px; color: #94a3b8; margin-top: 2px; }
.badge-ruta { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #fef3c7; color: #b45309; display: inline-flex; align-items: center; gap: 6px; }
.badge-completa { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #d1fae5; color: #047857; display: inline-flex; align-items: center; gap: 6px; }
.badge-espera { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #dbeafe; color: #1d4ed8; display: inline-flex; align-items: center; gap: 6px; }
.vacio { padding: 60px; text-align: center; }
.vacio i { font-size: 56px; color: #d1fae5; margin-bottom: 16px; }
.vacio p { font-size: 16px; color: #64748b; }
</style>

<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-map-marked-alt"></i>GPS - Tiempo Real</h2>
            <p>Monitoreo de unidades en movimiento</p>
        </div>
    </div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:24px;">
        <div style="padding:24px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(245,158,11,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-route" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $stats['en_ruta'] }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">En Ruta</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#10b981,#059669);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(5,150,105,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-check-circle" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $stats['completadas'] }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">Completadas</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(59,130,246,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-clock" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $stats['en_espera'] }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">En Espera</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(139,92,246,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-bus" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $stats['total_hoy'] }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">Total Hoy</p>
        </div>
    </div>

    <div class="filter-card">
        <form method="GET" action="{{ route('gerente.gps.index') }}" class="flex flex-wrap gap-4 items-end">
            <div style="flex:1;min-width:200px;">
                <label><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>Punto</label>
                <select name="punto_id" class="filter-input">
                    <option value="">Todos</option>
                    @foreach($puntos as $punto)
                        <option value="{{ $punto->punto_id }}" {{ request('punto_id') == $punto->punto_id ? 'selected' : '' }}>
                            {{ $punto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label><i class="fas fa-calendar mr-2 text-gray-400"></i>Fecha</label>
                <input type="date" name="fecha" value="{{ request('fecha', date('Y-m-d')) }}" class="filter-input">
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-filter mr-2"></i> Filtrar</button>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h3><i class="fas fa-list"></i>Unidades en Ruta</h3>
            <a href="{{ route('gerente.gps.mapa') }}" class="btn-mapa">
                <i class="fas fa-map"></i> Ver Mapa
            </a>
        </div>
        <div style="display:flex;padding:12px 24px;background:#f1f5f9;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#64748b;">
            <div class="table-cell"><i class="fas fa-bus"></i> Unidad</div>
            <div class="table-cell"><i class="fas fa-user"></i> Conductor</div>
            <div class="table-cell"><i class="fas fa-map-marker-alt"></i> Punto</div>
            <div class="table-cell"><i class="fas fa-satellite-dish"></i> Última Señal</div>
            <div class="table-cell"><i class="fas fa-flag"></i> Estado</div>
        </div>
        @forelse($unidadesActivas as $unidad)
        <div class="table-row">
            <div class="table-cell">
                <p style="font-weight:600;color:#1e293b;font-family:monospace;">{{ $unidad['placa'] }}</p>
                <small>#{{ str_pad($unidad['numero_interno'], 3, '0', STR_PAD_LEFT) }}</small>
            </div>
            <div class="table-cell">
                <p style="font-weight:500;color:#1e293b;">{{ $unidad['conductor'] }}</p>
            </div>
            <div class="table-cell">
                <p style="color:#64748b;">{{ $unidad['punto_nombre'] }}</p>
            </div>
            <div class="table-cell">
                <p style="color:#64748b;font-size:13px;">-</p>
            </div>
            <div class="table-cell">
                @if($unidad['en_ruta'])
                    <span class="badge-ruta"><i class="fas fa-route"></i> En Ruta</span>
                @else
                    <span class="badge-espera"><i class="fas fa-clock"></i> En Espera</span>
                @endif
            </div>
        </div>
        @empty
        <div class="vacio">
            <i class="fas fa-map-marker-alt"></i>
            <p>No hay unidades en ruta</p>
        </div>
        @endforelse
    </div>
</div>
@endsection