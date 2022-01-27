<?php

namespace App\Http\Controllers;

use App\DataTables\LeadsDataTable;
use App\LeadMails;
use App\Mail\ErrorMail;
use App\Mail\LeadSent;
use App\Mail\ReportMail;
use App\Priority;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mail;
use Web64\Colors\Facades\Colors;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\GetMessagesFailedException;
use Webklex\IMAP\Facades\Client;

class MailBoxController extends Controller {

    private $attachment_filename;
    private $body;
    private $newUser;

    /**
     * This is called by the schedule:run setup in CRONTAB
     *
     * This performs the handling of any emails in the IMAP Inbox
     *
     * @return void
     * @throws ConnectionFailedException
     * @throws GetMessagesFailedException
     */
    public function index()
    {
        // Added for debug
        if (config('app.debug'))
        {
            if (app()->runningInConsole())
            {
                $this->echod('red', "Running MalBoxController in Console", __LINE__);
            }
        }

        $oClient = Client::account('default');
        $oClient->connect();

        $aFolder[] = $oClient->getFolder('INBOX');
        $this->echod('yellow', 'Looking Through the Inbox', __LINE__);
        //Loop through the mailbox - We are only checking one folder at the moment
        foreach ($aFolder as $oFolder)
        {
            //Get all Messages from the current Mailbox $oFolder from a day ago
            $aMessage = $oFolder->query(NULL)->unseen()->since(Carbon::now()->subDays(2))->get();

            foreach ($aMessage as $oMessage)
            {
                echo $oMessage->getSubject() . "\r\n";
                echo 'Attachments: ' . $oMessage->getAttachments()->count() . "\r\n";

                $this->save_attachment($oMessage);
                $this->body = $oMessage->getHTMLBody() ?: $oMessage->getTextBody();

                // @todo - These need to be tested
                $emailFirstWord = trim(strtolower(explode(' ', strip_tags(preg_replace('#(<title.*?>).*?(</title>)#', '$1$2', $this->body)))[0]));
                $emailContent = strip_tags(str_replace('<br/>', ' ', str_replace('<br>', ' ', $this->body)));

                // If an email comes with the first word in the message "Spam"
                // It could:
                // 1. Be an existing lead with a message_id where an agent has added the word Spam <some reason>
                // 2. It is a new Email, and has to be treated as such so admin can deal with it.
                //
                if (strpos($emailFirstWord, 'spam') !== FALSE)
                {
                    // Body: Spam <Reason>
                    $subject_array = explode('-||', $oMessage->getSubject());
                    // We might Get back an array [0] = xxxxx and [1] 1234
                    $originalMessageId = $subject_array[1] ?? FALSE; // Either get a ID or its 0

                    if ($originalMessageId)
                    {
                        $lead = LeadMails::find($originalMessageId); // The Primary Key so we are passing in the value for ID in this case
                        $lead->rejected = 1;
                        $lead->rejected_message = $this->extract_rejected_message_from_body($emailContent, $lead->body);
                        $lead->save($oMessage);
                    } else
                    {
                        // We want to find the row we need to save based upon email_imap_id field
                        // We cannot use find() as it searches only on the Primary Key

                        $lead = LeadMails::where('email_imap_id', $oMessage->message_id)->first();
                        if ( ! ($lead && $lead->count()))
                        {
                            $this->save_new_lead($oMessage);
                            $this->echod('white', 'Save New Leads', __LINE__);
                        } else
                        {
                            $this->echod('white', 'This Lead already exists', __LINE__);
                        }
                    }
                } elseif (strpos($emailFirstWord, 'test') !== FALSE)
                {
                    $this->save_new_lead($oMessage, TRUE);

                    // Body:  xxx@yyy.zzz! [Agent Email Address]
                } elseif (filter_var(explode('!', $emailFirstWord)[0], FILTER_VALIDATE_EMAIL))
                {
                    $this->echod('yellow', 'We are here', __LINE__);
                    $originalMessageId = explode('-||', $oMessage->getSubject())[1];
                    $this->newUser = User::where('email', explode('!', $emailFirstWord)[0])->get(['id', 'email']);
                    if ($this->newUser->count())
                    {
                        $this->echod('yellow', 'We are here', __LINE__);
                        $lead = LeadMails::find($originalMessageId);
                        $lead->reassigned_message = str_replace(explode(' ', strip_tags($this->body))[0], '', $emailContent);
                        $lead->save();
                        $this->echod('green', 'Agent Redirect - Found', __LINE__);
                        $this->sendIndividualLead($originalMessageId, $this->newUser->first());
                    } else
                    {
                        $lead = LeadMails::find($originalMessageId);
                        $this->echod('red', 'Agent Redirect - Not Found', __LINE__);
                        Mail::to($lead->agent()->first()->email)->bcc('timbrownlaw@gmail.com')->send(new ErrorMail($lead, 'Agent not found with e-mail: ' . explode('!', $emailFirstWord)[0] . '. Please check the spelling.'));
                    }
                } else
                { //Count as a new Lead
                    // Where we have no subject line Message ID so check it against all known Imap Ids
                    $lead = LeadMails::where('email_imap_id', $oMessage->message_id);
                    if ( ! $lead->get()->count())
                    {
                        $this->save_new_lead($oMessage);
                        $this->echod('white', 'New Lead Saved', __LINE__);
                    }
                }
            }
        }
    }

