<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leads;
    public $dateFrom;
    public $dateTo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($leads, $datefrom, $dateto)
    {
        $this->leads    = $leads;
        $this->dateFrom = $datefrom;
        $this->dateTo   = $dateto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.report')
                ->subject('LeadBox Report');
    }
}
