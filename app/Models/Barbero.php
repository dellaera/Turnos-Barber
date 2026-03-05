<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barbero extends Model
{
    use HasFactory;

    protected $fillable = [
        'barberia_id',
        'nombre',
        'activo',
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
