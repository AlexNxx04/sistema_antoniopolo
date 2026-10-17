<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LlegadaGps extends Model
{
    protected $table = 'llegadas_gps';

    protected $primaryKey = 'llegada_id';

    public $timestamps = false;

    protected $fillable = [
        'salida_id',
        'punto_destino_id',
        'hora_llegada',
        'tiempo_trayecto_min',
        'es_offline',
        'sincronizado_at',
    ];

    protected $casts = [
        'hora_llegada' => 'datetime',
        'es_offline' => 'boolean',
        'sincronizado_at' => 'datetime',
    ];

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }

    public function puntoDestino()
    {
        return $this->belongsTo(Punto::class, 'punto_destino_id');
    }
}
