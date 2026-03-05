<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
        'barberia_id',
        'barbero_id',
        'servicio_id',
        'cliente_id',
        'fecha',
        'hora',
        'estado',
    ];

    public function barberia()
    {
        return $this->belongsTo(Barberia::class);
    }

    public function barbero()
    {
        return $this->belongsTo(Barbero::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
