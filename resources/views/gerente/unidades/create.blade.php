@extends('layouts.gerente')

@section('title', 'Nueva Unidad')
@section('header', 'Nueva Unidad')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header-left { display: flex; align-items: center; gap: 16px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.btn-volver { display: inline-flex; align-items: center; gap: 10px; padding: 12px 20px; background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 12px rgba(5,150,105,0.3); }
.btn-volver:hover { background: linear-gradient(135deg, #047857, #065f46); transform: translateY(-2px); }
.form-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
.form-header { padding: 24px; border-bottom: 1px solid #e2e8f0; background: linear-gradient(135deg, #f8fafc, #f1f5f9); display: flex; align-items: center; gap: 16px; }
.form-header-icon { width: 56px; height: 56px; background: linear-gradient(135deg, #8b5cf6, #6366f1); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; flex-shrink: 0; }
.form-header-text h3 { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
.form-header-text p { font-size: 14px; color: #64748b; }
.form-body { padding: 32px; }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 24px; }
.form-group { }
.form-group.full { grid-column: 1 / -1; }
.form-group label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 10px; }
.form-group label i { color: #8b5cf6; margin-right: 8px; }
.form-input { width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.form-input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.form-select { width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.form-select:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.form-hint { font-size: 12px; color: #94a3b8; margin-top: 8px; display: flex; align-items: center; gap: 6px; }
.form-error { font-size: 12px; color: #dc2626; margin-top: 8px; display: flex; align-items: center; gap: 6px; }
.form-actions { display: flex; justify-content: space-between; align-items: center; padding-top: 24px; margin-top: 24px; border-top: 1px solid #e2e8f0; }
.btn-cancel { display: inline-flex; align-items: center; gap: 8px; padding: 14px 24px; background: #f1f5f9; color: #475569; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; }
.btn-cancel:hover { background: #e2e8f0; }
.btn-save { display: inline-flex; align-items: center; gap: 10px; padding: 14px 32px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 15px rgba(5,150,105,0.3); }
.btn-save:hover { background: linear-gradient(135deg, #047857, #065f46); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5,150,105,0.4); }
.alert-error { margin-bottom: 24px; padding: 24px; background: linear-gradient(135deg, #fee2e2, #fecaca); border: 1px solid #fca5a5; border-radius: 16px; }
.alert-error-title { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; font-weight: 700; color: #991b1b; font-size: 14px; }
.alert-error ul { list-style: disc inside; font-size: 14px; color: #b91c1c; margin: 0; padding-left: 8px; }
.alert-error li { margin-bottom: 4px; }
</style>

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('gerente.unidades.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <div>
            <h2><i class="fas fa-bus"></i>Nueva Unidad</h2>
            <p>Registrar nueva unidad de transporte</p>
        </div>
    </div>
</div>

@if($errors->any())
<div class="alert-error">
    <div class="alert-error-title">
        <i class="fas fa-exclamation-circle"></i>
        Corrija los siguientes errores:
    </div>
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="form-card">
    <div class="form-header">
        <div class="form-header-icon">
            <i class="fas fa-bus"></i>
        </div>
        <div class="form-header-text">
            <h3>Datos de la Unidad</h3>
            <p>Ingrese la información de la nueva unidad</p>
        </div>
    </div>
    
    <form action="{{ route('gerente.unidades.store') }}" method="POST" class="form-body">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-car"></i> Placa <span style="color:#ef4444;">*</span></label>
                <input type="text" id="placa" name="placa" value="{{ old('placa') }}" required class="form-input" placeholder="ABC-1234" style="text-transform:uppercase;">
            </div>

            <div class="form-group">
                <label><i class="fas fa-hashtag"></i> Número Interno <span style="color:#ef4444;">*</span></label>
                <input type="number" id="numero_interno" name="numero_interno" value="{{ old('numero_interno') }}" required class="form-input" placeholder="1">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-mobile-alt"></i> Celular GPS</label>
                <input type="text" id="celular_gps" name="celular_gps" value="{{ old('celular_gps') }}" class="form-input" placeholder="0991234567">
            </div>

            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Punto de Operación</label>
                <select id="punto_id" name="punto_id" class="form-select">
                    <option value="">Seleccionar punto...</option>
                    @foreach($puntos as $punto)
                        <option value="{{ $punto->punto_id }}" {{ old('punto_id') == $punto->punto_id ? 'selected' : '' }}>
                            {{ $punto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('gerente.unidades.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Crear Unidad
            </button>
        </div>
    </form>
</div>
@endsection
