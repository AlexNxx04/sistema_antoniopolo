@extends('layouts.gerente')

@section('title', 'Ver Unidad')
@section('header', 'Detalle de Unidad')

@section('content')
<div class="fade-in" x-data="{ confirmOpen: false, confirmUrl: '', confirmMessage: '' }">
    
    <template x-teleport="body">
        <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" @keydown.escape.window="confirmOpen = false">
            <div x-show="confirmOpen" class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Confirmar Acción</h3>
                    <p class="text-gray-500 mb-6" x-text="confirmMessage"></p>
                    <div class="flex gap-3">
                        <button @click="confirmOpen = false" class="flex-1 px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">Cancelar</button>
                        <form x-bind:action="confirmUrl" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-5 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 shadow-lg">Confirmar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('gerente.unidades.index') }}" style="padding:14px 18px;background:linear-gradient(135deg,#059669,#047857);color:white;border-radius:12px;display:flex;align-items:center;gap:10px;font-weight:600;font-size:14px;box-shadow:0 4px 12px rgba(5,150,105,0.3);transition:all 0.2s" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(5,150,105,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(5,150,105,0.3)'">
                <i class="fas fa-arrow-left" style="font-size:16px"></i> Volver
            </a>
            <div>
                <h2 style="font-size:24px;font-weight:800;color:#1e293b;margin:0;">Detalle de Unidad</h2>
                <p style="font-size:13px;color:#94a3b8;margin:4px 0 0 0;">Información completa de la unidad</p>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr;gap:20px;margin-bottom:20px;">
        <div style="background:white;border-radius:20px;padding:28px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                <div style="width:90px;height:90px;border-radius:20px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(99,102,241,0.4);flex-shrink:0;">
                    <i class="fas fa-bus" style="font-size:40px;color:white;"></i>
                </div>
                <div style="flex:1;min-width:200px;">
                    <h3 style="font-size:22px;font-weight:800;color:#1e293b;margin:0 0 8px 0;font-family:monospace;">{{ $unidad->placa }}</h3>
                    <p style="font-size:14px;color:#64748b;margin:0 0 12px 0;">Unidad #{{ str_pad($unidad->numero_interno, 3, '0', STR_PAD_LEFT) }}</p>
                    @switch($unidad->estado)
                        @case('activo')
                            <span style="padding:8px 16px;border-radius:20px;font-size:13px;font-weight:600;background:#d1fae5;color:#059669;display:inline-flex;align-items:center;gap:8px;">
                                <i class="fas fa-check-circle"></i> Activa
                            </span>
                            @break
                        @case('dado_baja')
                            <span style="padding:8px 16px;border-radius:20px;font-size:13px;font-weight:600;background:#fee2e2;color:#b91c1c;display:inline-flex;align-items:center;gap:8px;">
                                <i class="fas fa-times-circle"></i> Dado de Baja
                            </span>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;">
        <div style="background:white;border-radius:16px;padding:20px;border:1px solid #e2e8f0;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
            <div style="display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;margin-bottom:14px;">
                <i class="fas fa-car" style="color:#6366f1;font-size:18px;"></i>
                <span style="font-size:14px;color:#64748b;">Placa</span>
            </div>
            <p style="font-size:18px;font-weight:700;color:#1e293b;font-family:monospace;">{{ $unidad->placa }}</p>
        </div>

        <div style="background:white;border-radius:16px;padding:20px;border:1px solid #e2e8f0;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
            <div style="display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;margin-bottom:14px;">
                <i class="fas fa-hashtag" style="color:#6366f1;font-size:18px;"></i>
                <span style="font-size:14px;color:#64748b;">Número</span>
            </div>
            <p style="font-size:18px;font-weight:700;color:#1e293b;">{{ str_pad($unidad->numero_interno, 3, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div style="background:white;border-radius:16px;padding:20px;border:1px solid #e2e8f0;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
            <div style="display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;margin-bottom:14px;">
                <i class="fas fa-mobile-alt" style="color:#059669;font-size:18px;"></i>
                <span style="font-size:14px;color:#64748b;">GPS</span>
            </div>
            <p style="font-size:18px;font-weight:700;color:#1e293b;font-family:monospace;">{{ $unidad->celular_gps ?? 'Sin registro' }}</p>
        </div>

        <div style="background:white;border-radius:16px;padding:20px;border:1px solid #e2e8f0;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
            <div style="display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;margin-bottom:14px;">
                <i class="fas fa-map-marker-alt" style="color:#6366f1;font-size:18px;"></i>
                <span style="font-size:14px;color:#64748b;">Punto</span>
            </div>
            <p style="font-size:18px;font-weight:700;color:#1e293b;">{{ $unidad->punto->nombre ?? 'Sin asignar' }}</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr;gap:20px;margin-top:20px;">
        <div style="background:white;border-radius:20px;padding:24px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 20px 0;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-user" style="color:#059669;font-size:18px;"></i> Conductor Asignado
            </h3>
            @if(!empty($conductor))
                <div style="display:flex;align-items:center;gap:16px;padding:16px;background:#f0fdf4;border-radius:14px;">
                    <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#059669,#047857);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:white;box-shadow:0 4px 12px rgba(5,150,105,0.3);">
                        {{ substr($conductor->nombres, 0, 1) }}{{ substr($conductor->apellidos, 0, 1) }}
                    </div>
                    <div>
                        <p style="font-size:16px;font-weight:700;color:#1e293b;margin:0;">{{ $conductor->nombres }} {{ $conductor->apellidos }}</p>
                        <p style="font-size:14px;color:#64748b;margin:4px 0 0 0;">{{ $conductor->cedula }}</p>
                    </div>
                </div>
            @else
                <div style="text-align:center;padding:40px;">
                    <i class="fas fa-user-slash" style="font-size:48px;color:#e2e8f0;margin-bottom:12px;"></i>
                    <p style="font-size:14px;color:#94a3b8;margin:0;">Sin conductor asignado</p>
                </div>
            @endif
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr;gap:20px;margin-top:20px;">
        <div style="background:white;border-radius:20px;padding:24px;border:1px solid #e2e8f0;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 20px 0;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-history" style="color:#6366f1;font-size:18px;"></i> Historial de Turnos
            </h3>
@if($turnos && $turnos->count() > 0)
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach($turnos as $turno)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#f8fafc;border-radius:12px;">
                            <div>
                                <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $turno->fecha?->format('d/m/Y') }} - Turno {{ str_pad($turno->posicion, 3, '0', STR_PAD_LEFT) }}</p>
                                <p style="font-size:12px;color:#64748b;margin:4px 0 0 0;">{{ $turno->punto->nombre ?? 'Sin punto' }}</p>
                            </div>
                            <div style="display:flex;gap:8px;">
                                @if($turno->es_descanso == 1)
                                <span style="padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;background:#ede9fe;color:#7c3aed;">DESCANSO</span>
                                @endif
                                @if($turno->es_puntero == 1)
                                <span style="padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;background:#fef3c7;color:#b45309;">PUNTERO {{ $turno->posicion <= 2 ? $turno->posicion : '' }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:40px;">
                    <i class="fas fa-history" style="font-size:48px;color:#e2e8f0;margin-bottom:12px;"></i>
                    <p style="font-size:14px;color:#94a3b8;margin:0;">No hay turnos registrados</p>
                </div>
            @endif
        </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:20px;">
        @if($unidad->estado !== 'dado_baja')
        <button @click="confirmOpen = true; confirmUrl = '{{ route('gerente.unidades.destroy', $unidad->unidad_id) }}'; confirmMessage = 'Dar de baja a la unidad {{ $unidad->placa }}. La unidad no podrá operar.'" style="flex:1;padding:16px;background:linear-gradient(135deg,#dc2626,#b91c1c);color:white;border-radius:14px;text-align:center;font-weight:600;font-size:14px;display:flex;align-items:center;justify-content:center;gap:10px;cursor:pointer;border:none;transition:all 0.2s;box-shadow:0 4px 12px rgba(220,38,38,0.3);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(220,38,38,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(220,38,38,0.3)'">
            <i class="fas fa-times-circle"></i> Dar de Baja
        </button>
        @else
        <form action="{{ route('gerente.unidades.reactivar', $unidad->unidad_id) }}" method="POST" style="flex:1;">
            @csrf
            <button type="submit" style="width:100%;padding:16px;background:linear-gradient(135deg,#059669,#047857);color:white;border-radius:14px;text-align:center;font-weight:600;font-size:14px;display:flex;align-items:center;justify-content:center;gap:10px;cursor:pointer;border:none;transition:all 0.2s;box-shadow:0 4px 12px rgba(5,150,105,0.3);">
                <i class="fas fa-check-circle"></i> Reactivar
            </button>
        </form>
        @endif
    </div>
</div>
@endsection