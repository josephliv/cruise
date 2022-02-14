<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

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
     * @return LeadSent $mailable
     */
    public function build()
    {

       if(Config::get('app.env') == 'local') {
           $mailable = $this
               ->subject($this->lead->subject . ' -||' . $this->lead->id)
               ->replyTo('sales@cruiser.joesdigitalservices.com')
               ->bcc('timbrownlawswebsites@gmail.com')
               ->view('mails.leadsent');
       } else {
           $mailable = $this
               ->subject($this->lead->subject . ' -||' . $this->lead->id)
               ->replyTo('sales@cruisertravels.com')
               ->bcc('timbrownlawswebsites@gmail.com')
               ->bcc('joesdigitalservices@gmail.com')
               ->view('mails.leadsent');
       }

        Log::debug($this->lead->attachment);
        if($this->lead->attachment){
            $attachment = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->lead->attachment);
            Log::debug($attachment);
            if(is_file($attachment)){
                $mailable->attach($attachment);
            }
        }

        return $mailable;
    }
}
