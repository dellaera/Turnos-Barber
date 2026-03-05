<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'barberia_id',
        'nombre',
        'telefono',
        'email',
    ];

    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }

    public function barberia()
    {
        return $this->belongsTo(Barberia::class);
    }

    public function ultimoTurno()
    {
        return $this->hasOne(Turno::class)->latestOfMany();
    }
}