    /**
     * Extract any added text to the beginning of an existing string
     *
     * @param $sBody
     * @param $dbBody
     * @return string
     */
    private function extract_rejected_message_from_body($sBody, $dbBody)
    {
        $dbBody = trim($dbBody);
        $sBody = trim($sBody);
        $dbBodyLength = strlen($dbBody);
        $sBodyLength = strlen($sBody);
        $aMessage = substr($sBody, 0, $sBodyLength - $dbBodyLength);
        $message = str_ireplace('spam', '', $aMessage);

        return trim(preg_replace('/[^a-z0-9 \'.]/i', '', $message));
    }

    /**
     * Saves a New Lead
     * @param      $oMessage
     * @param bool $test
     */
    private function save_new_lead($oMessage, $test = FALSE)
    {
        $lead = new LeadMails();
        $lead->email_imap_id = $oMessage->message_id;
        $lead->email_from = $oMessage->getFrom()[0]->mail; // @TB Correct
        $lead->agent_id = 0;
        $lead->subject = $oMessage->getSubject();
        $lead->body = $this->body;
        $lead->attachment = $this->attachment_filename;
        $lead->received_date = $oMessage->date;
        $lead->priority = 100;

        if ($test != FALSE)
        {
            $lead->rejected = 1;
            $lead->rejected_message = "Test Email - IGNORE";
            $lead->agent_id = 1;
            $lead->old_agent_id = 1;
        }

        $lead->save();
        $this->apply_rules_and_priorities($lead);

    }

    /**
     * Apply the Rules and Priorities to each Email
     *
     * @todo Is this correct? The code as it stands will allow a match from either Subject or Sender to email the lead directly
     *
     * @param $lead
     */
    private function apply_rules_and_priorities($lead)
    {
        foreach (Priority::all() as $priority)
        {
            echo $priority->id . " - " . $priority->description . " - " . $priority->condition . "\r\n";
            switch ($priority->field)
            {
                case 1: // Subject
                {
                    if (strpos(strtolower($lead->subject), strtolower($priority->condition)) !== FALSE)
                    {
                        $this->send_lead_to_destination($lead, $priority);
                        $this->echod('green', 'P Subject: ' . $lead->subject . ' - ' . $priority->condition, __LINE__);
                        break 2;
                    }
                    break;
                }
                case 2: // From Email Address
                {
                    if (strtolower($lead->email_from) == strtolower($priority->condition))
                    {
                        $this->send_lead_to_destination($lead, $priority);
                        $this->echod('green', 'P Email: ' . $lead->subject . ' - ' . $priority->condition, __LINE__);
                        break 2;
                    }
                    break;
                }
                default:
                {
                    break;
                }
            }
        }
    }

