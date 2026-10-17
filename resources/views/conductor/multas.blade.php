@extends('layouts.conductor')

@section('title', 'Mis Multas')
@section('header', 'Historial de Multas')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white shadow-lg">
            <i class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Mis Multas</h2>
            <p class="text-gray-500 text-sm">Historial completo de multas</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3">
        <i class="fas fa-check-circle text-xl"></i>
        {{ session('success') }}
    </div>
@endif

<div class="dashboard-card mb-6">
    <div class="stats-grid">
        <div class="stat-box stat-box-salidas">
            <p class="text-blue-800">{{ $multas->total() }}</p>
            <p class="text-blue-600">Total</p>
        </div>
        <div class="stat-box stat-box-turno">
            <p class="text-amber-800">{{ $multas->where('estado', 'pendiente')->count() }}</p>
            <p class="text-amber-600">Pendientes</p>
        </div>
        <div class="stat-box stat-box-salidas">
            <p class="text-green-800">{{ $multas->where('estado', 'cobrada')->count() }}</p>
            <p class="text-green-600">Cobradas</p>
        </div>
        <div class="stat-box stat-box-turno">
            <p class="text-gray-800">${{ number_format($multas->sum('monto'), 2) }}</p>
            <p class="text-gray-600">Monto Total</p>
        </div>
    </div>
</div>

<div class="dashboard-card">
    <div class="section-header">
        <h3><i class="fas fa-list text-emerald-500 mr-2"></i>Lista de Multas</h3>
    </div>
    
    @if($multas->count() > 0)
    <div class="divide-y divide-gray-100">
        @foreach($multas as $multa)
        <div class="p-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg {{ $multa->estado === 'pendiente' ? 'bg-amber-100 text-amber-600' : ($multa->estado === 'cobrada' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600') }} flex items-center justify-center">
                        <i class="fas {{ $multa->tipo === 'retraso_llegada' ? 'fa-clock' : ($multa->tipo === 'ausencia' ? 'fa-user-times' : 'fa-exclamation-circle') }}"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $multa->tipo_display }}</p>
                        <p class="text-xs text-gray-500">{{ $multa->registrado_en->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-lg ${{ number_format($multa->monto, 2) }}</p>
                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $multa->estado_badge_class }}">
                        {{ $multa->estado_display }}
                    </span>
                </div>
            </div>
            @if($multa->descripcion)
            <p class="text-sm text-gray-600 ml-13">{{ $multa->descripcion }}</p>
            @endif
            @if($multa->minutos_retraso)
            <p class="text-xs text-gray-500 mt-1 ml-13">
                <i class="fas fa-clock mr-1"></i>
                {{ $multa->minutos_retraso }} minutos de retraso
            </p>
            @endif
        </div>
        @endforeach
    </div>
    
    <div class="p-4 border-t border-gray-100">
        {{ $multas->links() }}
    </div>
    @else
    <div class="p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <i class="fas fa-check-circle text-3xl text-gray-400"></i>
        </div>
        <p class="font-bold text-gray-800 text-xl">Sin multas</p>
        <p class="text-gray-500">No tienes multas registradas</p>
    </div>
    @endif
</div>
@endsection
