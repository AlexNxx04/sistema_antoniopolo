<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntervaloSalida extends Model
{
    protected $table = 'intervalos_salida';

    protected $primaryKey = 'intervalo_id';

    public $timestamps = false;

    protected $fillable = [
        'punto_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'intervalo_min',
        'updated_by',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'intervalo_min' => 'integer',
    ];

    public function punto()
    {
        return $this->belongsTo(Punto::class, 'punto_id');
    }

    public static function getIntervaloActual($puntoId)
    {
        $ahora = now()->format('H:i:s');
        $diaSemana = now()->dayOfWeek;
        
        return self::where('punto_id', $puntoId)
            ->whereRaw("REPLACE(dia_semana, ' ', '') LIKE ?", ["%{$diaSemana}%"])
            ->whereTime('hora_inicio', '<=', $ahora)
            ->whereTime('hora_fin', '>=', $ahora)
            ->first();
    }

    public static function calcularMinutosPermitidos($puntoId, $horaLlegada)
    {
        $intervalo = self::getIntervaloActual($puntoId);
        
        if (!$intervalo) {
            return $puntoId == 1 ? 15 : 15;
        }
        
        return $intervalo->intervalo_min;
    }

    public static function tieneInfraccion($puntoId, $horaSalida, $horaLlegada)
    {
        $intervaloPermitido = self::calcularMinutosPermitidos($puntoId, $horaLlegada);
        
        $salida = \Carbon\Carbon::parse($horaSalida);
        $llegada = \Carbon\Carbon::parse($horaLlegada);
        
        $minutosReales = $llegada->diffInMinutes($salida);
        
        return $minutosReales > $intervaloPermitido ? $minutosReales - $intervaloPermitido : 0;
    }
}