    /**
     * Send Email as set in rules
     *
     * @param $lead
     * @param $priority
     */
    private function send_lead_to_destination($lead, $priority)
    {
        if (trim($priority->send_to_email) != '')
        {
            $this->newUser = User::where('email', $priority->send_to_email)->get(['id', 'email']);
            if ($this->newUser->count())
            {
                $this->sendIndividualLead($lead->id, $this->newUser->first());
                $this->echod('yellow', 'New User', __LINE__);
            } else
            {
                $this->sendIndividualLead($lead->id, NULL, $priority->send_to_email);
            }
        }

        $lead->priority = $priority->priority;
        $lead->to_group = $priority->user_group;
        $lead->save();
    }

    /**
     * Save any attachments
     * Can an email have more than one attachment?
     *  Answer: NO, there is only provision for One Attachment in the Table and Only One is looked for.
     *  Any other attachments will get lost.
     *
     * @param $oMessage
     * @return void
     * @prop $attachment_filename - sets this property
     */
    private function save_attachment($oMessage)
    {
        if ($oMessage->getAttachments()->count())
        {
            $attachment = $oMessage->getAttachments()->first();
            $masked_attachment = $attachment->mask();
            $token = implode('-', [$masked_attachment->id, $masked_attachment->getMessage()->getUid(), $masked_attachment->name]);
            $token = 'attc' . str_replace(' ', '_', $token);
            $this->attachment_filename = str_replace("/", "", str_replace("'", "", $token));
            \Storage::disk('public')->put($this->attachment_filename, $masked_attachment->getContent());
        }
    }

    public function manage(Request $request)
    {
        //public function manage(LeadsDataTable $dataTable){

        if ( ! count($request->input()))
        {

            $leadMails = LeadMails::orderBy('id', 'asc')
                ->limit(1)
                ->get()
                ->first();

            if ($leadMails)
            {
                //$dateFrom   = \Carbon\Carbon::parse($leadMails->created_at)->startOfDay();

                $dateFrom = Carbon::now()->subDays(30)->startOfDay();
            } else
            {
                $dateFrom = Carbon::now()->startOfDay();
            }

            $dateTo = Carbon::now()->endOfDay();
        } else
        {
            $dateFrom = Carbon::parse($request->input('from-date'))->startOfDay();
            $dateTo = Carbon::parse($request->input('to-date'))->endOfDay();
        }

        $users = User::all();

        $leadMails = LeadMails::where('updated_at', '>=', $dateFrom)
            ->where('updated_at', '<=', $dateTo)
            ->orderBy('id', 'desc')->get();

        return view('pages.emailsmanage', compact('leadMails', 'dateFrom', 'dateTo', 'users'));

//        return $dataTable->render('pages.emailsmanagedatatable');

    }

    public function datatables(LeadsDataTable $dataTable)
    {

        //dataTable($query)
    }

    public function transferLead(Request $request, $leadId, $userId)
    {

        $user = User::where('id', $userId)->first();
        if ($user)
        {
            $lead = $this->sendIndividualLead($leadId, $user, $user->email);

            return json_encode(array('success' => 'Lead #' . $leadId . ' successfully transferred to agent: ' . $user->email));
        } else
        {
            return json_encode(array('error' => 'User ID: ' . $userId . ' not found'));
        }

    }

