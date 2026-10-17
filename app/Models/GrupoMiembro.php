<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoMiembro extends Model
{
    protected $table = 'grupo_miembros';

    protected $primaryKey = 'miembro_id';

    public $timestamps = false;

    protected $fillable = [
        'grupo_id',
        'unidad_id',
    ];

    public function grupo()
    {
        return $this->belongsTo(GrupoDescanso::class, 'grupo_id');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }
}
