<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'barberia_id',
        'nombre',
        'duracion_minutos',
        'precio',
    ];

    public function barberia()
    {
        return $this->belongsTo(Barberia::class);
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }
}
