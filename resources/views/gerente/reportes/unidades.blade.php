@extends('layouts.gerente')

@section('title', 'Reporte - Unidades')
@section('header', 'Reporte de Unidades')

@section('content')
<style>
.btn-volver { padding: 12px 20px; background: #f1f5f9; color: #64748b; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.btn-descargar { padding: 12px 20px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.table-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.table-header-row { padding: 14px 24px; background: #f1f5f9; border-bottom: 1px solid #e2e8f0; display: flex; }
.table-cell { flex: 1; font-size: 13px; font-weight: 600; color: #64748b; }
.table-row { padding: 14px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; }
.table-row:hover { background: #f8fafc; }
.badge-activo { padding: 4px 10px; background: #d1fae5; color: #047857; border-radius: 8px; font-size: 12px; font-weight: 600; }
.badge-inactivo { padding: 4px 10px; background: #f1f5f9; color: #64748b; border-radius: 8px; font-size: 12px; font-weight: 600; }
</style>

<div style="display:flex;justify-content:space-between;margin-bottom:24px;">
    <a href="{{ route('gerente.reportes.index') }}" class="btn-volver">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
    <a href="{{ route('gerente.reportes.unidades', ['formato' => 'pdf']) }}" class="btn-descargar">
        <i class="fas fa-download"></i> Generar PDF
    </a>
</div>

<div class="table-card">
    <div class="table-header-row">
        <div class="table-cell">#</div>
        <div class="table-cell"><i class="fas fa-bus"></i> Placa</div>
        <div class="table-cell"><i class="fas fa-hashtag"></i> Número</div>
        <div class="table-cell"><i class="fas fa-user"></i> Conductor</div>
        <div class="table-cell"><i class="fas fa-toggle-on"></i> Estado</div>
    </div>
    @forelse($unidades as $unidad)
    <div class="table-row">
        <div class="table-cell">{{ $loop->iteration }}</div>
        <div class="table-cell" style="font-weight:600;color:#1e293b;">{{ $unidad->placa }}</div>
        <div class="table-cell">#{{ str_pad($unidad->numero_interno, 3, '0', STR_PAD_LEFT) }}</div>
        <div class="table-cell">{{ $unidad->conductor?->nombres ?? 'Sin asignar' }}</div>
        <div class="table-cell">
            <span class="{{ $unidad->estado === 'activo' ? 'badge-activo' : 'badge-inactivo' }}">
                {{ ucfirst($unidad->estado) }}
            </span>
        </div>
    </div>
    @empty
    <div style="padding:60px;text-align:center;">
        <p style="color:#64748b;">No hay unidades registradas</p>
    </div>
    @endforelse
</div>
@endsection
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
