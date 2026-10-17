<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaGeocerca extends Model
{
    protected $table = 'alertas_geocerca';

    protected $primaryKey = 'alerta_id';

    public $timestamps = false;

    protected $fillable = [
        'salida_id',
        'turno_id',
        'unidad_id',
        'tipo',
        'latitud',
        'longitud',
        'distancia_m',
        'punto_id',
        'mensaje',
        'leido',
        'registrado_at',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'distancia_m' => 'decimal:2',
        'leido' => 'boolean',
        'registrado_at' => 'datetime',
    ];

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }

    public function punto()
    {
        return $this->belongsTo(Punto::class, 'punto_id');
    }

    public static function marcarLeidas()
    {
        return self::where('leido', false)->update(['leido' => true]);
    }
}
