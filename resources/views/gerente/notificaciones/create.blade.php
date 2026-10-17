@extends('layouts.gerente')

@section('title', 'Nueva Notificación')
@section('header', 'Nueva Notificación')

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
.form-header-icon { width: 56px; height: 56px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; flex-shrink: 0; }
.form-header-text h3 { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
.form-header-text p { font-size: 14px; color: #64748b; }
.form-body { padding: 32px; }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 24px; }
.form-group { }
.form-group.full { grid-column: 1 / -1; }
.form-group label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 10px; }
.form-group label i { color: #059669; margin-right: 8px; }
.form-input { width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.form-input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.form-select { width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.form-select:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.form-textarea { width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; resize: vertical; min-height: 140px; }
.form-textarea:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.form-hint { font-size: 12px; color: #94a3b8; margin-top: 8px; display: flex; align-items: center; gap: 6px; }
.form-error { font-size: 12px; color: #dc2626; margin-top: 8px; display: flex; align-items: center; gap: 6px; }
.form-actions { display: flex; justify-content: space-between; align-items: center; padding-top: 24px; margin-top: 24px; border-top: 1px solid #e2e8f0; }
.btn-cancel { display: inline-flex; align-items: center; gap: 8px; padding: 14px 24px; background: #f1f5f9; color: #475569; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; }
.btn-cancel:hover { background: #e2e8f0; }
.btn-save { display: inline-flex; align-items: center; gap: 10px; padding: 14px 32px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 15px rgba(5,150,105,0.3); }
.btn-save:hover { background: linear-gradient(135deg, #047857, #065f46); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5,150,105,0.4); }
</style>

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('gerente.notificaciones.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <div>
            <h2><i class="fas fa-bell"></i>Nueva Notificación</h2>
            <p>Envía una notificación a un conductor o usuario del sistema</p>
        </div>
    </div>
</div>

<div class="form-card">
    <div class="form-header">
        <div class="form-header-icon">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="form-header-text">
            <h3>Crear Nueva Notificación</h3>
            <p>Completa los campos para enviar la notificación</p>
        </div>
    </div>
    
    <form action="{{ route('gerente.notificaciones.store') }}" method="POST" class="form-body">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Destinatario</label>
                <select name="usuario_id" id="usuario_id" required class="form-select">
                    <option value="">Seleccione un usuario</option>
                    <optgroup label="Conductores">
                        @foreach($usuarios->where('rol_id', 3) as $usuario)
                            <option value="{{ $usuario->usuario_id }}" {{ old('usuario_id') == $usuario->usuario_id ? 'selected' : '' }}>
                                {{ $usuario->nombres }} {{ $usuario->apellidos }} - {{ $usuario->cedula }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Otros usuarios">
                        @foreach($usuarios->where('rol_id', '!=', 3) as $usuario)
                            <option value="{{ $usuario->usuario_id }}" {{ old('usuario_id') == $usuario->usuario_id ? 'selected' : '' }}>
                                {{ $usuario->nombres }} {{ $usuario->apellidos }} - {{ $usuario->cedula }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                @error('usuario_id')
                    <p class="form-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label><i class="fas fa-tag"></i> Tipo de Notificación</label>
                <select name="tipo" id="tipo" required class="form-select">
                    <option value="">Seleccione el tipo</option>
                    <option value="llamado_turno" {{ old('tipo') == 'llamado_turno' ? 'selected' : '' }}>Llamado de Turno</option>
                    <option value="sancion" {{ old('tipo') == 'sancion' ? 'selected' : '' }}>Sanción</option>
                    <option value="documento_vencido" {{ old('tipo') == 'documento_vencido' ? 'selected' : '' }}>Documento Vencido</option>
                    <option value="alerta_general" {{ old('tipo') == 'alerta_general' ? 'selected' : '' }}>Alerta General</option>
                </select>
                @error('tipo')
                    <p class="form-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-group full">
            <label><i class="fas fa-comment"></i> Mensaje</label>
            <textarea name="mensaje" id="mensaje" rows="4" required maxlength="500" class="form-textarea" placeholder="Escriba el mensaje de la notificación...">{{ old('mensaje') }}</textarea>
            <p class="form-hint"><i class="fas fa-info-circle"></i> Máximo 500 caracteres</p>
            @error('mensaje')
                <p class="form-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
            @enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('gerente.notificaciones.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn-save">
                <i class="fas fa-paper-plane"></i> Enviar Notificación
            </button>
        </div>
    </form>
</div>
@endsection
