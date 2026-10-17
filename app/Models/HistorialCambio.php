<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialCambio extends Model
{
    protected $table = 'historial_cambios';

    protected $primaryKey = 'historial_id';

    public $timestamps = false;

    protected $fillable = [
        'tabla_afectada',
        'registro_id',
        'accion',
        'campo_modificado',
        'valor_anterior',
        'valor_nuevo',
        'usuario_id',
        'timestamp',
        'ip_origen',
        'punto_id',
        'descripcion',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function punto()
    {
        return $this->belongsTo(Punto::class, 'punto_id');
    }

    public static function registrar($tabla, $registroId, $accion, $usuarioId, $campo = null, $anterior = null, $nuevo = null, $ip = null, $puntoId = null, $descripcion = null)
    {
        $accionNormalizada = match (strtolower($accion)) {
            'insert', 'crear' => 'crear',
            'update', 'modificar', 'generar' => 'modificar',
            'delete', 'eliminar' => 'eliminar',
            default => $accion
        };

        return self::create([
            'tabla_afectada' => $tabla,
            'registro_id' => $registroId,
            'accion' => $accionNormalizada,
            'campo_modificado' => $campo,
            'valor_anterior' => $anterior,
            'valor_nuevo' => $nuevo,
            'usuario_id' => $usuarioId,
            'timestamp' => now(),
            'ip_origen' => $ip,
            'punto_id' => $puntoId,
            'descripcion' => $descripcion,
        ]);
    }
}