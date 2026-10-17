<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'usuario_id';

    public $timestamps = false;

    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'username',
        'password',
        'password_hash',
        'rol_id',
        'punto_id',
        'unidad_id',
        'activo',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function punto()
    {
        return $this->belongsTo(Punto::class, 'punto_id');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by', 'usuario_id');
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class, 'usuario_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'usuario_id');
    }

    public function getNameAttribute()
    {
        return $this->nombres.' '.$this->apellidos;
    }

    public function getRolNombreAttribute()
    {
        return $this->rol ? $this->rol->nombre : null;
    }

    public function getPuntoNombreAttribute()
    {
        return $this->punto ? $this->punto->nombre : 'Sin punto';
    }

    public function isGerente(): bool
    {
        return $this->rol_id === 1;
    }

    public function isTarjetera(): bool
    {
        return $this->rol_id === 2;
    }

    public function isConductor(): bool
    {
        return $this->rol_id === 3;
    }

    public function esActivo(): bool
    {
        return $this->activo === true;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
}
