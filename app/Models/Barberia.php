<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barberia extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'nombre',
        'direccion',
        'telefono',
        'slug',
        'logo_url',
        'color_primario',
        'color_secundario',
        'mensaje_bienvenida',
        'informacion_contacto',
    ];

    public function barberos()
    {
        return $this->hasMany(Barbero::class);
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
