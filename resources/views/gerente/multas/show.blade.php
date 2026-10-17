@extends('layouts.gerente')

@section('title', 'Detalle Multa')
@section('header', 'Detalle de Multa')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.btn-volver { padding: 12px 20px; background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.info-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.info-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; }
.info-header h3 { font-size: 16px; font-weight: 700; color: #1e293b; }
.info-row { padding: 14px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
.info-row:last-child { border-bottom: none; }
.info-label { color: #64748b; font-size: 14px; }
.info-value { color: #1e293b; font-weight: 600; }
.monto { font-size: 24px; font-weight: 700; color: #059669; }
.badge-estado { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; }
.descripcion { padding: 16px 24px; color: #374151; font-size: 14px; white-space: pre-line; }
</style>

<div class="fade-in">
    <a href="{{ route('gerente.multas.index') }}" class="btn-volver" style="margin-bottom:20px;">
        <i class="fas fa-arrow-left"></i> Volver
    </a>

    <div class="page-header">
        <div>
            <h2><i class="fas fa-eye"></i>Detalle de Multa</h2>
            <p>Información completa #{{ str_pad($multa->multa_id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;">
        <div class="info-card">
            <div class="info-header" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                <h3><i class="fas fa-file-invoice-dollar"></i> Información de la Multa</h3>
            </div>
            <div class="info-row">
                <span class="info-label">Estado</span>
                <span class="badge-estado {{ $multa->estado_badge_class }}">{{ $multa->estado_display }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo</span>
                <span class="info-value">{{ $multa->tipo_display }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Monto</span>
                <span class="monto">${{ number_format($multa->monto, 2) }}</span>
            </div>
            @if($multa->minutos_retraso)
            <div class="info-row">
                <span class="info-label">Minutos de Retraso</span>
                <span class="info-value">{{ $multa->minutos_retraso }} min</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Fecha Registro</span>
                <span class="info-value">{{ $multa->registrado_en?->format('d/m/Y H:i') ?? '-' }}</span>
            </div>
            @if($multa->cobrado_en)
            <div class="info-row">
                <span class="info-label">Fecha Cobro</span>
                <span class="info-value">{{ $multa->cobrado_en?->format('d/m/Y H:i') }}</span>
            </div>
            @endif
        </div>

        <div class="info-card">
            <div class="info-header" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
                <h3><i class="fas fa-user"></i> Conductor y Turno</h3>
            </div>
            <div class="info-row">
                <span class="info-label">Conductor</span>
                <span class="info-value">{{ $multa->usuario->nombres ?? 'N/A' }} {{ $multa->usuario->apellidos ?? '' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cédula</span>
                <span class="info-value">{{ $multa->usuario->cedula ?? '-' }}</span>
            </div>
            @if($multa->turno)
            <div class="info-row">
                <span class="info-label">Fecha Turno</span>
                <span class="info-value">{{ $multa->turno->fecha?->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Unidad</span>
                <span class="info-value">{{ $multa->turno->unidad?->placa ?? 'N/A' }}</span>
            </div>
            @endif
        </div>

        @if($multa->descripcion)
        <div class="info-card" style="grid-column:1/-1;">
            <div class="info-header" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
                <h3><i class="fas fa-align-left"></i> Descripción</h3>
            </div>
            <div class="descripcion">{{ $multa->descripcion }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
