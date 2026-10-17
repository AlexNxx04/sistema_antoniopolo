@extends('layouts.gerente')

@section('title', 'Unidades')
@section('header', 'Gestión de Unidades')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.btn-new-user { display: inline-flex; align-items: center; gap: 10px; padding: 14px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(5,150,105,0.3); }
.btn-new-user:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5,150,105,0.4); }
.filter-card { padding: 20px 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.filter-card label { font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 8px; }
.filter-input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.filter-input:focus { border-color: #059669; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,0.1); }
.btn-search { padding: 12px 24px; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-search:hover { background: linear-gradient(135deg, #047857, #065f46); }
.btn-clean { padding: 12px 24px; background: #f1f5f9; color: #475569; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-clean:hover { background: #e2e8f0; }
</style>

<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-car"></i>Gestión de Unidades</h2>
            <p>Administra las unidades de transporte</p>
        </div>
        <a href="{{ route('gerente.unidades.create') }}" class="btn-new-user">
            <i class="fas fa-plus"></i> Nueva Unidad
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-center gap-3">
        <i class="fas fa-check-circle text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('gerente.unidades.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[250px]">
                <label><i class="fas fa-search mr-2 text-gray-400"></i> Filtrar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por placa..." class="filter-input">
            </div>
            <div class="w-44">
                <label><i class="fas fa-toggle-on mr-2 text-gray-400"></i>Estado</label>
                <select name="estado" class="filter-input">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="dado_baja" {{ request('estado') === 'dado_baja' ? 'selected' : '' }}>Dado de Baja</option>
                </select>
            </div>
            <div class="w-44">
                <label><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>Punto</label>
                <select name="punto_id" class="filter-input">
                    <option value="">Todos</option>
                    @foreach($puntos as $punto)
                    <option value="{{ $punto->punto_id }}" {{ request('punto_id') == $punto->punto_id ? 'selected' : '' }}>{{ $punto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-filter mr-2"></i> Filtrar</button>
            <a href="{{ route('gerente.unidades.index') }}" class="btn-clean"><i class="fas fa-times mr-2"></i> Limpiar</a>
        </form>
    </div>

    <div style="display:flex;flex-direction:column;gap:24px;">
        @php
            $unidadesGuaranda = $unidades->filter(function($u) { return isset($u->punto->nombre) && stripos($u->punto->nombre, 'Guaranda') !== false; });
            $unidadesSalinas = $unidades->filter(function($u) { return isset($u->punto->nombre) && stripos($u->punto->nombre, 'Salinas') !== false; });
            $unidadesOtros = $unidades->filter(function($u) { return !isset($u->punto->nombre) || (stripos($u->punto->nombre, 'Guaranda') === false && stripos($u->punto->nombre, 'Salinas') === false); });
        @endphp

        @if($unidadesGuaranda->count() > 0)
        <div>
            <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:linear-gradient(135deg,#059669,#047857);border-radius:12px;margin-bottom:16px;">
                <i class="fas fa-bus" style="color:white;font-size:18px;"></i>
                <span style="color:white;font-weight:700;font-size:16px;">Unidades Guaranda</span>
                <span style="margin-left:auto;background:rgba(255,255,255,0.2);padding:4px 12px;border-radius:20px;color:white;font-size:13px;font-weight:600;">{{ $unidadesGuaranda->count() }}</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($unidadesGuaranda as $unidad)
            @php
                $estadoColors = [
                    'activo' => ['bg' => '#d1fae5', 'text' => '#047857', 'icon' => 'fa-check-circle'],
                    'dado_baja' => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'icon' => 'fa-times-circle']
                ];
                $colors = $estadoColors[$unidad->estado] ?? $estadoColors['activo'];
            @endphp
            <div style="background:white;border-radius:16px;padding:16px;border:1px solid #e2e8f0;transition:all 0.3s;position:relative;overflow:hidden;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:{{ $colors['text'] }}"></div>
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:white;box-shadow:0 4px 12px rgba(99,102,241,0.3);flex-shrink:0;">
                        {{ str_pad($unidad->numero_interno, 3, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 4px 0;font-family:monospace;">{{ $unidad->placa }}</h3>
                        <span style="padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $colors['bg'] }};color:{{ $colors['text'] }};display:inline-flex;align-items:center;gap:4px;">
                            <i class="fas {{ $colors['icon'] }}"></i> {{ ucfirst($unidad->estado) }}
                        </span>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:8px;padding:8px;background:#f8fafc;border-radius:8px;">
                        <i class="fas fa-map-marker-alt" style="color:#6366f1;font-size:14px;"></i>
                        <span style="font-size:13px;color:#475569;">{{ $unidad->punto->nombre ?? 'Sin asignar' }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;padding:8px;background:#f8fafc;border-radius:8px;">
                        <i class="fas fa-mobile-alt" style="color:#059669;font-size:14px;"></i>
                        <span style="font-size:13px;color:#475569;font-family:monospace;">{{ $unidad->celular_gps ?? 'Sin GPS' }}</span>
                    </div>
                </div>
                <div style="display:flex;gap:8px;border-top:1px solid #f1f5f9;padding-top:12px;">
                    <a href="{{ route('gerente.unidades.show', $unidad->unidad_id) }}" style="flex:1;padding:10px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#2563eb;border-radius:10px;text-align:center;font-weight:600;font-size:12px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(37,99,235,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                    <a href="{{ route('gerente.unidades.edit', $unidad->unidad_id) }}" style="flex:1;padding:10px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;border-radius:10px;text-align:center;font-weight:600;font-size:12px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(5,150,105,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
            @endforeach
            </div>
        </div>
        @endif

        @if($unidadesSalinas->count() > 0)
        <div>
            <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:linear-gradient(135deg,#0ea5e9,#0284c7);border-radius:12px;margin-bottom:16px;">
                <i class="fas fa-bus" style="color:white;font-size:18px;"></i>
                <span style="color:white;font-weight:700;font-size:16px;">Unidades Salinas</span>
                <span style="margin-left:auto;background:rgba(255,255,255,0.2);padding:4px 12px;border-radius:20px;color:white;font-size:13px;font-weight:600;">{{ $unidadesSalinas->count() }}</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($unidadesSalinas as $unidad)
            @php
                $estadoColors = [
                    'activo' => ['bg' => '#d1fae5', 'text' => '#047857', 'icon' => 'fa-check-circle'],
                    'dado_baja' => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'icon' => 'fa-times-circle']
                ];
                $colors = $estadoColors[$unidad->estado] ?? $estadoColors['activo'];
            @endphp
            <div style="background:white;border-radius:16px;padding:16px;border:1px solid #e2e8f0;transition:all 0.3s;position:relative;overflow:hidden;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:{{ $colors['text'] }}"></div>
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:white;box-shadow:0 4px 12px rgba(99,102,241,0.3);flex-shrink:0;">
                        {{ str_pad($unidad->numero_interno, 3, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 4px 0;font-family:monospace;">{{ $unidad->placa }}</h3>
                        <span style="padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $colors['bg'] }};color:{{ $colors['text'] }};display:inline-flex;align-items:center;gap:4px;">
                            <i class="fas {{ $colors['icon'] }}"></i> {{ ucfirst($unidad->estado) }}
                        </span>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:8px;padding:8px;background:#f8fafc;border-radius:8px;">
                        <i class="fas fa-map-marker-alt" style="color:#6366f1;font-size:14px;"></i>
                        <span style="font-size:13px;color:#475569;">{{ $unidad->punto->nombre ?? 'Sin asignar' }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;padding:8px;background:#f8fafc;border-radius:8px;">
                        <i class="fas fa-mobile-alt" style="color:#059669;font-size:14px;"></i>
                        <span style="font-size:13px;color:#475569;font-family:monospace;">{{ $unidad->celular_gps ?? 'Sin GPS' }}</span>
                    </div>
                </div>
                <div style="display:flex;gap:8px;border-top:1px solid #f1f5f9;padding-top:12px;">
                    <a href="{{ route('gerente.unidades.show', $unidad->unidad_id) }}" style="flex:1;padding:10px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#2563eb;border-radius:10px;text-align:center;font-weight:600;font-size:12px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(37,99,235,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                    <a href="{{ route('gerente.unidades.edit', $unidad->unidad_id) }}" style="flex:1;padding:10px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;border-radius:10px;text-align:center;font-weight:600;font-size:12px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(5,150,105,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
            @endforeach
            </div>
        </div>
        @endif

        @if($unidadesOtros->count() > 0)
        <div>
            <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:12px;margin-bottom:16px;">
                <i class="fas fa-bus" style="color:white;font-size:18px;"></i>
                <span style="color:white;font-weight:700;font-size:16px;">Otras Unidades</span>
                <span style="margin-left:auto;background:rgba(255,255,255,0.2);padding:4px 12px;border-radius:20px;color:white;font-size:13px;font-weight:600;">{{ $unidadesOtros->count() }}</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($unidadesOtros as $unidad)
            @php
                $estadoColors = [
                    'activo' => ['bg' => '#d1fae5', 'text' => '#047857', 'icon' => 'fa-check-circle'],
                    'dado_baja' => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'icon' => 'fa-times-circle']
                ];
                $colors = $estadoColors[$unidad->estado] ?? $estadoColors['activo'];
            @endphp
            <div style="background:white;border-radius:16px;padding:16px;border:1px solid #e2e8f0;transition:all 0.3s;position:relative;overflow:hidden;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:{{ $colors['text'] }}"></div>
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:white;box-shadow:0 4px 12px rgba(99,102,241,0.3);flex-shrink:0;">
                        {{ str_pad($unidad->numero_interno, 3, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 4px 0;font-family:monospace;">{{ $unidad->placa }}</h3>
                        <span style="padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $colors['bg'] }};color:{{ $colors['text'] }};display:inline-flex;align-items:center;gap:4px;">
                            <i class="fas {{ $colors['icon'] }}"></i> {{ ucfirst($unidad->estado) }}
                        </span>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:8px;padding:8px;background:#f8fafc;border-radius:8px;">
                        <i class="fas fa-map-marker-alt" style="color:#6366f1;font-size:14px;"></i>
                        <span style="font-size:13px;color:#475569;">{{ $unidad->punto->nombre ?? 'Sin asignar' }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;padding:8px;background:#f8fafc;border-radius:8px;">
                        <i class="fas fa-mobile-alt" style="color:#059669;font-size:14px;"></i>
                        <span style="font-size:13px;color:#475569;font-family:monospace;">{{ $unidad->celular_gps ?? 'Sin GPS' }}</span>
                    </div>
                </div>
                <div style="display:flex;gap:8px;border-top:1px solid #f1f5f9;padding-top:12px;">
                    <a href="{{ route('gerente.unidades.show', $unidad->unidad_id) }}" style="flex:1;padding:10px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#2563eb;border-radius:10px;text-align:center;font-weight:600;font-size:12px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(37,99,235,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                    <a href="{{ route('gerente.unidades.edit', $unidad->unidad_id) }}" style="flex:1;padding:10px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;border-radius:10px;text-align:center;font-weight:600;font-size:12px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s" onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 15px rgba(5,150,105,0.3)'" onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
            @endforeach
            </div>
        </div>
        @endif

        @if($unidades->count() == 0)
        <div style="text-align:center;padding:60px;background:white;border-radius:20px;border:1px solid #e2e8f0;">
            <i class="fas fa-bus" style="font-size:64px;color:#d1fae5;margin-bottom:16px;"></i>
            <p style="font-size:18px;color:#64748b;margin-bottom:20px;">No hay unidades registradas</p>
            <a href="{{ route('gerente.unidades.create') }}" style="padding:14px 24px;background:linear-gradient(135deg,#059669,#047857);color:white;border-radius:12px;display:inline-flex;align-items:center;gap:10px;font-weight:600;font-size:14px;">
                <i class="fas fa-plus"></i> Crear primera unidad
            </a>
        </div>
        @endif
    </div>
</div>
@endsection