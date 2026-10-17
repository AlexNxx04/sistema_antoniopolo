@extends('layouts.gerente')

@section('title', 'Reporte - Multas')
@section('header', 'Reporte de Multas')

@section('content')
<style>
.btn-volver { padding: 12px 20px; background: #f1f5f9; color: #64748b; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.btn-descargar { padding: 12px 20px; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.stat-card { padding: 20px; border-radius: 16px; color: white; }
.table-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.table-header-row { padding: 14px 24px; background: #f1f5f9; border-bottom: 1px solid #e2e8f0; display: flex; }
.table-cell { flex: 1; font-size: 13px; font-weight: 600; color: #64748b; }
.table-row { padding: 14px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; }
.badge-pendiente { padding: 4px 10px; background: #fef3c7; color: #b45309; border-radius: 8px; font-size: 12px; font-weight: 600; }
.badge-cobrada { padding: 4px 10px; background: #d1fae5; color: #047857; border-radius: 8px; font-size: 12px; font-weight: 600; }
.badge-anulada { padding: 4px 10px; background: #fee2e2; color: #dc2626; border-radius: 8px; font-size: 12px; font-weight: 600; }
</style>

<div style="display:flex;justify-content:space-between;margin-bottom:24px;">
    <a href="{{ route('gerente.reportes.index') }}" class="btn-volver">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
    <a href="{{ route('gerente.reportes.multas', ['formato' => 'pdf', 'fecha_inicio' => $fechaInicio->format('Y-m-d'), 'fecha_fin' => $fechaFin->format('Y-m-d')]) }}" class="btn-descargar">
        <i class="fas fa-download"></i> Generar PDF
    </a>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:24px;">
    <div style="padding:28px;background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:24px;color:white;position:relative;overflow:hidden;box-shadow:0 12px 32px rgba(220,38,38,0.4);">
        <div style="position:absolute;top:-30px;right:-30px;width:100px;height:100px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-30px;left:-30px;width:60px;height:60px;background:rgba(255,255,255,0.1);border-radius:50%;"></div>
        <i class="fas fa-exclamation-circle" style="font-size:36px;display:block;margin-bottom:16px;opacity:0.95;"></i>
        <p style="font-size:48px;font-weight:800;line-height:1;">${{ number_format($totalGeneral, 2) }}</p>
        <p style="font-size:15px;font-weight:600;opacity:0.9;margin-top:6px;">Total Generado</p>
    </div>
    <div style="padding:28px;background:linear-gradient(135deg,#10b981,#059669);border-radius:24px;color:white;position:relative;overflow:hidden;box-shadow:0 12px 32px rgba(5,150,105,0.4);">
        <div style="position:absolute;top:-30px;right:-30px;width:100px;height:100px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-30px;left:-30px;width:60px;height:60px;background:rgba(255,255,255,0.1);border-radius:50%;"></div>
        <i class="fas fa-check-circle" style="font-size:36px;display:block;margin-bottom:16px;opacity:0.95;"></i>
        <p style="font-size:48px;font-weight:800;line-height:1;">${{ number_format($totalCobrado, 2) }}</p>
        <p style="font-size:15px;font-weight:600;opacity:0.9;margin-top:6px;">Total Cobrado</p>
    </div>
    <div style="padding:28px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:24px;color:white;position:relative;overflow:hidden;box-shadow:0 12px 32px rgba(245,158,11,0.4);">
        <div style="position:absolute;top:-30px;right:-30px;width:100px;height:100px;background:rgba(255,255,255,0.15);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-30px;left:-30px;width:60px;height:60px;background:rgba(255,255,255,0.1);border-radius:50%;"></div>
        <i class="fas fa-clock" style="font-size:36px;display:block;margin-bottom:16px;opacity:0.95;"></i>
        <p style="font-size:48px;font-weight:800;line-height:1;">${{ number_format($totalPendiente, 2) }}</p>
        <p style="font-size:15px;font-weight:600;opacity:0.9;margin-top:6px;">Total Pendiente</p>
    </div>
</div>

<div class="table-card">
    <div class="table-header-row">
        <div class="table-cell">#</div>
        <div class="table-cell"><i class="fas fa-calendar"></i> Fecha</div>
        <div class="table-cell"><i class="fas fa-user"></i> Conductor</div>
        <div class="table-cell"><i class="fas fa-tag"></i> Tipo</div>
        <div class="table-cell"><i class="fas fa-dollar-sign"></i> Monto</div>
        <div class="table-cell"><i class="fas fa-toggle-on"></i> Estado</div>
    </div>
    @forelse($multas as $multa)
    <div class="table-row">
        <div class="table-cell">#{{ str_pad($multa->multa_id, 4, '0', STR_PAD_LEFT) }}</div>
        <div class="table-cell">{{ $multa->registrado_en?->format('d/m/Y H:i') ?? '-' }}</div>
        <div class="table-cell">{{ $multa->usuario->nombres ?? 'N/A' }} {{ $multa->usuario->apellidos ?? '' }}</div>
        <div class="table-cell">{{ $multa->tipo_display ?? '-' }}</div>
        <div class="table-cell" style="font-weight:600;">${{ number_format($multa->monto, 2) }}</div>
        <div class="table-cell">
            @switch($multa->estado)
                @case('cobrada')
                    <span class="badge-cobrada">Cobrada</span>
                    @break
                @case('anulada')
                    <span class="badge-anulada">Anulada</span>
                    @break
                @default
                    <span class="badge-pendiente">Pendiente</span>
            @endswitch
        </div>
    </div>
    @empty
    <div style="padding:60px;text-align:center;">
        <p style="color:#64748b;">No hay multas en este período</p>
    </div>
    @endforelse
</div>
@endsection
