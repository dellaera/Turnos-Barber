<?php

namespace App\Models;

use Carbon\Carbon;
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
        'recordatorio_24_enviado',
        'recordatorio_1_enviado',
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

    public function getFechaHoraAttribute(): Carbon
    {
        return Carbon::parse("{$this->fecha} {$this->hora}");
    }
}
