<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turnos_diarios';

    protected $primaryKey = 'turno_id';

    public $timestamps = false;

    protected $fillable = [
        'ciclo_id',
        'fecha',
        'punto_id',
        'usuario_id',
        'unidad_id',
        'posicion',
        'es_puntero',
        'es_descanso',
        'generado_by',
        'generado_at',
        'bloqueado',
        'justificacion_retraso',
        'hora_limite',
        'motivo_bloqueo',
        'bloqueado_por',
        'bloqueado_en',
        'confirmado_por',
        'confirmado_en',
        'presencia_confirmada',
        'presencia_at',
        'presencia_lat',
        'presencia_lng',
        'es_puntero_confirmado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'es_puntero' => 'boolean',
        'es_descanso' => 'boolean',
        'bloqueado' => 'boolean',
        'generado_at' => 'datetime',
        'bloqueado_en' => 'datetime',
        'confirmado_en' => 'datetime',
        'presencia_confirmada' => 'boolean',
        'presencia_at' => 'datetime',
        'presencia_lat' => 'decimal:6',
        'presencia_lng' => 'decimal:6',
        'es_puntero_confirmado' => 'boolean',
    ];

    public function ciclo()
    {
        return $this->belongsTo(CicloAnual::class, 'ciclo_id');
    }

    public function punto()
    {
        return $this->belongsTo(Punto::class, 'punto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }

    public function generador()
    {
        return $this->belongsTo(User::class, 'generado_by');
    }

    public function bloqueador()
    {
        return $this->belongsTo(User::class, 'bloqueado_por');
    }
    
    public function salidas()
    {
        return $this->hasMany(Salida::class, 'turno_id');
    }

    public function esPuntero(): bool
    {
        return $this->es_puntero == 1;
    }

    public function esDescanso(): bool
    {
        return $this->es_descanso == 1;
    }

    public function esConductor(): bool
    {
        return $this->es_puntero == 0 && $this->es_descanso == 0;
    }

    public function estaBloqueado(): bool
    {
        return $this->bloqueado == 1;
    }

    public function tieneLimiteHora(): bool
    {
        return ! empty($this->hora_limite);
    }

    public function haSuperadoLimite(): bool
    {
        if (! $this->tieneLimiteHora()) {
            return false;
        }

        return now()->format('H:i:s') > $this->hora_limite;
    }

    public static function getTurnosDelDia($fecha, $puntoId = null)
    {
        $query = self::with(['usuario', 'unidad', 'punto', 'bloqueador'])
            ->whereDate('fecha', $fecha);

        if ($puntoId) {
            $query->where('punto_id', $puntoId);
        }

        return $query->orderBy('posicion')->get();
    }

    public static function getTurnosActivos($fecha, $puntoId)
    {
        return self::with(['usuario', 'unidad'])
            ->whereDate('fecha', $fecha)
            ->where('punto_id', $puntoId)
            ->where('bloqueado', 0)
            ->orderBy('posicion')
            ->get();
    }

    public static function getPunteros($fecha, $puntoId)
    {
        return self::with(['usuario'])
            ->whereDate('fecha', $fecha)
            ->where('punto_id', $puntoId)
            ->where('es_puntero', 1)
            ->orderBy('posicion')
            ->get();
    }

    public static function unidadDescansaHoy(Unidad $unidad, $fecha)
    {
        return self::where('unidad_id', $unidad->unidad_id)
            ->whereDate('fecha', $fecha)
            ->where('es_descanso', 1)
            ->exists();
    }
}
