@extends('layouts.gerente')

@section('title', 'Reportes')
@section('header', 'Centro de Reportes')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.reporte-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; transition: all 0.3s; position: relative; }
.reporte-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
.reporte-card:hover .reporte-icono { transform: scale(1.1); }
.reporte-banda { height: 6px; }
.reporte-icono { width: 64px; height: 64px; border-radius: 18px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; transition: all 0.3s; }
.reporte-titulo { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.reporte-desc { font-size: 14px; color: #64748b; line-height: 1.5; margin-bottom: 16px; }
.reporte-link { font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; }
.btn-generar { padding: 10px 20px; border-radius: 10px; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
.btn-generar:hover { transform: translateX(4px); }
</style>

<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-chart-bar"></i>Centro de Reportes</h2>
            <p>Seleccione el tipo de reporte que desea generar</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;">
        <div class="reporte-card" style="padding:32px;">
            <div class="reporte-banda" style="background:linear-gradient(90deg,#3b82f6,#2563eb);margin:-32px -32px 0 -32px;"></div>
            <div style="margin-top:20px;">
                <div class="reporte-icono" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);display:inline-flex;">
                    <i class="fas fa-users" style="font-size:28px;color:#2563eb;"></i>
                </div>
            </div>
            <h3 class="reporte-titulo">Reporte por Conductores</h3>
            <p class="reporte-desc">Listado completo de conductores con su estado, documentos y asignaciones actuales.</p>
            <a href="{{ route('gerente.reportes.conductores', ['formato' => 'pdf']) }}" style="padding:12px 24px;background:linear-gradient(135deg,#3b82f6,#2563eb); border-radius:12px;color:white;font-weight:600;display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                <i class="fas fa-file-pdf"></i> Generar PDF
            </a>
        </div>

        <div class="reporte-card" style="padding:32px;">
            <div class="reporte-banda" style="background:linear-gradient(90deg,#8b5cf6,#7c3aed);margin:-32px -32px 0 -32px;"></div>
            <div style="margin-top:20px;">
                <div class="reporte-icono" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:inline-flex;">
                    <i class="fas fa-bus" style="font-size:28px;color:#7c3aed;"></i>
                </div>
            </div>
            <h3 class="reporte-titulo">Reporte por Unidades</h3>
            <p class="reporte-desc">Estado actual de la flota, unidades activas y disponibles.</p>
            <a href="{{ route('gerente.reportes.unidades', ['formato' => 'pdf']) }}" style="padding:12px 24px;background:linear-gradient(135deg,#8b5cf6,#7c3aed); border-radius:12px;color:white;font-weight:600;display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                <i class="fas fa-file-pdf"></i> Generar PDF
            </a>
        </div>

        <div class="reporte-card" style="padding:32px;">
            <div class="reporte-banda" style="background:linear-gradient(90deg,#ef4444,#dc2626);margin:-32px -32px 0 -32px;"></div>
            <div style="margin-top:20px;">
                <div class="reporte-icono" style="background:linear-gradient(135deg,#fee2e2,#fecaca);display:inline-flex;">
                    <i class="fas fa-exclamation-triangle" style="font-size:28px;color:#dc2626;"></i>
                </div>
            </div>
            <h3 class="reporte-titulo">Reporte por Multas</h3>
            <p class="reporte-desc">Resumen de sanciones generadas, cobradas y pendientes.</p>
            <a href="{{ route('gerente.reportes.multas', ['formato' => 'pdf']) }}" style="padding:12px 24px;background:linear-gradient(135deg,#ef4444,#dc2626); border-radius:12px;color:white;font-weight:600;display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                <i class="fas fa-file-pdf"></i> Generar PDF
            </a>
        </div>
    </div>
</div>
@endsection