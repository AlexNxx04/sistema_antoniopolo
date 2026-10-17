<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $table = 'salidas';

    protected $primaryKey = 'salida_id';

    public $timestamps = false;

    protected $fillable = [
        'turno_id',
        'punto_origen_id',
        'hora_salida',
        'registrado_by',
        'registro_auto',
        'es_excepcion',
        'motivo_excepcion',
        'infraccion_intervalo',
    ];

    protected $casts = [
        'hora_salida' => 'datetime',
        'registro_auto' => 'boolean',
        'es_excepcion' => 'boolean',
        'infraccion_intervalo' => 'boolean',
    ];

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function puntoOrigen()
    {
        return $this->belongsTo(Punto::class, 'punto_origen_id');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_by');
    }

    public function llegada()
    {
        return $this->hasOne(LlegadaGps::class, 'salida_id');
    }

    public function posiciones()
    {
        return $this->hasMany(PosicionGps::class, 'salida_id');
    }
}
