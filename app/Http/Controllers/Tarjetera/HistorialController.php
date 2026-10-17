<?php

namespace App\Http\Controllers\Tarjetera;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;

class HistorialController extends Controller
{
    public function index(Request $request)
    {
        $puntoId = Auth::user()->punto_id;
        $fecha = $request->input('fecha', now()->toDateString());

        $turnos = Turno::where('punto_id', $puntoId)
            ->whereDate('fecha', $fecha)
            ->with(['usuario', 'unidad'])
            ->orderBy('posicion')
            ->get();

        $puntoNombre = $puntoId == 1 ? 'Guaranda' : 'Salinas';

        return view('tarjetera.historial.index', compact('turnos', 'fecha', 'puntoNombre'));
    }

    public function pdf(Request $request)
    {
        $puntoId = Auth::user()->punto_id;
        $fecha = $request->input('fecha', now()->toDateString());

        $turnos = Turno::where('punto_id', $puntoId)
            ->whereDate('fecha', $fecha)
            ->with(['usuario', 'unidad'])
            ->orderBy('posicion')
            ->get();

        $puntoNombre = $puntoId == 1 ? 'Guaranda' : 'Salinas';
        $total = $turnos->count();
        $operan = $turnos->where('es_descanso', 0)->count();
        $descansan = $turnos->where('es_descanso', 1)->count();
        $fechaFormateada = \Carbon\Carbon::parse($fecha)->format('d-m-Y');

        $html = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1f2937; padding: 15px; }
.header { text-align: center; margin-bottom: 15px; border-bottom: 3px solid #d97706; padding-bottom: 10px; }
h1 { color: #d97706; font-size: 18px; margin-bottom: 5px; }
.subtitle { color: #6b7280; font-size: 11px; }
.date { color: #374151; font-size: 12px; font-weight: bold; margin-top: 5px; }
.stats { display: table; width: 100%; margin: 15px 0; }
.stats tr { display: table-row; }
.stats td { display: table-cell; width: 33%; text-align: center; padding: 10px; }
.t1 { background: #fffbeb; }
.t2 { background: #ecfdf5; }
.t3 { background: #f3f4f6; }
.num { font-size: 22px; font-weight: bold; }
.lbl { font-size: 9px; text-transform: uppercase; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th { background: #d97706; color: white; padding: 8px; font-size: 10px; text-transform: uppercase; }
td { padding: 6px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
tr:nth-child(even) td { background: #fffbeb; }
tr:nth-child(odd) td { background: #ffffff; }
.badge { padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
.b1 { background: #e5e7eb; color: #374151; }
.b2 { background: #059669; color: white; }
.b3 { background: #fef3c7; color: #92400e; }
.footer { margin-top: 15px; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
</style>
</head>
<body>
<div class="header">
<h1>ANTONIO POLO S.A.</h1>
<p class="subtitle">Registro de Turnos - ' . $puntoNombre . '</p>
<p class="date">Fecha: ' . \Carbon\Carbon::parse($fecha)->format('d/m/Y') . '</p>
</div>

<table class="stats">
<tr>
<td class="t1"><div class="num" style="color:#d97706;">' . $total . '</div><div class="lbl" style="color:#d97706;">Total</div></td>
<td class="t2"><div class="num" style="color:#059669;">' . $operan . '</div><div class="lbl" style="color:#059669;">Operan</div></td>
<td class="t3"><div class="num" style="color:#4b5563;">' . $descansan . '</div><div class="lbl" style="color:#4b5563;">Descansan</div></td>
</tr>
</table>

<table>
<tr>
<th style="width:8%;text-align:center;">#</th>
<th style="width:10%;">Unidad</th>
<th style="width:12%;">Placa</th>
<th style="width:28%;">Conductor</th>
<th style="width:15%;">Telefono</th>
<th style="width:27%;text-align:center;">Tipo</th>
</tr>';

        foreach ($turnos as $turno) {
            $conductor = trim(($turno->usuario->nombres ?? 'Sin asignar') . ' ' . ($turno->usuario->apellidos ?? ''));
            $unidad = $turno->unidad->numero_interno ?? '-';
            $placa = $turno->unidad->placa ?? 'N/A';
            $telefono = $turno->usuario->telefono ?? 'N/A';

            $badge = '';
            if ($turno->es_descanso == 1) {
                $badge = '<span class="badge b1">DESCANSO</span>';
            } elseif ($turno->posicion == 1) {
                $badge = '<span class="badge b2">PUNTERO 1</span>';
            } elseif ($turno->posicion == 2) {
                $badge = '<span class="badge b2">PUNTERO 2</span>';
            } else {
                $badge = '<span class="badge b3">TURNO ' . $turno->posicion . '</span>';
            }

            $html .= '<tr>
<td style="text-align:center;font-weight:bold;">' . $turno->posicion . '</td>
<td>' . $unidad . '</td>
<td>' . $placa . '</td>
<td><strong>' . $conductor . '</strong></td>
<td>' . $telefono . '</td>
<td style="text-align:center;">' . $badge . '</td>
</tr>';
        }

        $html .= '</table>

<div class="footer">
Generado el ' . now()->format('d/m/Y') . ' a las ' . now()->format('H:i') . ' | Sistema de Turnos - Antonio Polo S.A.
</div>
</body>
</html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="turnos_' . $puntoNombre . '_' . $fechaFormateada . '.pdf"');
    }
}