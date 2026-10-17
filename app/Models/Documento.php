<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $primaryKey = 'documento_id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'tipo',
        'numero',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'updated_at',
        'updated_by',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function esVencido(): bool
    {
        return $this->fecha_vencimiento < now()->toDateString();
    }

    public function diasParaVencer(): int
    {
        return now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function estaPorVencer(int $dias = 30): bool
    {
        $diasRestantes = $this->diasParaVencer();

        return $diasRestantes > 0 && $diasRestantes <= $dias;
    }

    public function getTipoNombreAttribute(): string
    {
        return match ($this->tipo) {
            'licencia' => 'Licencia de Conducir',
            'matricula' => 'Matrícula Vehicular',
            default => $this->tipo,
        };
    }

    public function getEstadoBadgeClassAttribute(): string
    {
        return match ($this->estado) {
            'vigente' => 'bg-green-100 text-green-700',
            'vencido' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public static function verificarVencimientos(): array
    {
        $documentos = self::with('usuario')->get();
        $vencidos = [];
        $porVencer = [];

        foreach ($documentos as $doc) {
            $nuevoEstado = $doc->esVencido() ? 'vencido' : 'vigente';

            if ($nuevoEstado !== $doc->estado) {
                $doc->estado = $nuevoEstado;
                $doc->updated_at = now();
                $doc->save();
            }

            if ($doc->esVencido()) {
                $vencidos[] = $doc;
            } elseif ($doc->estaPorVencer()) {
                $porVencer[] = $doc;
            }
        }

        return ['vencidos' => $vencidos, 'porVencer' => $porVencer];
    }
}
