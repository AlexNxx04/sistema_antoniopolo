@extends('layouts.gerente')

@section('title', 'Notificaciones')
@section('header', 'Notificaciones')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.btn-new { display: inline-flex; align-items: center; gap: 10px; padding: 14px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(5,150,105,0.3); }
.btn-new:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5,150,105,0.4); }
.filter-card { padding: 20px 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.filter-card label { font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px; }
.filter-input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.filter-input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.btn-search { padding: 12px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-search:hover { background: linear-gradient(135deg, #047857, #065f46); }
.btn-clean { padding: 12px 24px; background: #f1f5f9; color: #475569; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; }
.btn-clean:hover { background: #e2e8f0; }
.table-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ecfdf5, #d1fae5); }
.table-header h3 { font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 10px; }
.table-header h3 i { color: #059669; }
.table-row { display: grid; grid-template-columns: 60px 1.5fr 1.2fr 2fr 120px 100px 100px; gap: 16px; padding: 14px 24px; border-bottom: 1px solid #f1f5f9; align-items: center; transition: all 0.2s; }
.table-row:hover { background: linear-gradient(90deg, #ecfdf5, #d1fae5); }
.table-row.no-leido { background: #f0fdf4; }
.badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
.badge-tipo-llamado { background: #dbeafe; color: #1d4ed8; }
.badge-tipo-sancion { background: #fee2e2; color: #dc2626; }
.badge-tipo-documento { background: #fef3c7; color: #b45309; }
.badge-tipo-alerta { background: #d1fae5; color: #047857; }
.badge-leida { background: #d1fae5; color: #047857; }
.badge-nueva { background: #fef3c7; color: #b45309; }
.avatar { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; color: white; flex-shrink: 0; }
.avatar-green { background: linear-gradient(135deg, #10b981, #059669); }
.avatar-blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.action-btn { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-read { background: #dbeafe; color: #2563eb; }
.action-read:hover { background: #2563eb; color: white; }
.action-delete { background: #fee2e2; color: #dc2626; }
.action-delete:hover { background: #dc2626; color: white; }
.vacio { padding: 60px; text-align: center; }
.vacio i { font-size: 56px; color: #d1fae5; margin-bottom: 16px; display: block; }
.vacio p { font-size: 16px; color: #64748b; }
</style>

<div x-data="{ confirmOpen: false, confirmUrl: '', confirmMessage: '' }">
    
    <template x-teleport="body">
        <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" @keydown.escape.window="confirmOpen = false">
            <div x-show="confirmOpen" class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Eliminar Notificación</h3>
                    <p class="text-gray-500 mb-6" x-text="confirmMessage"></p>
                    <div class="flex gap-3">
                        <button @click="confirmOpen = false" class="flex-1 px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">Cancelar</button>
                        <button @click="document.getElementById('deleteForm').submit()" class="flex-1 px-5 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 shadow-lg">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <div class="page-header">
        <div>
            <h2><i class="fas fa-bell"></i>Notificaciones</h2>
            <p>Gestiona las notificaciones del sistema</p>
        </div>
        <a href="{{ route('gerente.notificaciones.create') }}" class="btn-new">
            <i class="fas fa-plus"></i> Nueva Notificación
        </a>
    </div>

    @if(session('success'))
    <div style="margin-bottom:24px;padding:16px 24px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);border-radius:14px;border:1px solid #059669;color:#065f46;font-weight:600;display:flex;align-items:center;gap:12px;">
        <i class="fas fa-check-circle" style="font-size:20px;"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('gerente.notificaciones.index') }}" style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label><i class="fas fa-search mr-2 text-gray-400"></i> Filtrar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por conductor..." class="filter-input">
            </div>
            <div style="width:180px;">
                <label><i class="fas fa-tag mr-2 text-gray-400"></i> Tipo</label>
                <select name="tipo" class="filter-input">
                    <option value="">Todos</option>
                    <option value="llamado_turno" {{ request('tipo') === 'llamado_turno' ? 'selected' : '' }}>Llamado de Turno</option>
                    <option value="sancion" {{ request('tipo') === 'sancion' ? 'selected' : '' }}>Sanción</option>
                    <option value="documento_vencido" {{ request('tipo') === 'documento_vencido' ? 'selected' : '' }}>Documento Vencido</option>
                    <option value="alerta_general" {{ request('tipo') === 'alerta_general' ? 'selected' : '' }}>Alerta General</option>
                </select>
            </div>
            <div style="width:150px;">
                <label><i class="fas fa-toggle-on mr-2 text-gray-400"></i> Estado</label>
                <select name="leido" class="filter-input">
                    <option value="">Todos</option>
                    <option value="si" {{ request('leido') === 'si' ? 'selected' : '' }}>Leídas</option>
                    <option value="no" {{ request('leido') === 'no' ? 'selected' : '' }}>No Leídas</option>
                </select>
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-filter mr-2"></i> Filtrar</button>
            <a href="{{ route('gerente.notificaciones.index') }}" class="btn-clean"><i class="fas fa-times mr-2"></i> Limpiar</a>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h3><i class="fas fa-list"></i> Lista de Notificaciones</h3>
            <span style="font-size:13px;color:#64748b;">{{ $notificaciones->total() }} notificaciones</span>
        </div>
        
        <div style="display:grid;grid-template-columns:60px 1.5fr 1.2fr 2fr 120px 100px 100px;gap:16px;padding:14px 24px;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#059669;">
            <div>#</div>
            <div><i class="fas fa-user mr-2"></i>Destinatario</div>
            <div><i class="fas fa-tag mr-2"></i>Tipo</div>
            <div><i class="fas fa-comment mr-2"></i>Mensaje</div>
            <div><i class="fas fa-clock mr-2"></i>Enviado</div>
            <div><i class="fas fa-toggle-on mr-2"></i>Estado</div>
            <div style="text-align:center;"><i class="fas fa-cogs mr-2"></i>Acción</div>
        </div>

        @forelse($notificaciones as $notif)
        <div class="table-row {{ !$notif->leido ? 'no-leido' : '' }}">
            <div style="color:#94a3b8;font-size:13px;">{{ $loop->iteration + ($notificaciones->currentPage() - 1) * $notificaciones->perPage() }}</div>
            <div>
                @if($notif->usuario)
                <div style="display:flex;align-items:center;gap:12px;">
                    <div class="avatar avatar-{{ $notif->leido ? 'blue' : 'green' }}">
                        {{ substr($notif->usuario->nombres, 0, 1) }}{{ substr($notif->usuario->apellidos, 0, 1) }}
                    </div>
                    <div>
                        <p style="font-weight:600;color:#1e293b;font-size:14px;">{{ $notif->usuario->nombres }} {{ $notif->usuario->apellidos }}</p>
                        <p style="font-size:12px;color:#94a3b8;">{{ $notif->usuario->cedula }}</p>
                    </div>
                </div>
                @else
                <span style="color:#94a3b8;">Sistema</span>
                @endif
            </div>
            <div>
                @php
                    $tipoClase = match($notif->tipo) {
                        'llamado_turno' => 'badge-tipo-llamado',
                        'sancion' => 'badge-tipo-sancion',
                        'documento_vencido' => 'badge-tipo-documento',
                        'alerta_general' => 'badge-tipo-alerta',
                        default => 'badge-tipo-llamado'
                    };
                    $tipoIcono = match($notif->tipo) {
                        'llamado_turno' => 'fa-bell',
                        'sancion' => 'fa-gavel',
                        'documento_vencido' => 'fa-file-excel',
                        'alerta_general' => 'fa-exclamation-circle',
                        default => 'fa-bell'
                    };
                    $tipoTexto = match($notif->tipo) {
                        'llamado_turno' => 'Llamado de Turno',
                        'sancion' => 'Sanción',
                        'documento_vencido' => 'Documento Vencido',
                        'alerta_general' => 'Alerta General',
                        default => 'Notificación'
                    };
                @endphp
                <span class="badge {{ $tipoClase }}">
                    <i class="fas {{ $tipoIcono }}"></i> {{ $tipoTexto }}
                </span>
            </div>
            <div>
                <p style="color:#475569;font-size:13px;line-height:1.4;">{{ Str::limit($notif->mensaje, 60) }}</p>
            </div>
            <div style="color:#64748b;font-size:12px;">{{ $notif->enviado_at?->format('d/m/Y H:i') }}</div>
            <div>
                @if($notif->leido)
                <span class="badge badge-leida">
                    <i class="fas fa-check"></i> Leída
                </span>
                @else
                <span class="badge badge-nueva">
                    <i class="fas fa-envelope"></i> Nueva
                </span>
                @endif
            </div>
            <div style="display:flex;justify-content:center;gap:8px;">
                <form action="{{ route('gerente.notificaciones.leer', $notif->notif_id) }}" method="POST">
                    @csrf
                    <button type="submit" class="action-btn action-read" title="Marcar como leída">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
                <button @click="confirmOpen = true; confirmUrl = '{{ route('gerente.notificaciones.destroy', $notif->notif_id) }}'; confirmMessage = '¿Eliminar esta notificación?'" 
                        class="action-btn action-delete" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
                <form x-bind:action="confirmUrl" method="POST" id="deleteForm" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
        @empty
        <div class="vacio">
            <i class="fas fa-bell"></i>
            <p>No hay notificaciones</p>
        </div>
        @endforelse

        @if($notificaciones->hasPages())
        <div style="padding:20px;border-top:1px solid #e2e8f0;background:#f8fafc;">
            {{ $notificaciones->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