    public function sendLeads(Request $request)
    {

        $user = \Auth::user();

        $currentTime = 1 * (explode(':', explode(' ', Carbon::now()->setTimeZone('America/New_York'))[1])[0] . explode(':', explode(' ', Carbon::now()->setTimeZone('America/New_York'))[1])[1]);
        $time_set_init = 1 * (explode(':', $user->time_set_init)[0] . explode(':', $user->time_set_init)[1]);
        $time_set_final = 1 * (explode(':', $user->time_set_final)[0] . explode(':', $user->time_set_final)[1]);

        if (LeadMails::where('agent_id', $user->id)->where('updated_at', '>', Carbon::now()->subDay())->count() < $user->leads_allowed)
        {
            if ($currentTime >= $time_set_init && $currentTime <= $time_set_final)
            {

                if ($user->user_group == 1)
                {
                    $leadMails = LeadMails::where('rejected', 0)
                        ->where('agent_id', 0)
                        ->where('to_group', $user->user_group)
                        ->orderBy('to_group', 'desc')
                        ->orderBy('priority')
                        ->orderBy('updated_at')
                        ->limit(1)
                        ->get(['id', 'email_from', 'agent_id', 'subject', 'body', 'attachment', 'received_date', 'priority', 'rejected', 'to_veteran']);
                } else
                {
                    $leadMails = LeadMails::where('rejected', 0)
                        ->where('agent_id', 0)
                        //->whereIn('to_group', [null,0,$user->user_group])
                        //->whereNull('to_veteran')
                        ->orderBy('priority')
                        ->orderBy('updated_at')
                        ->limit(1)
                        ->get(['id', 'email_from', 'agent_id', 'subject', 'body', 'attachment', 'received_date', 'priority', 'rejected', 'to_veteran']);

                    //dd($leadMails->toSql());
                }

                foreach ($leadMails as $lead)
                {
                    Mail::to($user->email)->send(new LeadSent($lead));
                    $lead->agent_id = $user->id;
                    $lead->assigned_date = Carbon::now();
                    $lead->save();
                }

            } else
            {
                return array('type' => 'ERROR', 'message' => 'You are not in the Allowed Period!');
            }
        } else
        {
            return array('type' => 'ERROR', 'message' => 'You have reached your 24h leads limit!');
        }

        return array('type' => 'SUCCESS', 'message' => count($leadMails) . ' Lead ' . (count($leadMails) > 1 ? 'have' : 'has') . ' been sent to your e-mail!', 'leads' => count($leadMails));

    }

    /**
     *
     * @param        $leadId
     * @param        $user
     * @param string $forceEmail
     * @return mixed
     */
    public function sendIndividualLead($leadId, $user, $forceEmail = '')
    {
        $lead = LeadMails::find($leadId);

        if ( ! $user)
        { // Sending an e-mail to a non-user
            $lead->agent_id = -1;
            $mailable = Mail::to($forceEmail)->send(new LeadSent($lead));
            //@todo Fix Logic - What is $forceEmail is not set?
        } else
        {
            if ($lead->agent_id > 0)
            {
                $lead->old_agent_id = $lead->agent_id;
                $lead->old_assigned_date = $lead->assigned_date;
            }
            $lead->agent_id = $user->id;
            $lead->assigned_date = Carbon::now();

            // We need to send the attachment as well
            $mailable = Mail::to($user->email)->send(new LeadSent($lead));
        }

        $lead->save();

        return $mailable;
    }

    public function downloadAttachment($leadId)
    {
        $lead = LeadMails::find($leadId);

        return redirect(\Storage::url($lead->attachment));
    }

    /**
     * Called from the Admin Email View via AJAX Call to populate a Modal
     *
     * @param $leadId
     * @return false|string
     */
    public function getBody($leadId)
    {
        $lead = LeadMails::find($leadId);
        $content = $this->parseMailBody($lead->body);
//        //@todo not tested with text email
//        if ( ! $this->isHTML($content)) {
//            $content = nl2br($content);
//        } else {
//            $content = preg_replace("/\r\n/", "", $content);
//        }

        return json_encode(array('body' => base64_encode($content)));
    }

    public function getReassigned($leadId)
    {
        $lead = LeadMails::find($leadId);
        $content = $this->parseMailBody($lead->reassigned_message);

        return json_encode(array('body' => base64_encode(explode('On', $content)[0])));
    }

    public function getRejected($leadId)
    {
        $lead = LeadMails::find($leadId);
        $content = $this->parseMailBody($lead->rejected_message);

        return json_encode(array('body' => base64_encode(explode('On', $content)[0])));
    }

    /**
     *
     *
     * @param $content
     * @return string
     */
    private function parseMailBody($content)
    {
        //@todo not tested with text email
        if ($this->isHTML($content))
        {
            $content = preg_replace("/\r\n/", "", $content);
        } else
        {
            $content = nl2br($content);
        }

        return $content;
    }


