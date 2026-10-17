@extends('layouts.gerente')

@section('title', 'Auditoría')
@section('header', 'Historial de Cambios')

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
.btn-clean { padding: 12px 24px; background: #f1f5f9; color: #475569; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-clean:hover { background: #e2e8f0; }
.btn-exportar { padding: 12px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 15px rgba(5,150,105,0.3); }
.btn-exportar:hover { background: linear-gradient(135deg, #047857, #065f46); transform: translateY(-2px); }
.table-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ecfdf5, #d1fae5); }
.table-header h3 { font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 10px; }
.table-header h3 i { color: #059669; }
.table-row { display: flex; align-items: center; padding: 14px 24px; border-bottom: 1px solid #f1f5f9; transition: all 0.2s; }
.table-row:hover { background: linear-gradient(90deg, #ecfdf5, #d1fae5); }
.table-row:last-child { border-bottom: none; }
.table-cell { flex: 1; min-width: 0; }
.table-cell:nth-child(2) { flex: 0.5; }
.table-cell:nth-child(6) { flex: 0.8; }
.table-cell small { display: block; font-size: 11px; color: #94a3b8; margin-top: 2px; }
.badge { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; }
.badge-tabla { background: #dbeafe; color: #1d4ed8; }
.badge-crear { background: #d1fae5; color: #047857; }
.badge-modificar { background: #fef3c7; color: #b45309; }
.badge-eliminar { background: #fee2e2; color: #dc2626; }
.avatar { width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 12px; }
.vacio { padding: 60px; text-align: center; }
.vacio i { font-size: 56px; color: #d1fae5; margin-bottom: 16px; }
.vacio p { font-size: 16px; color: #64748b; }
</style>

<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-history"></i>Auditoría del Sistema</h2>
            <p>Historial completo de modificaciones</p>
        </div>
        <form action="{{ route('gerente.auditoria.exportar') }}" method="GET" target="_blank">
            @if(request()->has('fecha_desde'))
                <input type="hidden" name="fecha_desde" value="{{ request('fecha_desde') }}">
            @endif
            @if(request()->has('fecha_hasta'))
                <input type="hidden" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
            @endif
            <button type="submit" class="btn-exportar">
                <i class="fas fa-file-csv mr-2"></i> Exportar CSV
            </button>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:24px;">
        <div style="padding:24px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(59,130,246,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-history" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $cambios->total() }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">Total</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#10b981,#059669);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(5,150,105,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-plus-circle" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $cambios->where('accion', 'crear')->count() }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">Creaciones</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(245,158,11,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-edit" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $cambios->where('accion', 'modificar')->count() }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">Modificaciones</p>
        </div>
        <div style="padding:24px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:20px;color:white;box-shadow:0 10px 25px rgba(139,92,246,0.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
            <i class="fas fa-trash-alt" style="font-size:32px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:42px;font-weight:800;line-height:1;">{{ $cambios->where('accion', 'eliminar')->count() }}</p>
            <p style="font-size:14px;font-weight:600;opacity:0.9;margin-top:4px;">Eliminaciones</p>
        </div>
    </div>

    <div class="filter-card">
        <form method="GET" action="{{ route('gerente.auditoria.index') }}" style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;"><i class="fas fa-table mr-2 text-gray-400"></i> Tabla</label>
                <select name="tabla" class="filter-input">
                    <option value="">Todas</option>
                    <option value="usuarios" {{ request('tabla') == 'usuarios' ? 'selected' : '' }}>Usuarios</option>
                    <option value="unidades" {{ request('tabla') == 'unidades' ? 'selected' : '' }}>Unidades</option>
                    <option value="puntos" {{ request('tabla') == 'puntos' ? 'selected' : '' }}>Puntos</option>
                    <option value="ciclos_anuales" {{ request('tabla') == 'ciclos_anuales' ? 'selected' : '' }}>Ciclos</option>
                    <option value="parametros" {{ request('tabla') == 'parametros' ? 'selected' : '' }}>Parámetros</option>
                    <option value="turnos" {{ request('tabla') == 'turnos' ? 'selected' : '' }}>Turnos</option>
                    <option value="multas" {{ request('tabla') == 'multas' ? 'selected' : '' }}>Multas</option>
                    <option value="sorteos" {{ request('tabla') == 'sorteos' ? 'selected' : '' }}>Sorteos</option>
                    <option value="documentos" {{ request('tabla') == 'documentos' ? 'selected' : '' }}>Documentos</option>
                </select>
            </div>
            <div style="flex:1;min-width:200px;">
                <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;"><i class="fas fa-user mr-2 text-gray-400"></i> Usuario</label>
                <select name="usuario_id" class="filter-input">
                    <option value="">Todos</option>
                    @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->usuario_id }}" {{ request('usuario_id') == $usuario->usuario_id ? 'selected' : '' }}>
                        {{ $usuario->nombres }} {{ $usuario->apellidos }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:180px;">
                <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;"><i class="fas fa-cog mr-2 text-gray-400"></i> Acción</label>
                <select name="accion" class="filter-input">
                    <option value="">Todas</option>
                    <option value="crear" {{ request('accion') == 'crear' ? 'selected' : '' }}>Crear</option>
                    <option value="modificar" {{ request('accion') == 'modificar' ? 'selected' : '' }}>Modificar</option>
                    <option value="eliminar" {{ request('accion') == 'eliminar' ? 'selected' : '' }}>Eliminar</option>
                </select>
            </div>
            <div style="flex:1;min-width:200px;">
                <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;"><i class="fas fa-calendar mr-2 text-gray-400"></i> Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="filter-input">
            </div>
            <div style="flex:1;min-width:200px;">
                <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;"><i class="fas fa-calendar mr-2 text-gray-400"></i> Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="filter-input">
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-filter mr-2"></i> Filtrar</button>
            <a href="{{ route('gerente.auditoria.index') }}" class="btn-clean"><i class="fas fa-times mr-2"></i> Limpiar</a>
        </form>
    </div>

    <div class="table-card" style="overflow:hidden;">
        <div class="table-header">
            <h3><i class="fas fa-history"></i> Historial de Cambios</h3>
            <span style="font-size:13px;color:#64748b;">{{ $cambios->total() }} registros</span>
        </div>
        <div style="display:grid;grid-template-columns:100px 60px 1.2fr 0.8fr 1.8fr 100px;gap:16px;padding:14px 24px;background:#f8fafc;border-bottom:1px solid #e2e8f0;overflow:hidden;">
            <div style="font-size:13px;font-weight:600;color:#059669;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fas fa-table mr-2"></i>Tabla</div>
            <div style="font-size:13px;font-weight:600;color:#059669;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fas fa-hashtag mr-2"></i>ID</div>
            <div style="font-size:13px;font-weight:600;color:#059669;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fas fa-user mr-2"></i>Usuario</div>
            <div style="font-size:13px;font-weight:600;color:#059669;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fas fa-cog mr-2"></i>Acción</div>
            <div style="font-size:13px;font-weight:600;color:#059669;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fas fa-edit mr-2"></i>Cambio</div>
            <div style="font-size:13px;font-weight:600;color:#059669;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fas fa-clock mr-2"></i>Fecha</div>
        </div>
        <div style="overflow:auto;max-height:600px;">
        @forelse($cambios as $cambio)
        <div style="display:grid;grid-template-columns:100px 60px 1.2fr 0.8fr 1.8fr 100px;gap:16px;padding:14px 24px;border-bottom:1px solid #f1f5f9;align-items:center;transition:all 0.2s;overflow:hidden;" onmouseover="this.style.background='linear-gradient(90deg,#ecfdf5,#d1fae5)'" onmouseout="this.style.background='transparent'">
            <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="badge badge-tabla">{{ $cambio->tabla_afectada }}</span></div>
            <div style="font-family:monospace;color:#64748b;font-size:14px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">#{{ $cambio->registro_id }}</div>
            <div style="display:flex;align-items:center;gap:10px;overflow:hidden;">
                <div style="width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;flex-shrink:0;">{{ substr($cambio->usuario->nombres ?? 'U', 0, 1) }}</div>
                <span style="font-weight:600;color:#1e293b;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cambio->usuario->nombres ?? 'N/A' }}</span>
            </div>
            <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                @switch($cambio->accion)
                    @case('crear')
                        <span class="badge badge-crear"><i class="fas fa-plus mr-1"></i> Crear</span>
                        @break
                    @case('modificar')
                        <span class="badge badge-modificar"><i class="fas fa-edit mr-1"></i> Modificar</span>
                        @break
                    @case('eliminar')
                        <span class="badge badge-eliminar"><i class="fas fa-trash mr-1"></i> Eliminar</span>
                        @break
                @endswitch
            </div>
            <div style="font-size:12px;line-height:1.4;overflow:hidden;text-overflow:ellipsis;">
                @if($cambio->accion == 'crear')
                    <div style="color:#047857;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cambio->campo_modificado }}</div>
                    <div style="color:#047857;opacity:0.8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cambio->valor_anterior ?? '-' }}</div>
                @elseif($cambio->accion == 'eliminar')
                    <div style="color:#dc2626;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cambio->campo_modificado }}</div>
                    <div style="color:#dc2626;opacity:0.8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cambio->valor_anterior ?? '-' }}</div>
                @else
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <span style="color:#dc2626;background:#fee2e2;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cambio->valor_anterior ?? '-' }}</span>
                        <i class="fas fa-arrow-right" style="color:#94a3b8;font-size:10px;"></i>
                        <span style="color:#047857;background:#d1fae5;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cambio->valor_nuevo ?? '-' }}</span>
                    </div>
                @endif
            </div>
            <div style="color:#64748b;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cambio->timestamp?->format('d/m/Y') }}</div>
        </div>
        @empty
        <div class="vacio">
            <i class="fas fa-history" style="font-size:56px;color:#d1fae5;margin-bottom:16px;display:block;"></i>
            <p style="font-size:16px;color:#64748b;">No hay registros de auditoría</p>
        </div>
        @endforelse
        </div>
    </div>
</div>
@endsection