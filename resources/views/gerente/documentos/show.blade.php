@extends('layouts.gerente')

@section('title', 'Detalle Documento')
@section('header', 'Detalle Documento')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('gerente.documentos.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detalle de Documento</h1>
            <p class="text-sm text-gray-500">Información completa del documento</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('gerente.documentos.edit', $documento->documento_id) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Datos del Documento
                </h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tipo</label>
                        @if($documento->tipo === 'licencia')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                                Licencia de Conducir
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-purple-100 text-purple-700">
                                Matrícula Vehicular
                            </span>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Número</label>
                        <p class="text-gray-900 font-semibold text-lg">{{ $documento->numero ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Emisión</label>
                        <p class="text-gray-900">{{ $documento->fecha_emision ? $documento->fecha_emision->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Vencimiento</label>
                        <p class="text-gray-900 {{ $documento->esVencido() ? 'text-red-600 font-semibold' : ($documento->estaPorVencer() ? 'text-amber-600 font-semibold' : '') }}">
                            {{ $documento->fecha_vencimiento->format('d/m/Y') }}
                        </p>
                        @if($documento->esVencido())
                            <p class="text-xs text-red-500 mt-1">{{ abs($documento->diasParaVencer()) }} días vencido</p>
                        @elseif($documento->estaPorVencer())
                            <p class="text-xs text-amber-500 mt-1">{{ $documento->diasParaVencer() }} días para vencer</p>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $documento->estado_badge_class }}">
                            {{ ucfirst($documento->estado) }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Última Actualización</label>
                        <p class="text-gray-900">{{ $documento->updated_at ? $documento->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($documento->usuario)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Conductor
                </h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre Completo</label>
                        <p class="text-gray-900 font-semibold text-lg">{{ $documento->usuario->nombres }} {{ $documento->usuario->apellidos }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Cédula</label>
                        <p class="text-gray-900">{{ $documento->usuario->cedula }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Teléfono</label>
                        <p class="text-gray-900">{{ $documento->usuario->telefono ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Correo</label>
                        <p class="text-gray-900">{{ $documento->usuario->correo ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="lg:col-span-1">
        @if($documento->editor)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Última Edición
            </h3>
            <p class="text-gray-700">{{ $documento->editor->nombres }} {{ $documento->editor->apellidos }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $documento->updated_at ? $documento->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Acciones
            </h3>

            <div class="space-y-3">
                <a href="{{ route('gerente.documentos.edit', $documento->documento_id) }}" 
                   class="w-full px-4 py-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-sm font-medium transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar Documento
                </a>

                <form action="{{ route('gerente.documentos.destroy', $documento->documento_id) }}" method="POST" 
                      onsubmit="return confirm('¿Estás seguro de eliminar este documento?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-3 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar Documento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
