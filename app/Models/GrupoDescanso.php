<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoDescanso extends Model
{
    protected $table = 'grupos_descanso';

    protected $primaryKey = 'grupo_id';

    public $timestamps = false;

    protected $fillable = [
        'ciclo_id',
        'nombre',
        'dia_descanso',
        'activo',
        'creado_por',
        'creado_en',
    ];

    protected $casts = [
        'dia_descanso' => 'integer',
        'activo' => 'boolean',
        'creado_en' => 'datetime',
    ];

    public function ciclo()
    {
        return $this->belongsTo(CicloAnual::class, 'ciclo_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function miembros()
    {
        return $this->hasMany(GrupoMiembro::class, 'grupo_id');
    }

    public function miembrosWithUnidad()
    {
        return $this->hasMany(GrupoMiembro::class, 'grupo_id')->with('unidad');
    }

    public function getNombreDiaDescansoAttribute(): string
    {
        $dias = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];

        return $dias[$this->dia_descanso] ?? 'Desconocido';
    }
}
