@extends('layouts.gerente')

@section('title', 'Historial de Turnos')
@section('header', 'Historial de Turnos')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.btn-volver { padding: 12px 20px; background: #f1f5f9; color: #64748b; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
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
.table-header h3 { font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 10px; }
.table-header h3 i { color: #059669; }
.table-cell { flex: 1; font-size: 13px; font-weight: 600; color: #059669; min-width: 0; }
.table-cell:last-child { flex: 0.8; }
.table-row { padding: 14px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; transition: all 0.2s; }
.table-row:hover { background: linear-gradient(90deg, #ecfdf5, #d1fae5); }
.badge { padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; }
.badge-punto { background: #dbeafe; color: #1d4ed8; }
.badge-asignado { background: #dbeafe; color: #1d4ed8; }
.badge-en_curso { background: #fef3c7; color: #b45309; }
.badge-completado { background: #d1fae5; color: #047857; }
.badge-cancelado { background: #fee2e2; color: #dc2626; }
.vacio { padding: 60px; text-align: center; }
.vacio i { font-size: 56px; color: #d1fae5; margin-bottom: 16px; display: block; }
.vacio p { font-size: 16px; color: #64748b; }
</style>

<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-history"></i>Historial de Turnos</h2>
            <p>Consulta el registro histórico de sorteos</p>
        </div>
        <a href="{{ route('gerente.sorteo.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="filter-card">
        <form method="GET" action="{{ route('gerente.sorteo.historial') }}" style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;"><i class="fas fa-calendar mr-2 text-gray-400"></i> Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="filter-input">
            </div>
            <div style="flex:1;min-width:200px;">
                <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;"><i class="fas fa-calendar mr-2 text-gray-400"></i> Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="filter-input">
            </div>
            <div style="flex:1;min-width:200px;">
                <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i> Punto</label>
                <select name="punto_id" class="filter-input">
                    <option value="">Todos</option>
                    @foreach($puntos as $punto)
                        <option value="{{ $punto->punto_id }}" {{ request('punto_id') == $punto->punto_id ? 'selected' : '' }}>{{ $punto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:200px;">
                <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;"><i class="fas fa-toggle-on mr-2 text-gray-400"></i> Estado</label>
                <select name="estado" class="filter-input">
                    <option value="">Todos</option>
                    <option value="asignado" {{ request('estado') === 'asignado' ? 'selected' : '' }}>Asignado</option>
                    <option value="en_curso" {{ request('estado') === 'en_curso' ? 'selected' : '' }}>En Curso</option>
                    <option value="completado" {{ request('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
                    <option value="cancelado" {{ request('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <button type="submit" class="btn-filtrar"><i class="fas fa-filter mr-2"></i> Filtrar</button>
            <a href="{{ route('gerente.sorteo.historial') }}" class="btn-limpiar"><i class="fas fa-times mr-2"></i> Limpiar</a>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h3><i class="fas fa-history"></i> Historial de Turnos</h3>
            <span style="font-size:13px;color:#64748b;">{{ $turnos->total() }} registros</span>
        </div>
        <div style="display:flex;padding:14px 24px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
            <div class="table-cell"><i class="fas fa-calendar"></i> Fecha</div>
            <div class="table-cell"><i class="fas fa-map-marker-alt"></i> Punto</div>
            <div class="table-cell"><i class="fas fa-sort-numeric-up"></i> Orden</div>
            <div class="table-cell"><i class="fas fa-user"></i> Conductor</div>
            <div class="table-cell"><i class="fas fa-bus"></i> Unidad</div>
            <div class="table-cell"><i class="fas fa-toggle-on"></i> Estado</div>
        </div>
        @forelse($turnos as $turno)
        <div class="table-row">
            <div class="table-cell">{{ $turno->fecha->format('d/m/Y') }}</div>
            <div class="table-cell">
                @if($turno->punto)
                    <span class="badge badge-punto">{{ $turno->punto->nombre }}</span>
                @else
                    <span style="color:#94a3b8;">N/A</span>
                @endif
            </div>
            <div class="table-cell">
                <span style="width:28px;height:28px;background:#dbeafe;color:#1d4ed8;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                    {{ $turno->orden }}
                </span>
                @if($turno->es_descanso == 1)
                <span style="display:block;font-size:10px;color:#7c3aed;font-weight:600;">DESCANSO</span>
                @elseif($turno->es_puntero == 1)
                <span style="display:block;font-size:10px;color:#7c3aed;font-weight:600;">PUNTERO {{ $turno->posicion <= 2 ? $turno->posicion : '' }}</span>
                @endif
            </div>
            <div class="table-cell">
                @if($turno->usuario)
                    <span style="font-weight:500;color:#1e293b;">{{ $turno->usuario->nombres }} {{ $turno->usuario->apellidos }}</span>
                    <small style="display:block;color:#64748b;font-size:12px;">{{ $turno->usuario->cedula }}</small>
                @else
                    <span style="color:#94a3b8;">Sin asignar</span>
                @endif
            </div>
            <div class="table-cell">
                @if($turno->unidad)
                    <span style="font-weight:500;font-family:monospace;">{{ $turno->unidad->placa }}</span>
                @else
                    <span style="color:#94a3b8;">N/A</span>
                @endif
            </div>
            <div class="table-cell">
                @switch($turno->estado)
                    @case('asignado')
                        <span class="badge badge-asignado">Asignado</span>
                        @break
                    @case('en_curso')
                        <span class="badge badge-en_curso">En Curso</span>
                        @break
                    @case('completado')
                        <span class="badge badge-completado">Completado</span>
                        @break
                    @case('cancelado')
                        <span class="badge badge-cancelado">Cancelado</span>
                        @break
                @endswitch
            </div>
        </div>
        @empty
        <div class="vacio">
            <i class="fas fa-inbox" style="font-size:48px;color:#d1fae5;margin-bottom:16px;display:block;"></i>
            <p>No se encontraron registros</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
