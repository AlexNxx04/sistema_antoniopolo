<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro de Turnos - {{ $puntoNombre }}</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f59e0b; color: white; }
        .descanso { background: #f3f4f6; }
        .puntero { background: #d1fae5; }
    </style>
</head>
<body>
    <h1>Antonio Polo S.A. - {{ $puntoNombre }}</h1>
    <p>Fecha: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
    <p>Total: {{ $turnos->count() }} | Operan: {{ $turnos->where('es_descanso', 0)->count() }} | Descansan: {{ $turnos->where('es_descanso', 1)->count() }}</p>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Unidad</th>
                <th>Placa</th>
                <th>Conductor</th>
                <th>Tipo</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody>
            @foreach($turnos as $turno)
            <tr class="{{ $turno->es_descanso == 1 ? 'descanso' : '' }}">
                <td>{{ $turno->posicion }}</td>
                <td>{{ $turno->unidad->numero_interno ?? '-' }}</td>
                <td>{{ $turno->unidad->placa ?? 'N/A' }}</td>
                <td>{{ $turno->usuario->nombres ?? 'N/A' }}</td>
                <td>
                    @if($turno->es_descanso == 1)
                        DESCANS0
                    @elseif($turno->posicion == 1)
                        PUNTERO 1
                    @elseif($turno->posicion == 2)
                        PUNTERO 2
                    @else
                        TURNO {{ $turno->posicion }}
                    @endif
                </td>
                <td>{{ $turno->generado_at ? \Carbon\Carbon::parse($turno->generado_at)->format('H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>