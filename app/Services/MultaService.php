<?php

namespace App\Services;

use App\Models\Multa;
use App\Models\Turno;
use App\Models\IntervaloSalida;
use App\Models\Salida;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MultaService
{
    const VALOR_POR_MINUTO = 0.50;
    
    const INTERVALOS_GUARANDA = [
        ['inicio' => '05:30', 'fin' => '09:00', 'intervalo' => 10],
        ['inicio' => '09:00', 'fin' => '18:00', 'intervalo' => 15],
        ['inicio' => '18:00', 'fin' => '19:30', 'intervalo' => 10],
    ];
    
    const INTERVALOS_SALINAS = [
        ['inicio' => '05:30', 'fin' => '18:00', 'intervalo' => 15],
        ['inicio' => '18:00', 'fin' => '19:30', 'intervalo' => 10],
    ];

    public static function getIntervaloPermitido($puntoId, $hora = null)
    {
        $hora = $hora ?? now()->format('H:i');
        $intervalos = $puntoId == 1 ? self::INTERVALOS_GUARANDA : self::INTERVALOS_SALINAS;
        
        foreach ($intervalos as $intervalo) {
            if ($hora >= $intervalo['inicio'] && $hora < $intervalo['fin']) {
                return $intervalo['intervalo'];
            }
        }
        
        return 15;
    }

    public static function calcularMinutosRetraso($puntoId, $horaSalida, $horaLlegada)
    {
        $intervalo = self::getIntervaloPermitido($puntoId, $horaSalida);
        
        $salida = \Carbon\Carbon::parse($horaSalida);
        $llegada = \Carbon\Carbon::parse($horaLlegada);
        
        $minutosReales = $llegada->diffInMinutes($salida);
        
        return max(0, $minutosReales - $intervalo);
    }

    public static function crearMultaPorRetraso($turnoId, $salidaId, $horaSalida, $horaLlegada, $puntoId = null)
    {
        $turno = Turno::with('usuario')->find($turnoId);
        
        if (!$turno) {
            return null;
        }
        
        $puntoId = $puntoId ?? $turno->punto_id;
        
        $minutosRetraso = self::calcularMinutosRetraso($puntoId, $horaSalida, $horaLlegada);
        
        if ($minutosRetraso <= 0) {
            return null;
        }
        
        $monto = $minutosRetraso * self::VALOR_POR_MINUTO;
        
        return Multa::create([
            'usuario_id' => $turno->usuario_id,
            'turno_id' => $turnoId,
            'salida_id' => $salidaId,
            'tipo' => 'retraso_llegada',
            'monto' => $monto,
            'minutos_retraso' => $minutosRetraso,
            'descripcion' => "Retraso de {$minutosRetraso} minutos. Intervalo: " . self::getIntervaloPermitido($puntoId, $horaSalida) . "min. Valor: $" . self::VALOR_POR_MINUTO . "/min",
            'estado' => self::determinarEstado($puntoId),
            'registrado_por' => Auth::check() ? Auth::user()->usuario_id : null,
            'registrado_en' => now(),
        ]);
    }

    public static function determinarEstado($puntoId)
    {
        $esFinDeSemana = in_array(now()->dayOfWeek, [0, 6]);
        
        $esCargoOperativo = DB::table('parametros')
            ->where('clave', 'cargo_operativo_dias')
            ->value('valor') ?? '1,2,3,4,5';
        
        $diaActual = now()->dayOfWeek;
        $diasOperativos = array_map('intval', explode(',', $esCargoOperativo));
        
        if (!in_array($diaActual, $diasOperativos)) {
            return 'pendiente_semana';
        }
        
        return $esFinDeSemana ? 'pendiente_semana' : 'pendiente';
    }

    public static function calcularYBorrarMultas($puntoId, $fecha = null)
    {
        $fecha = $fecha ?? now()->toDateString();
        
        $salidas = Salida::with(['turno.usuario', 'llegada'])
            ->whereHas('turno', function($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            })
            ->whereDate('hora_salida', $fecha)
            ->whereNotNull('hora_salida')
            ->get();
        
        $multasCreadas = [];
        
        foreach ($salidas as $salida) {
            if (!$salida->llegada) {
                continue;
            }
            
            $horaSalida = \Carbon\Carbon::parse($salida->hora_salida)->format('H:i');
            $horaLlegada = \Carbon\Carbon::parse($salida->llegada->hora_llegada)->format('H:i');
            
            $minutosRetraso = self::calcularMinutosRetraso($puntoId, $horaSalida, $horaLlegada);
            
            if ($minutosRetraso > 0) {
                $multa = self::crearMultaPorRetraso(
                    $salida->turno_id,
                    $salida->salida_id,
                    $salida->hora_salida,
                    $salida->llegada->hora_llegada,
                    $puntoId
                );
                
                if ($multa) {
                    $multasCreadas[] = $multa;
                }
            }
        }
        
        return $multasCreadas;
    }

    public static function getPendientesSemana($puntoId = null)
    {
        $query = Multa::where('estado', 'pendiente_semana');
        
        if ($puntoId) {
            $query->whereHas('usuario', function($q) use ($puntoId) {
                $q->where('punto_id', $puntoId);
            });
        }
        
        return $query->get();
    }

    public static function marcarPagada($multaId, $usuarioId)
    {
        $multa = Multa::findOrFail($multaId);
        
        $multa->estado = 'cobrada';
        $multa->cobrado_por = $usuarioId;
        $multa->cobrado_en = now();
        $multa->save();
        
        return $multa;
    }

    public static function marcarPendiente($multaId)
    {
        $multa = Multa::findOrFail($multaId);
        
        $multa->estado = 'pendiente';
        $multa->save();
        
        return $multa;
    }
}