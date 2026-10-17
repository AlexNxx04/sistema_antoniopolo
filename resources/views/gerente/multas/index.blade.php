@extends('layouts.gerente')

@section('title', 'Multas')
@section('header', 'Multas por Retraso')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.stat-card { background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 20px; }
.filter-card { padding: 20px 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.filter-card label { font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px; }
.filter-input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.filter-input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.btn-filtrar { padding: 12px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-filtrar:hover { background: linear-gradient(135deg, #047857, #065f46); }
.btn-limpiar { padding: 12px 24px; background: #f1f5f9; color: #475569; border-radius: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.2s; }
.btn-limpiar:hover { background: #e2e8f0; }
.table-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ecfdf5, #d1fae5); }
.table-header h3 { font-size: 16px; font-weight: 700; color: #1e293b; }
.table-row { display: flex; align-items: center; padding: 14px 24px; border-bottom: 1px solid #f1f5f9; }
.table-row:hover { background: #f8fafc; }
.table-cell { flex: 1; }
.table-cell small { display: block; font-size: 11px; color: #94a3b8; margin-top: 2px; }
.badge-pendiente { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #fef3c7; color: #b45309; }
.badge-cobrada { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #d1fae5; color: #047857; }
.badge-anulada { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #fee2e2; color: #dc2626; }
.btn-ver { padding: 10px 16px; background: #3b82f6; color: white; border-radius: 10px; font-size: 13px; }
.vacio { padding: 60px; text-align: center; }
.vacio i { font-size: 56px; color: #d1fae5; margin-bottom: 16px; }
.vacio p { font-size: 16px; color: #64748b; }
</style>

<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-file-invoice-dollar"></i>Multas por Retraso</h2>
            <p>Visualización de multas registradas</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:24px;">
        <div style="padding:24px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(245,158,11,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-clock" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $estadisticas['pendientes'] }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">Pendientes</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#10b981,#059669);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(5,150,105,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-check-circle" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">${{ number_format($estadisticas['cobradas'], 2) }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">Total Cobrado</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(59,130,246,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-file-invoice-dollar" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $estadisticas['total'] }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">Total Registradas</p>
        </div>
    </div>

    <div class="filter-card">
        <form method="GET" action="{{ route('gerente.multas.index') }}" style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label><i class="fas fa-user mr-2 text-gray-400"></i> Conductor</label>
                <select name="usuario_id" class="filter-input">
                    <option value="">Todos</option>
                    @foreach($conductores as $conductor)
                        <option value="{{ $conductor->usuario_id }}" {{ $usuarioId == $conductor->usuario_id ? 'selected' : '' }}>
                            {{ $conductor->nombres }} {{ $conductor->apellidos }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="width:150px;">
                <label><i class="fas fa-toggle-on mr-2 text-gray-400"></i> Estado</label>
                <select name="estado" class="filter-input">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ $estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="cobrada" {{ $estado == 'cobrada' ? 'selected' : '' }}>Cobrada</option>
                    <option value="anulada" {{ $estado == 'anulada' ? 'selected' : '' }}>Anulada</option>
                </select>
            </div>
            <div style="width:150px;">
                <label><i class="fas fa-calendar mr-2 text-gray-400"></i> Desde</label>
                <input type="date" name="fecha_desde" value="{{ $fechaDesde?->format('Y-m-d') }}" class="filter-input">
            </div>
            <div style="width:150px;">
                <label><i class="fas fa-calendar mr-2 text-gray-400"></i> Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ $fechaHasta?->format('Y-m-d') }}" class="filter-input">
            </div>
            <button type="submit" class="btn-filtrar"><i class="fas fa-filter mr-2"></i> Filtrar</button>
            <a href="{{ route('gerente.multas.index') }}" class="btn-limpiar"><i class="fas fa-times mr-2"></i> Limpiar</a>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h3><i class="fas fa-list"></i> Lista de Multas</h3>
        </div>
        <div style="display:flex;padding:12px 24px;background:#f1f5f9;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#64748b;">
            <div class="table-cell"><i class="fas fa-user"></i> Conductor</div>
            <div class="table-cell"><i class="fas fa-tag"></i> Tipo</div>
            <div class="table-cell"><i class="fas fa-dollar-sign"></i> Monto</div>
            <div class="table-cell"><i class="fas fa-toggle-on"></i> Estado</div>
            <div class="table-cell"><i class="fas fa-calendar"></i> Fecha</div>
            <div class="table-cell" style="text-align:center;"><i class="fas fa-cogs"></i> Acciones</div>
        </div>
        @forelse($multas as $multa)
        <div class="table-row">
            <div class="table-cell">
                <p style="font-weight:600;color:#1e293b;">{{ $multa->usuario->nombres ?? 'N/A' }} {{ $multa->usuario->apellidos ?? '' }}</p>
                <small>{{ $multa->usuario->cedula ?? '' }}</small>
            </div>
            <div class="table-cell">
                <span style="padding:4px 10px;background:#f1f5f9;color:#475569;border-radius:8px;font-size:12px;font-weight:600;">
                    {{ ucfirst(str_replace('_', ' ', $multa->tipo)) }}
                </span>
            </div>
            <div class="table-cell">
                <p style="font-weight:700;color:#1e293b;">${{ number_format($multa->monto, 2) }}</p>
            </div>
            <div class="table-cell">
                @switch($multa->estado)
                    @case('pendiente')
                        <span class="badge-pendiente"><i class="fas fa-clock"></i> Pendiente</span>
                        @break
                    @case('cobrada')
                        <span class="badge-cobrada"><i class="fas fa-check-circle"></i> Cobrada</span>
                        @break
                    @case('anulada')
                        <span class="badge-anulada"><i class="fas fa-times-circle"></i> Anulada</span>
                        @break
                @endswitch
            </div>
            <div class="table-cell">
                <p style="color:#64748b;">{{ $multa->registrado_en?->format('d/m/Y') }}</p>
            </div>
            <div class="table-cell" style="text-align:center;">
                <a href="{{ route('gerente.multas.show', $multa->multa_id) }}" class="btn-ver">
                    <i class="fas fa-eye"></i> Ver
                </a>
            </div>
        </div>
        @empty
        <div class="vacio">
            <i class="fas fa-file-invoice-dollar"></i>
            <p>No hay multas registradas</p>
        </div>
        @endforelse
    </div>
</div>
@endsection