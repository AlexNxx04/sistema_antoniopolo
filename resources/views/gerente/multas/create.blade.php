@extends('layouts.gerente')

@section('title', 'Registrar Multa')
@section('header', 'Registrar Multa')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.btn-volver { padding: 12px 20px; background: #f1f5f9; color: #64748b; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.form-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.form-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; background: linear-gradient(135deg, #ecfdf5, #d1fae5); }
.form-header h3 { font-size: 16px; font-weight: 700; color: #1e293b; }
.form-body { padding: 24px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; }
.form-group label span { color: #dc2626; }
.form-input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.form-input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.form-footer { padding: 16px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end; gap: 12px; }
.btn-cancelar { padding: 12px 24px; border: 2px solid #e2e8f0; color: #64748b; border-radius: 12px; font-weight: 600; }
.btn-guardar { padding: 12px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; }
.error { color: #dc2626; font-size: 13px; margin-top: 4px; }
</style>

<div class="fade-in" style="max-width:600px;">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-plus-circle"></i>Registrar Multa</h2>
            <p>Registrar una multa manualmente</p>
        </div>
        <a href="{{ route('gerente.multas.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <form action="{{ route('gerente.multas.store') }}" method="POST">
        @csrf
        <input type="hidden" name="turno_id" value="{{ $turnoId }}">

        <div class="form-card">
            <div class="form-header">
                <h3><i class="fas fa-file-invoice-dollar"></i> Datos de la Multa</h3>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label>Conductor <span>*</span></label>
                    <select name="usuario_id" id="usuario_id" required class="form-input">
                        <option value="">Seleccionar conductor...</option>
                        @foreach($conductores as $conductor)
                            <option value="{{ $conductor->usuario_id }}" {{ old('usuario_id') == $conductor->usuario_id ? 'selected' : '' }}>
                                {{ $conductor->nombres }} {{ $conductor->apellidos }} - {{ $conductor->cedula }}
                            </option>
                        @endforeach
                    </select>
                    @error('usuario_id')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Tipo de Multa <span>*</span></label>
                    <select name="tipo" id="tipo" required class="form-input">
                        <option value="">Seleccionar tipo...</option>
                        <option value="retraso_llegada" {{ old('tipo') == 'retraso_llegada' ? 'selected' : '' }}>Retraso en llegada</option>
                        <option value="ausencia" {{ old('tipo') == 'ausencia' ? 'selected' : '' }}>Ausencia</option>
                        <option value="documento_vencido" {{ old('tipo') == 'documento_vencido' ? 'selected' : '' }}>Documento vencido</option>
                        <option value="indisciplina" {{ old('tipo') == 'indisciplina' ? 'selected' : '' }}>Indisciplina</option>
                    </select>
                    @error('tipo')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Monto <span>*</span></label>
                    <div style="position:relative;max-width:200px;">
                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#64748b;">$</span>
                        <input type="number" name="monto" id="monto" required value="{{ old('monto', 1) }}" min="0.01" step="0.01" class="form-input" style="padding-left:30px;">
                    </div>
                    @error('monto')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group" id="minutos_field">
                    <label>Minutos de Retraso</label>
                    <input type="number" name="minutos_retraso" id="minutos_retraso" value="{{ old('minutos_retraso') }}" min="0" class="form-input" style="max-width:200px;">
                    <p style="font-size:12px;color:#64748b;margin-top:4px;">Opcional. Solo para multas por retraso.</p>
                    @error('minutos_retraso')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3" class="form-input" placeholder="Detalles adicionales de la multa...">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('gerente.multas.index') }}" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-guardar">
                    <i class="fas fa-save"></i> Registrar Multa
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('tipo').addEventListener('change', function() {
    const minutosField = document.getElementById('minutos_field');
    if (this.value === 'retraso_llegada') {
        minutosField.style.display = 'block';
    } else {
        minutosField.style.display = 'none';
    }
});
</script>
@endsection
