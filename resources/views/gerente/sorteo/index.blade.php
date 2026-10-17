@extends('layouts.gerente')

@section('title', 'Sorteo Diario')
@section('header', 'Sorteo Diario')

@section('content')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
.page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
.page-header h2 i { color: #059669; margin-right: 10px; }
.page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
.btn-historial { padding: 12px 20px; background: #f1f5f9; color: #64748b; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
.card-main { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.card-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #ecfdf5, #d1fae5); }
.card-header h3 { font-size: 16px; font-weight: 700; color: #1e293b; }
.card-header span { font-size: 13px; color: #64748b; }
.table-row { padding: 14px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; }
.table-row:hover { background: #f8fafc; }
.avatar { width: 32px; height: 32px; background: #dbeafe; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #2563eb; font-weight: 700; font-size: 12px; }
.badge { padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; }
.badge-asignado { background: #dbeafe; color: #1d4ed8; }
.badge-en_curso { background: #fef3c7; color: #b45309; }
.badge-completado { background: #d1fae5; color: #047857; }
.badge-cancelado { background: #fee2e2; color: #dc2626; }
.btn-accion { padding: 8px 16px; background: #059669; color: white; border-radius: 8px; font-size: 12px; font-weight: 600; }
.btn-generar { padding: 14px 28px; background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 14px; font-weight: 600; font-size: 16px; box-shadow: 0 8px 20px rgba(5,150,105,0.3); }
.success { padding: 16px; background: #d1fae5; border: 1px solid #059669; border-radius: 12px; color: #047857; margin-bottom: 20px; }
.error { padding: 16px; background: #fee2e2; border: 1px solid #dc2626; border-radius: 12px; color: #dc2626; margin-bottom: 20px; }
</style>

<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-dice"></i>Sorteo Diario</h2>
            <p>Genera el orden de turnos y asigna punteros</p>
        </div>
        <a href="{{ route('gerente.sorteo.historial') }}" class="btn-historial">
            <i class="fas fa-history"></i> Ver Historial
        </a>
    </div>

    @if(session('success'))
    <div class="success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="error">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
        <div class="card-main">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Turnos del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h3>
                <span>{{ $turnos->count() }} turnos</span>
            </div>
            <div class="table-header-row" style="padding:12px 24px;background:#f1f5f9;border-bottom:1px solid #e2e8f0;display:flex;font-size:13px;font-weight:600;color:#64748b;">
                <div style="width:80px;">Orden</div>
                <div style="flex:1;">Conductor</div>
                <div style="flex:1;">Unidad</div>
                <div style="width:120px;">Estado</div>
                <div style="width:100px;text-align:center;">Acciones</div>
            </div>
            @forelse($turnos as $turno)
            <div class="table-row">
                <div style="width:80px;">
                    <span class="avatar">{{ $turno->orden }}</span>
                </div>
                <div style="flex:1;">
                    @if($turno->usuario)
                        <span style="font-weight:500;color:#1e293b;">{{ $turno->usuario->nombres }} {{ $turno->usuario->apellidos }}</span>
                        <small style="display:block;color:#64748b;font-size:12px;">{{ $turno->usuario->cedula }}</small>
                    @else
                        <span style="color:#94a3b8;">Sin asignar</span>
                    @endif
                </div>
                <div style="flex:1;">
                    @if($turno->unidad)
                        <span style="font-weight:500;font-family:monospace;">{{ $turno->unidad->placa }}</span>
                    @else
                        <span style="color:#94a3b8;">Sin unidad</span>
                    @endif
                </div>
                <div style="width:120px;">
                    @switch($turno->estado)
                        @case('asignado')
                            <span class="badge badge-asignado">Asignado</span>
                            @break
                        @case('en_curso')
                            <span class="badge badge-en_curso">En Curso</span>
                            @break
                        @case('completado')
                            <span class="badge badge-completado">Completado</span>
                            @break
                        @case('cancelado')
                            <span class="badge badge-cancelado">Cancelado</span>
                            @break
                    @endswitch
                </div>
                <div style="width:100px;text-align:center;">
                    @if($turno->estado === 'asignado')
                    <form action="{{ route('gerente.sorteo.eliminar', $turno->turno_id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-accion" style="background:#dc2626;">Eliminar</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div style="padding:60px;text-align:center;">
                <p style="color:#64748b;">No hay turnos para esta fecha</p>
            </div>
            @endforelse
        </div>

        <div style="background:white;border-radius:20px;border:1px solid #e2e8f0;padding:24px;">
            <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin-bottom:20px;">
                <i class="fas fa-play"></i> Generar Sorteo
            </h3>
            <form action="{{ route('gerente.sorteo.generar') }}" method="POST">
                @csrf
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;">Fecha</label>
                    <input type="date" name="fecha" value="{{ $fecha }}" required 
                        style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;background:#f8fafc;">
                </div>
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px;font-weight:600;color:#475569;display:block;margin-bottom:8px;">Punto</label>
                    <select name="punto_id" required style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;background:#f8fafc;">
                        @foreach($puntos as $punto)
                            <option value="{{ $punto->punto_id }}">{{ $punto->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-generar" style="width:100%;">
                    <i class="fas fa-dice"></i> Generar Sorteo
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
                                        'cancelado' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $estadoClase }}">
                                    {{ $turno->estadoDisplay }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <div class="relative group">
                                        <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg" title="Acciones">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                        </button>
                                        <div class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-100 hidden group-hover:block z-10">
                                            <form action="{{ route('gerente.sorteo.cambiarEstado', $turno->turno_id) }}" method="POST" class="p-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estado" value="en_curso">
                                                <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-yellow-50 rounded {{ $turno->estado === 'en_curso' ? 'bg-yellow-50' : '' }}">
                                                    En Curso
                                                </button>
                                            </form>
                                            <form action="{{ route('gerente.sorteo.cambiarEstado', $turno->turno_id) }}" method="POST" class="p-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estado" value="completado">
                                                <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-green-50 rounded {{ $turno->estado === 'completado' ? 'bg-green-50' : '' }}">
                                                    Completado
                                                </button>
                                            </form>
                                            <form action="{{ route('gerente.sorteo.cambiarEstado', $turno->turno_id) }}" method="POST" class="p-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estado" value="cancelado">
                                                <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-red-50 rounded {{ $turno->estado === 'cancelado' ? 'bg-red-50' : '' }}">
                                                    Cancelar
                                                </button>
                                            </form>
                                            <hr class="my-1">
                                            <form action="{{ route('gerente.sorteo.eliminarTurno', $turno->turno_id) }}" method="POST" class="p-1" onsubmit="return confirm('¿Eliminar este turno?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                No hay turnos para esta fecha
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Generar Sorteo</h3>
            
            <form action="{{ route('gerente.sorteo.generar') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date" name="fecha" id="fecha" value="{{ $fecha }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('fecha') border-red-500 @enderror">
                        @error('fecha')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="punto_id" class="block text-sm font-medium text-gray-700 mb-1">Punto *</label>
                        <select name="punto_id" id="punto_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('punto_id') border-red-500 @enderror">
                            <option value="">Seleccionar punto</option>
                            @foreach($puntos as $punto)
                                <option value="{{ $punto->punto_id }}">{{ $punto->nombre }}</option>
                            @endforeach
                        </select>
                        @error('punto_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Generar Sorteo
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Cambiar Fecha</h4>
                <form action="{{ route('gerente.sorteo.index') }}" method="GET" class="flex gap-2">
                    <input type="date" name="fecha" value="{{ $fecha }}" required
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Ver
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
            <h3 class="font-semibold text-gray-800 mb-4">Estadísticas</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Turnos</span>
                    <span class="font-semibold text-gray-900">{{ $turnos->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Completados</span>
                    <span class="font-semibold text-green-600">{{ $turnos->where('estado', 'completado')->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">En Curso</span>
                    <span class="font-semibold text-yellow-600">{{ $turnos->where('estado', 'en_curso')->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Cancelados</span>
                    <span class="font-semibold text-red-600">{{ $turnos->where('estado', 'cancelado')->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
