<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicloAnual extends Model
{
    protected $table = 'ciclos_anuales';

    protected $primaryKey = 'ciclo_id';

    public $timestamps = false;

    protected $fillable = [
        'anio',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'creado_by',
        'creado_at',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'creado_at' => 'datetime',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_by');
    }

    public function grupos()
    {
        return $this->hasMany(GrupoDescanso::class, 'ciclo_id');
    }

    public function esActivo(): bool
    {
        return $this->activo === true;
    }

    public static function getCicloActivo()
    {
        return self::where('activo', 1)->first();
    }
}
