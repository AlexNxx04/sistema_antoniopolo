<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnoConfig extends Model
{
    use HasFactory;

    protected $table = 'turnos_config';

    protected $primaryKey = 'config_id';

    public $timestamps = false;

    protected $fillable = [
        'ciclo_id',
        'punto_id',
        'config_json',
        'creado_por',
        'creado_en',
    ];

    protected $casts = [
        'creado_en' => 'datetime',
    ];

    public function ciclo()
    {
        return $this->belongsTo(CicloAnual::class, 'ciclo_id');
    }

    public function punto()
    {
        return $this->belongsTo(Punto::class, 'punto_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
