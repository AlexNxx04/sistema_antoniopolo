<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $primaryKey = 'notif_id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'tipo',
        'mensaje',
        'enviado_at',
        'leido',
        'leido_at',
        'referencia_id',
        'referencia_tabla',
    ];

    protected $casts = [
        'enviado_at' => 'datetime',
        'leido_at' => 'datetime',
        'leido' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function marcarLeido()
    {
        $this->leido = 1;
        $this->leido_at = now();
        $this->save();
    }

    public static function getNoLeidas($usuarioId)
    {
        return self::where('usuario_id', $usuarioId)
            ->where('leido', 0)
            ->orderByDesc('enviado_at')
            ->get();
    }

    public static function getPorTipo($tipo)
    {
        return self::where('tipo', $tipo)
            ->orderByDesc('enviado_at')
            ->get();
    }
}
