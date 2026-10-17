@extends('layouts.conductor')

@section('title', 'Justificativos')
@section('header', 'Mis Justificativos')

@section('content')
<style>
.form-card { background: white; border-radius: 1.5rem; box-shadow: 0 4px 15px rgba(5, 150, 105, 0.1); border: 1px solid #d1fae5; }
.form-input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; transition: all 0.2s; }
.form-input:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
.form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
</style>

<div class="mb-6">
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white shadow-lg">
            <i class="fas fa-file-alt text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Justificativos</h2>
            <p class="text-gray-500 text-sm">Registra y consulta tus justificativos</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-1">
        <div class="form-card p-5">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-plus-circle text-emerald-500"></i>
                Nuevo Justificativo
            </h3>
            
            @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('conductor.justificativos.crear') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label">Turno</label>
                    <select name="turno_id" class="form-input" required>
                        <option value="">Seleccionar turno...</option>
                        @foreach($turnosDisponibles ?? [] as $t)
                        <option value="{{ $t->turno_id }}">
                            {{ $t->fecha->format('d/m/Y') }} - Posición #{{ $t->posicion }}
                            {{ $t->es_descanso ? '(Descanso)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Tipo de Justificativo</label>
                    <select name="tipo" class="form-input" required>
                        <option value="atraso">Atraso</option>
                        <option value="inasistencia">Inasistencia</option>
                        <option value="sustitucion">Sustitución de manejo</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Motivo</label>
                    <textarea name="motivo" class="form-input" rows="3" placeholder="Describe el motivo del justificativo..." required></textarea>
                </div>

                <div id="sustituto-section" class="hidden">
                    <div class="mb-4">
                        <label class="form-label">Nombre del Sustituto</label>
                        <input type="text" name="sustituto_nombre" class="form-input" placeholder="Nombre completo">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Cédula del Sustituto</label>
                        <input type="text" name="sustituto_cedula" class="form-input" placeholder="Número de cédula">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Relación/Parentesco</label>
                        <input type="text" name="sustituto_relacion" class="form-input" placeholder="Ej: Hermano, Amigo, etc.">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Enviar Justificativo
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="dashboard-card">
            <div class="section-header">
                <h3><i class="fas fa-history text-emerald-500 mr-2"></i>Mis Justificativos</h3>
            </div>
            
            @if($justificativos->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($justificativos as $j)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg 
                                @if($j->tipo == 'atraso') bg-amber-100 text-amber-600
                                @elseif($j->tipo == 'inasistencia') bg-red-100 text-red-600
                                @else bg-blue-100 text-blue-600 @endif 
                                flex items-center justify-center">
                                <i class="fas 
                                    @if($j->tipo == 'atraso') fa-clock
                                    @elseif($j->tipo == 'inasistencia') fa-user-times
                                    @else fa-exchange-alt @endif"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    @if($j->tipo == 'atraso') Atraso
                                    @elseif($j->tipo == 'inasistencia') Inasistencia
                                    @else Sustitución @endif
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $j->turno ? $j->turno->fecha->format('d/m/Y') . ' - Turno #' . $j->turno->posicion : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-bold 
                                @if($j->estado == 'pendiente') bg-amber-100 text-amber-700
                                @elseif($j->estado == 'aprobado') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700 @endif">
                                @if($j->estado == 'pendiente') Pendiente
                                @elseif($j->estado == 'aprobado') Aprobado
                                @else Rechazado @endif
                            </span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 ml-13">{{ $j->motivo }}</p>
                    @if($j->sustituto_nombre)
                    <p class="text-xs text-gray-500 mt-1 ml-13">
                        <i class="fas fa-user mr-1"></i>
                        Sustituto: {{ $j->sustituto_nombre }} ({{ $j->sustituto_cedula }})
                    </p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1 ml-13">
                        <i class="fas fa-calendar mr-1"></i>
                        Enviado: {{ $j->registrado_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                @endforeach
            </div>
            
            <div class="p-4 border-t border-gray-100">
                {{ $justificativos->links() }}
            </div>
            @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-inbox text-3xl text-gray-400"></i>
                </div>
                <p class="font-bold text-gray-800 text-xl">Sin justificativos</p>
                <p class="text-gray-500">No has enviado justificativos</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.querySelector('select[name="tipo"]').addEventListener('change', function() {
    const sustiSection = document.getElementById('sustituto-section');
    if (this.value === 'sustitucion') {
        sustiSection.classList.remove('hidden');
    } else {
        sustiSection.classList.add('hidden');
    }
});
</script>
@endsection
