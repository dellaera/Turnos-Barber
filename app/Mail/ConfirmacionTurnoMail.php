<?php

namespace App\Mail;

use App\Models\Turno;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmacionTurnoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Turno $turno)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de tu turno en ' . $this->turno->barberia->nombre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.turnos.confirmacion',
            with: [
                'turno' => $this->turno->loadMissing(['barberia', 'barbero', 'servicio']),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
