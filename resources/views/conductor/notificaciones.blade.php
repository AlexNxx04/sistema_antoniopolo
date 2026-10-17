@extends('layouts.conductor')

@section('title', 'Notificaciones')

@section('content')
<div class="space-y-4">
    <h2 class="text-lg font-bold text-gray-800">Notificaciones</h2>
    
    @forelse($notificaciones as $notif)
    <div class="card p-4 {{ !$notif->leido ? 'border-l-4 border-l-emerald-500' : '' }}">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ !$notif->leido ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                @switch($notif->tipo)
                    @case('llamado_turno')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @break
                    @case('sancion')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        @break
                    @case('documento_vencido')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        @break
                    @default
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                @endswitch
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-800">{{ $notif->mensaje }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $notif->enviado_at->format('d/m/Y H:i') }}</p>
            </div>
            @if(!$notif->leido)
            <form action="{{ route('conductor.notificaciones.leer', $notif->notif_id) }}" method="POST">
                @csrf
                <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="card p-6 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <h3 class="font-semibold text-gray-700 mb-2">Sin notificaciones</h3>
        <p class="text-sm text-gray-500">No tienes notificaciones pendientes.</p>
    </div>
    @endforelse

    @if($notificaciones->hasPages())
    <div class="mt-4">
        {{ $notificaciones->links() }}
    </div>
    @endif
</div>
@endsection
