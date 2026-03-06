<?php

namespace App\Mail;

use App\Models\Turno;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecordatorioTurnoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Turno $turno, public string $anticipacion)
    {
    }

    public function build(): self
    {
        return $this
            ->subject("Recordatorio de tu turno en {$this->turno->barberia->nombre}")
            ->view('emails.turnos.recordatorio');
    }
}
