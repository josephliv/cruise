<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lead;
    public $message_text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($lead, $message_text)
    {
        $this->lead         = $lead;
        $this->message_text = $message_text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailable = $this
                    ->subject($this->lead->subject . '-||' . $this->lead->id)
                    ->view('mails.error');

        return $mailable;
    }
}
