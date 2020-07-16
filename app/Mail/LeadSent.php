<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadSent extends Mailable
{
    use Queueable, SerializesModels;

    public $lead;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($lead)
    {
        $this->lead = $lead;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailable = $this
                    ->subject($this->lead->subject . ' -||' . $this->lead->id)
                    ->view('mails.leadsent');

        if($this->lead->attachment){
            $attachment = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->lead->attachment);
            if(is_file($attachment)){
                $mailable->attach($attachment);
            }
        }        

        return $mailable;
    }
}
