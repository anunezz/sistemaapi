<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public bool $sinImpedimentos;
    public ?string $cuerpo_correo;

    public function __construct(bool $sinImpedimentos, ?string $cuerpo_correo = null)
    {
        $this->sinImpedimentos = $sinImpedimentos;
        $this->cuerpo_correo   = $cuerpo_correo;
    }

    public function build()
    {
        $subject = $this->sinImpedimentos
            ? 'Resultado de verificación: con impedimentos'
            : 'Resultado de verificación: sin impedimentos';

        $title = $this->sinImpedimentos
            ? 'Impedimentos detectados'
            : 'Sin impedimentos detectados';

        $line = $this->sinImpedimentos
            ? 'Esta solicitud encontró impedimentos.'
            : 'Esta solicitud no encontró impedimentos.';

        return $this->subject($subject)
                    ->view('emails.verification_result')
                    ->with([
                        'title'         => $title,
                        'line'          => $line,
                        'cuerpo_correo' => $this->cuerpo_correo, // 👈 lo pasamos a la vista
                    ]);
    }
}
