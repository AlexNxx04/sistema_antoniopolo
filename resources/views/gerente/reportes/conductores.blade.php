@extends('layouts.gerente')

@section('title', 'Reporte de Conductores')
@section('header', 'Reporte de Conductores')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.btn-volver { padding: 12px 20px; background: #f1f5f9; color: #64748b; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.btn-descargar { padding: 12px 20px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.table-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.table-header-row { padding: 14px 24px; background: #f1f5f9; border-bottom: 1px solid #e2e8f0; display: flex; }
.table-cell { flex: 1; font-size: 13px; font-weight: 600; color: #64748b; }
.table-row { padding: 14px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; }
.table-row:hover { background: #f8fafc; }
.avatar { width: 40px; height: 40px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #059669; font-weight: 700; font-size: 14px; }
.badge { padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; }
.badge-verde { background: #d1fae5; color: #047857; }
.badge-rojo { background: #fee2e2; color: #dc2626; }
.badge-amber { background: #fef3c7; color: #b45309; }
</style>

<div style="display:flex;justify-content:space-between;margin-bottom:24px;">
    <a href="{{ route('gerente.reportes.index') }}" class="btn-volver">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
    <a href="{{ route('gerente.reportes.conductores', ['formato' => 'pdf']) }}" class="btn-descargar">
        <i class="fas fa-download"></i> Generar PDF
    </a>
</div>

<div class="table-card">
    <div class="table-header-row">
        <div class="table-cell"><i class="fas fa-user"></i> Conductor</div>
        <div class="table-cell"><i class="fas fa-id-card"></i> Cédula</div>
        <div class="table-cell"><i class="fas fa-clock"></i> Turno Hoy</div>
        <div class="table-cell"><i class="fas fa-file-alt"></i> Documentos</div>
        <div class="table-cell" style="text-align:center;"><i class="fas fa-cogs"></i> Acciones</div>
    </div>
    @forelse($conductores as $conductor)
    <div class="table-row">
        <div class="table-cell" style="display:flex;align-items:center;gap:12px;">
            <div class="avatar">{{ substr($conductor->nombres, 0, 1) }}{{ substr($conductor->apellidos, 0, 1) }}</div>
            <span style="font-weight:500;color:#1e293b;">{{ $conductor->nombres }} {{ $conductor->apellidos }}</span>
        </div>
        <div class="table-cell" style="color:#64748b;">{{ $conductor->cedula }}</div>
        <div class="table-cell">
            @if($conductor->turnos->count() > 0)
                @php $turno = $conductor->turnos->first(); @endphp
                <span style="font-weight:500;color:#1e293b;">{{ $turno->punto->nombre ?? 'N/A' }}</span>
                @if($turno->es_descanso)
                    <span class="badge badge-amber" style="margin-left:8px;">Descanso</span>
                @endif
            @else
                <span style="color:#94a3b8;">Sin turno</span>
            @endif
        </div>
        <div class="table-cell">
            @if(isset($documentosProximos[$conductor->usuario_id]) && $documentosProximos[$conductor->usuario_id]->count() > 0)
                @foreach($documentosProximos[$conductor->usuario_id] as $doc)
                    <span class="badge badge-rojo" style="margin-right:4px;">{{ ucfirst($doc->tipo) }}</span>
                @endforeach
            @else
                <span class="badge badge-verde">Al día</span>
            @endif
        </div>
        <div class="table-cell" style="text-align:center;">
            <a href="{{ route('gerente.usuarios.show', $conductor->usuario_id) }}" style="color:#059669;font-weight:500;font-size:13px;">
                Ver perfil
            </a>
        </div>
    </div>
    @empty
    <div style="padding:60px;text-align:center;">
        <p style="color:#64748b;">No hay conductores registrados</p>
    </div>
    @endforelse
</div>
@endsection
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-500">No hay conductores registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
