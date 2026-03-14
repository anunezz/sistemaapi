<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Exception;
use App\Models\ImImpedimento;

class MailImpediments extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($im_impedimento,$id_tipo_solicitud)
    {
        $this->im_impedimento = $im_impedimento;
        $this->id_tipo_solicitud = $id_tipo_solicitud;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {
            $subject = "";
            switch ($this->id_tipo_solicitud) {
                case 4:
                    $subject = "Se ha detectado un impedimento de alta de modificación con el número ".$this->im_impedimento->numero_impedimento;
                break;
            }

            return $this->subject($subject)
            ->view('emails.impediments')
            ->with([
                'ImImpedimento' => $this->im_impedimento,
                'id_tipo_solicitud' => $this->id_tipo_solicitud
            ]);

        } catch (Exception $e) {
            \Log::info( json_encode($e) );
        }
    }
}