    public function report(Request $request, $dateFrom, $dateTo)
    {

        $leads = \DB::select(\DB::raw(
            "
            SELECT 	LM.agent_id,
                    U.name AS agent_name,
                    COUNT(*) AS leads_count,
                    SUM(CASE WHEN IFNULL(LM.old_agent_id, 0) > 0 THEN
                                1
                            ELSE
                                0
                        END
                    ) AS leads_reassigned,
                    SUM(LM.rejected) AS leads_rejected,
                    MAX(CONVERT_TZ(LM.updated_at, '+00:00', '-05:00')) AS last_lead
            FROM lead_mails LM
                INNER JOIN users U ON
                    U.id = LM.agent_id
            WHERE   LM.updated_at >= '" . $dateFrom . " 00:00:00' AND
                    LM.updated_at <= '" . $dateTo . " 23:59:59'
            GROUP BY LM.agent_id, U.name

            UNION ALL

            SELECT 	LM.agent_id,
                    'Not Assigned' agent_name,
                    COUNT(*) AS leads_count,
                    0 AS leads_rejected,
                    SUM(LM.rejected) AS leads_rejected,
                    MAX(CONVERT_TZ(LM.updated_at, '+00:00', '-05:00')) AS last_lead
            FROM lead_mails LM
            WHERE   LM.updated_at >= '" . $dateFrom . " 00:00:00' AND
                    LM.updated_at <= '" . $dateTo . " 23:59:59' AND
                    LM.agent_id = 0
            GROUP BY LM.agent_id
            "
        ));

        return $leads;
    }

    public function reportEmail(Request $request, $dateFrom, $dateTo)
    {

        $leads = \DB::select(\DB::raw(
            "
            SELECT 	LM.agent_id,
                    U.name AS agent_name,
                    COUNT(*) AS leads_count,
                    SUM(CASE
                            WHEN IFNULL(LM.old_agent_id, 0) > 0 THEN
                                1
                            ELSE
                                0
                        END
                    ) AS leads_reassigned,
                    SUM(LM.rejected) AS leads_rejected,
                    MAX(CONVERT_TZ(LM.updated_at, '+00:00', '-05:00')) AS last_lead
            FROM lead_mails LM
                INNER JOIN users U ON
                    U.id = LM.agent_id
            WHERE   LM.updated_at >= '" . $dateFrom . " 00:00:00' AND
                    LM.updated_at <= '" . $dateTo . " 23:59:59'
            GROUP BY LM.agent_id, U.name

            UNION ALL

            SELECT 	LM.agent_id,
                    'Not Assigned' agent_name,
                    COUNT(*) AS leads_count,
                    0 AS leads_rejected,
                    SUM(LM.rejected) AS leads_rejected,
                    MAX(CONVERT_TZ(LM.updated_at, '+00:00', '-05:00')) AS last_lead
            FROM lead_mails LM
            WHERE   LM.updated_at >= '" . $dateFrom . " 00:00:00' AND
                    LM.updated_at <= '" . $dateTo . " 23:59:59' AND
                    LM.agent_id = 0
            GROUP BY LM.agent_id
            "
        ));

        Mail::to(\Auth::user()->email)
            ->bcc('timbrownlaw@gmail.com')
            ->bcc('visiontocode2022@gmail.com')
            ->send(new ReportMail($leads, $dateFrom, $dateTo));

        return json_encode(array('type' => 'SUCCESS', 'message' => 'E-mail Report was sent to ' . \Auth::user()->email));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $leadId
     * @return Response
     */
    public function destroy($leadId): Response
    {
        $lead = LeadMails::find($leadId);
        $lead->delete();

        return redirect()->route('emails.manage')->withStatus(__('Lead successfully deleted.'));
    }

    /**
     * If the .env App.debug is True - Show the Fancy Color Command Line
     * else just show the standard output
     *
     * @param $color
     * @param $text
     * @param $line
     */
    private function echod($color, $text, $line)
    {
        if (config('app.debug'))
        {
            Colors::nobr()->bgLightYellow("Line: " . $line . ' ');
            Colors::{$color}(' ' . $text);
        } else
        {
            echo $text;
        }

    }

    /**
     *
     *
     * @param $string
     * @return bool
     */
    private function isHTML($string)
    {
        return ($string != strip_tags($string));
    }
}
