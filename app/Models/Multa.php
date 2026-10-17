<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Multa extends Model
{
    use HasFactory;

    protected $table = 'multas';

    protected $primaryKey = 'multa_id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'turno_id',
        'salida_id',
        'llegada_id',
        'tipo',
        'monto',
        'minutos_retraso',
        'descripcion',
        'estado',
        'registrado_por',
        'registrado_en',
        'cobrado_por',
        'cobrado_en',
    ];

    protected $casts = [
        'registrado_en' => 'datetime',
        'cobrado_en' => 'datetime',
        'monto' => 'decimal:2',
        'minutos_retraso' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }

    public function llegada()
    {
        return $this->belongsTo(LlegadaGps::class, 'llegada_id');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function cobrador()
    {
        return $this->belongsTo(User::class, 'cobrado_por');
    }

    public function getTipoDisplayAttribute(): string
    {
        return match ($this->tipo) {
            'retraso_llegada' => 'Retraso en llegada',
            'ausencia' => 'Ausencia',
            'documento_vencido' => 'Documento vencido',
            'indisciplina' => 'Indisciplina',
            default => $this->tipo,
        };
    }

    public function getEstadoDisplayAttribute(): string
    {
        return match ($this->estado) {
            'pendiente' => 'Pendiente',
            'cobrada' => 'Cobrada',
            'anulada' => 'Anulada',
            default => $this->estado,
        };
    }

    public function getEstadoBadgeClassAttribute(): string
    {
        return match ($this->estado) {
            'pendiente' => 'bg-amber-100 text-amber-700',
            'cobrada' => 'bg-green-100 text-green-700',
            'anulada' => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public static function crearPorRetraso($usuarioId, $turnoId, $llegadaId, $minutosRetraso, $valorPorMinuto)
    {
        $monto = $minutosRetraso * $valorPorMinuto;
        $registradoPor = Auth::check() ? Auth::id() : null;

        return self::create([
            'usuario_id' => $usuarioId,
            'turno_id' => $turnoId,
            'llegada_id' => $llegadaId,
            'tipo' => 'retraso_llegada',
            'monto' => $monto,
            'minutos_retraso' => $minutosRetraso,
            'descripcion' => "Retraso de {$minutosRetraso} minutos. \${$valorPorMinuto}/min = \${$monto}",
            'estado' => 'pendiente',
            'registrado_por' => $registradoPor,
            'registrado_en' => now(),
        ]);
    }
}
