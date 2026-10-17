@extends('layouts.gerente')

@section('title', 'Operación Diaria')
@section('header', 'Operación Diaria')

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
.seccion-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.seccion-header { padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; }
.seccion-header.op { background: linear-gradient(135deg, #059669, #047857); color: white; }
.seccion-header.descanso { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; }
.seccion-header h3 { font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
.turno-item { display: flex; align-items: center; gap: 14px; padding: 14px 24px; border-bottom: 1px solid #f1f5f9; transition: all 0.2s; }
.turno-item:hover { background: linear-gradient(90deg, #ecfdf5, #d1fae5); }
.turno-item:last-child { border-bottom: none; }
.turno-avatar { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: white; }
.turno-info { flex: 1; }
.turno-nombre { font-size: 14px; font-weight: 600; color: #1e293b; }
.turno-detalle { font-size: 12px; color: #64748b; margin-top: 2px; }
.badge-puntero { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #fef3c7; color: #b45309; }
.badge-descanso { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #ede9fe; color: #7c3aed; }
.vacio { padding: 40px; text-align: center; }
.vacio i { font-size: 40px; color: #e2e8f0; margin-bottom: 12px; }
.vacio p { font-size: 14px; color: #94a3b8; }
</style>

<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-bolt"></i>Operación Diaria</h2>
            <p>Visualización de turnos de operación y descanso</p>
        </div>
    </div>

    <div class="filter-card">
        <form method="GET" action="{{ route('gerente.operacion.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="w-48">
                <label><i class="fas fa-calendar mr-2 text-gray-400"></i>Fecha</label>
                <input type="date" name="fecha" value="{{ $fecha }}" class="filter-input">
            </div>
            <div class="w-48">
                <label><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>Punto</label>
                <select name="punto_id" class="filter-input">
                    <option value="">Todos los puntos</option>
                    @foreach($puntos as $punto)
                        <option value="{{ $punto->punto_id }}" {{ $puntoId == $punto->punto_id ? 'selected' : '' }}>
                            {{ $punto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-filter mr-2"></i> Filtrar</button>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;">
        <div style="background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#059669,#047857);padding:14px 20px;">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:white;"><i class="fas fa-bolt" style="margin-right:8px;"></i> Guaranda - Operan ({{ $operacionGuaranda->count() }})</h3>
            </div>
            <div style="max-height:400px;overflow-y:auto;">
                @forelse($operacionGuaranda as $turno)
                <a href="{{ route('gerente.operacion.show', $turno->turno_id) }}" style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f1f5f9;text-decoration:none;">
                    <div style="width:36px;height:36px;background:#dbeafe;color:#1d4ed8;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                        {{ $turno->posicion }}
                    </div>
                    <div style="flex:1;">
                        <p style="margin:0;font-size:13px;font-weight:600;color:#1e293b;">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                        <p style="margin:2px 0 0;font-size:11px;color:#64748b;">{{ $turno->unidad->placa ?? 'Sin unidad' }}</p>
                    </div>
                    @if($turno->es_puntero)
                    <span style="padding:3px 8px;border-radius:12px;font-size:10px;font-weight:600;background:#fef3c7;color:#b45309;">P{{ $turno->posicion }}</span>
                    @endif
                </a>
                @empty
                <div style="padding:30px;text-align:center;">
                    <i class="fas fa-check-circle" style="font-size:32px;color:#e2e8f0;margin-bottom:8px;display:block;"></i>
                    <p style="margin:0;font-size:13px;color:#94a3b8;">Sin operar</p>
                </div>
                @endforelse
            </div>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#059669,#047857);padding:14px 20px;">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:white;"><i class="fas fa-bolt" style="margin-right:8px;"></i> Salinas - Operan ({{ $operacionSalinas->count() }})</h3>
            </div>
            <div style="max-height:400px;overflow-y:auto;">
                @forelse($operacionSalinas as $turno)
                <a href="{{ route('gerente.operacion.show', $turno->turno_id) }}" style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f1f5f9;text-decoration:none;">
                    <div style="width:36px;height:36px;background:#dbeafe;color:#1d4ed8;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                        {{ $turno->posicion }}
                    </div>
                    <div style="flex:1;">
                        <p style="margin:0;font-size:13px;font-weight:600;color:#1e293b;">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                        <p style="margin:2px 0 0;font-size:11px;color:#64748b;">{{ $turno->unidad->placa ?? 'Sin unidad' }}</p>
                    </div>
                    @if($turno->es_puntero)
                    <span style="padding:3px 8px;border-radius:12px;font-size:10px;font-weight:600;background:#fef3c7;color:#b45309;">P{{ $turno->posicion }}</span>
                    @endif
                </a>
                @empty
                <div style="padding:30px;text-align:center;">
                    <i class="fas fa-check-circle" style="font-size:32px;color:#e2e8f0;margin-bottom:8px;display:block;"></i>
                    <p style="margin:0;font-size:13px;color:#94a3b8;">Sin operar</p>
                </div>
                @endforelse
            </div>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);padding:14px 20px;">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:white;"><i class="fas fa-bed" style="margin-right:8px;"></i> Guaranda - Descansan ({{ $descansoGuaranda->count() }})</h3>
            </div>
            <div style="max-height:200px;overflow-y:auto;">
                @forelse($descansoGuaranda as $turno)
                <a href="{{ route('gerente.operacion.show', $turno->turno_id) }}" style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f1f5f9;text-decoration:none;">
                    <div style="width:36px;height:36px;background:#ede9fe;color:#7c3aed;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                        {{ $turno->posicion }}
                    </div>
                    <div style="flex:1;">
                        <p style="margin:0;font-size:13px;font-weight:600;color:#1e293b;">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                    </div>
                    <span style="padding:3px 8px;border-radius:12px;font-size:10px;font-weight:600;background:#ede9fe;color:#7c3aed;">D</span>
                </a>
                @empty
                <div style="padding:20px;text-align:center;">
                    <i class="fas fa-bed" style="font-size:24px;color:#e2e8f0;margin-bottom:8px;display:block;"></i>
                    <p style="margin:0;font-size:12px;color:#94a3b8;">Sin descanso</p>
                </div>
                @endforelse
            </div>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);padding:14px 20px;">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:white;"><i class="fas fa-bed" style="margin-right:8px;"></i> Salinas - Descansan ({{ $descansoSalinas->count() }})</h3>
            </div>
            <div style="max-height:200px;overflow-y:auto;">
                @forelse($descansoSalinas as $turno)
                <a href="{{ route('gerente.operacion.show', $turno->turno_id) }}" style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f1f5f9;text-decoration:none;">
                    <div style="width:36px;height:36px;background:#ede9fe;color:#7c3aed;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                        {{ $turno->posicion }}
                    </div>
                    <div style="flex:1;">
                        <p style="margin:0;font-size:13px;font-weight:600;color:#1e293b;">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                    </div>
                    <span style="padding:3px 8px;border-radius:12px;font-size:10px;font-weight:600;background:#ede9fe;color:#7c3aed;">D</span>
                </a>
                @empty
                <div style="padding:20px;text-align:center;">
                    <i class="fas fa-bed" style="font-size:24px;color:#e2e8f0;margin-bottom:8px;display:block;"></i>
                    <p style="margin:0;font-size:12px;color:#94a3b8;">Sin descanso</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection