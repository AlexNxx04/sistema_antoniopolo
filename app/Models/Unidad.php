<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $table = 'unidades';

    protected $primaryKey = 'unidad_id';

    public $timestamps = true;

    protected $fillable = ['placa', 'numero_interno', 'celular_gps', 'punto_id', 'estado'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function punto()
    {
        return $this->belongsTo(Punto::class, 'punto_id');
    }

    public function conductor()
    {
        return $this->belongsTo(User::class, 'unidad_id');
    }
}
