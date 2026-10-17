<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Punto extends Model
{
    protected $table = 'puntos';

    protected $primaryKey = 'punto_id';

    public $timestamps = false;

    protected $fillable = ['nombre', 'latitud', 'longitud', 'radio_geofence_m', 'activo'];
}
