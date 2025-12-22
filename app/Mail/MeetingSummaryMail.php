<?php

namespace App\Mail;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeetingSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $meeting;

    /**
     * Create a new message instance.
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
        // ✅ Pasamos los datos aquí explícitamente
        $this->with(['meeting' => $meeting]);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🧠 Resumen de tu reunión: ' . ($this->meeting->titulo ?? 'Sin título'))
                    ->markdown('emails.meeting-summary');
    }
}
