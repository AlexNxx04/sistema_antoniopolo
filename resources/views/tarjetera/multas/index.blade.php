@extends('layouts.tarjetera')

@section('title', 'Multas')
@section('header', 'Multas')

@section('content')

<div class="p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-money-check-alt text-white text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Multas</h2>
            <p class="text-gray-500 text-sm">{{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-xl">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('info'))
    <div class="mb-6 p-4 bg-blue-100 border border-blue-300 text-blue-700 rounded-xl">
        <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
    </div>
    @endif

    @if($pendientes > 0)
    <div class="mb-6 p-4 bg-amber-100 border border-amber-300 text-amber-700 rounded-xl flex items-center justify-between">
        <div>
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>{{ $pendientes }}</strong> multa(s) pendiente(s) de cobro
        </div>
        <form action="{{ route('tarjetera.multas.calcular') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">
                <i class="fas fa-calculator mr-2"></i> Calcular Multas
            </button>
        </form>
    </div>
    @else
    <div class="mb-6 p-4 bg-gray-100 border border-gray-300 text-gray-700 rounded-xl">
        <i class="fas fa-calculator mr-2"></i> No hay multas pendientes. 
        <form action="{{ route('tarjetera.multas.calcular') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-amber-600 underline hover:text-amber-700 ml-2">
                Calcular hoy
            </button>
        </form>
    </div>
    @endif

    <div class="modern-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-amber-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Conductor</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tipo</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Minutos</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Monto</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($multas as $multa)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800">{{ $multa->usuario->nombres ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $multa->usuario->cedula ?? '' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-sm text-gray-600">
                            @switch($multa->tipo)
                                @case('retraso_llegada') RetrasoLlegada @break
                                @case('ausencia') Ausencia @break
                                @case('documento_vencido') Doc. Vencido @break
                                @case('indisciplina') Indisciplina @break
                                @default {{ $multa->tipo }}
                            @endswitch
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $multa->minutos_retraso ? $multa->minutos_retraso . ' min' : '-' }}
                    </td>
                    <td class="px-4 py-3 font-bold text-gray-800">
                        ${{ number_format($multa->monto, 2) }}
                    </td>
                    <td class="px-4 py-3">
                        @switch($multa->estado)
                            @case('pendiente')
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock mr-1"></i> Pendiente
                                </span>
                                @break
                            @case('pendiente_semana')
                                <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-calendar mr-1"></i> Lunes
                                </span>
                                @break
                            @case('cobrada')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-check mr-1"></i> Cobrada
                                </span>
                                @break
                            @case('anulada')
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                    Anulada
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-sm">
                        {{ $multa->registrado_en ? \Carbon\Carbon::parse($multa->registrado_en)->format('d/m H:i') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if(in_array($multa->estado, ['pendiente', 'pendiente_semana']))
                        <form action="{{ route('tarjetera.multas.cobrar', $multa->multa_id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600">
                                <i class="fas fa-check mr-1"></i> Cobrar
                            </button>
                        </form>
                        @else
                        <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                        <p>No hay multas registradas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($multas->hasPages())
    <div class="mt-4">
        {{ $multas->links() }}
    </div>
    @endif
</div>
@endsection