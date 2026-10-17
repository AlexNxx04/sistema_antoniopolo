<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro de Turnos - {{ $puntoNombre }} - {{ \Carbon\Carbon::parse($fecha)->format('d-m-Y') }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #1f2937; padding: 10mm; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #d97706; padding-bottom: 15px; }
        .header h1 { color: #d97706; font-size: 22px; letter-spacing: 2px; margin-bottom: 5px; }
        .header .subtitle { color: #6b7280; font-size: 13px; }
        .header .date { color: #374151; font-size: 14px; font-weight: bold; margin-top: 8px; }

        .stats { display: flex; justify-content: center; gap: 20px; margin: 25px 0; }
        .stat-box { text-align: center; padding: 15px 30px; border-radius: 10px; min-width: 100px; }
        .stat-total { background: #fffbeb; border: 2px solid #fcd34d; }
        .stat-operan { background: #ecfdf5; border: 2px solid #6ee7b7; }
        .stat-descansan { background: #f3f4f6; border: 2px solid #d1d5db; }
        .stat-number { font-size: 28px; font-weight: 800; }
        .stat-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-top: 3px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th { background: linear-gradient(135deg, #d97706, #f59e0b); color: white; padding: 10px 8px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tr:nth-child(even) { background: #fffbeb; }
        tr:hover { background: #fef3c7; }

        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-weight: 700; font-size: 10px; text-transform: uppercase; }
        .badge-descanso { background: #e5e7eb; color: #374151; }
        .badge-puntero { background: #059669; color: white; }
        .badge-turno { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

        .posicion { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; background: #d97706; color: white; border-radius: 50%; font-weight: bold; font-size: 12px; }
        .posicion-descanso { background: #9ca3af; }
        .posicion-puntero { background: #059669; }

        .footer { margin-top: 25px; text-align: center; padding-top: 15px; border-top: 1px solid #e5e7eb; }
        .footer p { font-size: 9px; color: #9ca3af; }
        .footer .system { color: #d97706; font-weight: 600; }

        .print-btn { display: none; }

        @media print {
            body { padding: 0; }
            .print-btn { display: none !important; }
            th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .posicion { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn" style="position:fixed; top:20px; right:20px; padding:12px 24px; background:#d97706; color:white; border:none; border-radius:8px; font-size:14px; cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
        <i class="fas fa-print"></i> Imprimir / Guardar PDF
    </button>

    <div class="header">
        <h1>ANTONIO POLO S.A.</h1>
        <p class="subtitle">Registro de Turnos - {{ $puntoNombre }}</p>
        <p class="date">{{ \Carbon\Carbon::parse($fecha)->format('l, d de F del Y') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box stat-total">
            <div class="stat-number" style="color:#d97706;">{{ $turnos->count() }}</div>
            <div class="stat-label" style="color:#d97706;">Total</div>
        </div>
        <div class="stat-box stat-operan">
            <div class="stat-number" style="color:#059669;">{{ $turnos->where('es_descanso', 0)->count() }}</div>
            <div class="stat-label" style="color:#059669;">Operan</div>
        </div>
        <div class="stat-box stat-descansan">
            <div class="stat-number" style="color:#4b5563;">{{ $turnos->where('es_descanso', 1)->count() }}</div>
            <div class="stat-label" style="color:#4b5563;">Descansan</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:8%; text-align:center;">Pos</th>
                <th style="width:10%;">Unidad</th>
                <th style="width:12%;">Placa</th>
                <th style="width:30%;">Conductor</th>
                <th style="width:15%; text-align:center;">Telefono</th>
                <th style="width:25%; text-align:center;">Tipo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($turnos as $turno)
            <tr>
                <td style="text-align:center;">
                    <span class="posicion @if($turno->es_descanso == 1) posicion-descanso @elseif($turno->posicion <= 2) posicion-puntero @endif">
                        {{ $turno->posicion }}
                    </span>
                </td>
                <td><strong>{{ $turno->unidad->numero_interno ?? '-' }}</strong></td>
                <td>{{ $turno->unidad->placa ?? 'N/A' }}</td>
                <td>
                    <strong>
                        {{ $turno->usuario->nombres ?? 'Sin asignar' }}
                        @if($turno->usuario->apellidos ?? false)
                            {{ $turno->usuario->apellidos }}
                        @endif
                    </strong>
                </td>
                <td style="text-align:center;">{{ $turno->usuario->telefono ?? 'N/A' }}</td>
                <td style="text-align:center;">
                    @if($turno->es_descanso == 1)
                        <span class="badge badge-descanso">Descanso</span>
                    @elseif($turno->posicion == 1)
                        <span class="badge badge-puntero">Puntero 1</span>
                    @elseif($turno->posicion == 2)
                        <span class="badge badge-puntero">Puntero 2</span>
                    @else
                        <span class="badge badge-turno">Turno {{ $turno->posicion }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} | <span class="system">Sistema de Gestión de Turnos</span> | Antonio Polo S.A.</p>
    </div>
</body>
</html>