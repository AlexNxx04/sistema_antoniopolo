<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosicionGps extends Model
{
    protected $table = 'posiciones_gps';

    protected $primaryKey = 'posicion_id';

    public $timestamps = false;

    protected $fillable = [
        'salida_id',
        'latitud',
        'longitud',
        'velocidad',
        'timestamp',
        'es_offline',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'velocidad' => 'decimal:2',
        'timestamp' => 'datetime',
        'es_offline' => 'boolean',
    ];

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }
}
