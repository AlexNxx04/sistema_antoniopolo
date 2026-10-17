<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Justificativo extends Model
{
    protected $table = 'justificativos';

    protected $primaryKey = 'justificativo_id';

    public $timestamps = false;

    protected $fillable = [
        'turno_id',
        'tipo',
        'motivo',
        'sustituto_nombre',
        'sustituto_cedula',
        'sustituto_relacion',
        'documento_url',
        'registrado_by',
        'registrado_at',
        'revisado_by',
        'revisado_at',
        'estado',
    ];

    protected $casts = [
        'registrado_at' => 'datetime',
        'revisado_at' => 'datetime',
    ];

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_by');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_by');
    }

    public static function puedeJustificar($turnoId)
    {
        $turno = Turno::find($turnoId);
        
        if (!$turno) {
            return false;
        }
        
        if ($turno->posicion <= 2) {
            return true;
        }
        
        return in_array($turno->estado, ['espera', 'llamado_1', 'llamado_2']);
    }

    public static function tiposPermitidos($esPuntero)
    {
        if ($esPuntero) {
            return [
                'sustitucion' => 'Sustitución',
            ];
        }
        
        return [
            'atraso' => 'Atraso',
            'inasistencia' => 'Inasistencia',
        ];
    }
}