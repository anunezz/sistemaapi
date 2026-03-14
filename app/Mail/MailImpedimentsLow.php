<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Exception;
use App\Models\ImImpedimento;

class MailImpedimentsLow extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($im_impedimento,$solicitud)
    {
        $this->im_impedimento = $im_impedimento;
        $this->solicitud = $solicitud;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {

            return $this->subject("Se ha detectado un impedimento de baja con el número ".$this->im_impedimento->numero_impedimento)
            ->view('emails.impediments_low')
            ->with([
                'ImImpedimento' => $this->im_impedimento,
                'solicitud' => $this->solicitud
            ]);

        } catch (Exception $e) {
            \Log::info( json_encode($e) );
        }
    }
}
