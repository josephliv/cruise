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

    /**
     * This is called by the schedule:run setup in CRONTAB
     *
     * This performs the initial "sorting" of any incoming emails
     *
     * @return void
     * @throws ConnectionFailedException
     * @throws GetMessagesFailedException
     */
    public function index() {

        if (app()->runningInConsole()) {
//           Colors::red("Running MalBoxController in Console");
            $this->echod('red', " Running MalBoxController in Console", __LINE__);
        }

        $oClient = Client::account('default');
        $oClient->connect();
        $aFolder[] = $oClient->getFolder('INBOX');

        //Loop through the mailbox
        foreach ($aFolder as $oFolder) {
            //Get all Messages from the current Mailbox $oFolder
            $aMessage = $oFolder->query(NULL)->unseen()->limit(5, 1)->get();

            foreach ($aMessage as $oMessage) {
                echo $oMessage->getSubject() . "\r\n";
                echo 'Attachments: ' . $oMessage->getAttachments()->count() . "\r\n";
                $this->save_attachment($oMessage);

                // Read the HTML or Text Body
                $body = $oMessage->getHTMLBody() ?: $oMessage->getTextBody();

                // Not quite sure of the Test Cases for these but will leave them in assuming they have some meaning I am unaware of.
                // Only applies to HTML.
                $emailFirstWord = trim(strtolower(explode(' ', strip_tags(preg_replace('#(<title.*?>).*?(</title>)#', '$1$2', $body)))[0]));
                $emailContent = strip_tags(str_replace('<br/>', ' ', str_replace('<br>', ' ', $body)));

                var_dump($emailFirstWord);
                // Check for Spam
                // Subject: xxxx -|| nnnn, where nnnn is the lead id
                // Body: spam and a reason!
                //
                if (strpos($emailFirstWord, 'spam') !== FALSE) {
                    $subject_array = explode('-||', $oMessage->getSubject());
                    // Get back an array [0] = xxxxx and [1] 1234
                    $originalMessageId = $subject_array[1] ?? FALSE; // Either get a ID or its FALSE

                    if ($originalMessageId) {
//                        $lead = LeadMails::where('email_imap_id', $oMessage->message_id);
                        $lead = LeadMails::find(trim($originalMessageId)); // The Primary Key so we are passing in the value for ID in this case
                        $lead->rejected = 1;
                        var_dump($lead->body);
                        var_dump($emailContent);
                        $lead->rejected_message = $this->extract_rejected_message_from_body($emailContent, $lead->body);
                        var_dump($lead->rejected_message);
                        $lead->save();
                    } else {
                        //Treat as a new incoming message - we will have to create it as it does not exist in the Database.

                    }

                    // is there an Email we need to use to perform a Re-assignment
                } elseif (filter_var(explode('!', $emailFirstWord)[0], FILTER_VALIDATE_EMAIL)) {
                    $originalMessageId = explode('-||', $oMessage->getSubject())[1];
                    $newUser = User::where('email', explode('!', $emailFirstWord)[0])->get(['id', 'email']);
                    if ($newUser->count()) {
                        $lead = LeadMails::find($originalMessageId);
                        $lead->reassigned_message = str_replace(explode(' ', strip_tags($body))[0], '', $emailContent);
                        $lead->save();

                        $this->sendIndividualLead($originalMessageId, $newUser->first());
                    } else {
                        $lead = LeadMails::where('email_imap_id', $oMessage->message_id);
                        $lead = LeadMails::find($originalMessageId);
                        //\Mail::to('timbrownlaw@gmail.com')->send(new ErrorMail($lead, 'Agent not found with e-mail: ' . explode('!', $emailFirstWord)[0] . '. Please check the spelling.'));
                        Mail::to($lead->agent()->first()->email)->cc('timbrownlaw@gmail.com')->send(new ErrorMail($lead, 'Agent not found with e-mail: ' . explode('!', $emailFirstWord)[0] . '. Please check the spelling.'));
                    }

                } else { //Count as a new Lead
                    $lead = LeadMails::where('email_imap_id', $oMessage->message_id);
                    if ( ! $lead->get()->count()) {
                        $lead = new LeadMails();
                        $lead->email_imap_id = $oMessage->message_id;
                        $lead->email_from = $oMessage->getFrom()[0]->mail; // @TB Correct
                        $lead->agent_id = 0;
                        $lead->subject = $oMessage->getSubject();
                        $lead->body = $body;
                        $lead->attachment = $this->attachment_filename;
                        $lead->received_date = $oMessage->date;
                        $lead->priority = 100;
                        $lead->save();

                        foreach (Priority::all() as $priority) {
                            echo $priority->field;
                            echo '<br>';
                            switch ($priority->field) {
                                case 1: // Subject
                                {
                                    // dump(array('Subject Line', $priority, array('subject' => $lead->subject, 'cond' => $priority->condition, 'conditional' => strpos(strtolower($lead->subject), strtolower($priority->condition)))));
                                    if (strpos(strtolower($lead->subject), strtolower($priority->condition)) !== FALSE) {
                                        // dump(array(strtolower($lead->subject), strtolower($priority->condition)));
                                        $lead->priority = $priority->priority;

                                        if (trim($priority->send_to_email) != '') {
                                            // dump(array('to_email', $priority->send_to_email));
                                            $newUser = User::where('email', $priority->send_to_email)->get(['id', 'email']);

                                            if ($newUser->count()) {
                                                // dump(array('to_email_user', $newUser));
                                                $this->sendIndividualLead($lead->id, $newUser->first());
                                            } else {
                                                // dump(array('to_email_user', 'not_user'));
                                                $this->sendIndividualLead($lead->id, NULL, $priority->send_to_email);
                                            }
                                        }

                                        $lead->priority = $priority->priority;
                                        $lead->to_group = $priority->user_group;
                                        $lead->save();
                                    }
                                    break;
                                }

                                case 2: // From Email Address
                                {
                                    //dump(array($priority->field,$priority));
                                    if (strtolower($lead->email_from) == strtolower($priority->condition)) {
                                        if (trim($priority->send_to_email) != '') {
                                            $newUser = User::where('email', $priority->send_to_email)->get(['id', 'email']);

                                            if ($newUser->count()) {
                                                $this->sendIndividualLead($lead->id, $newUser->first());
                                            } else {
                                                $this->sendIndividualLead($lead->id, NULL, $priority->send_to_email);
                                            }
                                        }

                                        $lead->priority = $priority->priority;
                                        $lead->to_group = $priority->user_group;
                                        $lead->save();
                                    }
                                    break;
                                }
                                default:
                                {
                                    // dump(array('default', $priority));
                                    break;
                                }
                            }
                        }
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
     * @return array|string|string[]|null
     */
    private function extract_rejected_message_from_body($sBody, $dbBody) {
        $dbBody = trim($dbBody);
        $sBody = trim($sBody);
        $dbBodyLength = strlen($dbBody);
        $sBodyLength = strlen($sBody);
        $aMessage = substr($sBody, 0, $sBodyLength - $dbBodyLength);

        $message = str_ireplace('spam', '', $aMessage);

        $this->echod('white', $dbBodyLength, __LINE__);
        $this->echod('white', $sBodyLength, __LINE__);
        $this->echod('white', $message, __LINE__);

        return trim(preg_replace('/[^a-z0-9 \'.]/i', '', $message));
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
    private function save_attachment($oMessage) {

        if ($oMessage->getAttachments()->count()) {
            $attachment = $oMessage->getAttachments()->first();
            $masked_attachment = $attachment->mask();
            $token = implode('-', [$masked_attachment->id, $masked_attachment->getMessage()->getUid(), $masked_attachment->name]);
            $token = 'attc' . str_replace(' ', '_', $token);
            $this->attachment_filename = str_replace("/", "", str_replace("'", "", $token));
            \Storage::disk('public')->put($this->attachment_filename, $masked_attachment->getContent());
        }
    }

    public function manage(Request $request) {
        //public function manage(LeadsDataTable $dataTable){

        if ( ! count($request->input())) {

            $leadMails = LeadMails::orderBy('id', 'asc')
                ->limit(1)
                ->get()
                ->first();

            if ($leadMails) {
                //$dateFrom   = \Carbon\Carbon::parse($leadMails->created_at)->startOfDay();

                $dateFrom = \Carbon\Carbon::now()->subDays(30)->startOfDay();
            } else {
                $dateFrom = \Carbon\Carbon::now()->startOfDay();
            }

            $dateTo = \Carbon\Carbon::now()->endOfDay();
        } else {
            $dateFrom = \Carbon\Carbon::parse($request->input('from-date'))->startOfDay();
            $dateTo = \Carbon\Carbon::parse($request->input('to-date'))->endOfDay();
        }

        $users = User::all();

        $leadMails = LeadMails::where('updated_at', '>=', $dateFrom)
            ->where('updated_at', '<=', $dateTo)
            ->orderBy('id', 'desc')->get();

        return view('pages.emailsmanage', compact('leadMails', 'dateFrom', 'dateTo', 'users'));

//        return $dataTable->render('pages.emailsmanagedatatable');

    }

    public function datatables(LeadsDataTable $dataTable) {

        //dataTable($query)
    }

    public function transferLead(Request $request, $leadId, $userId) {

        $user = User::where('id', $userId)->first();
        if ($user) {
            $lead = $this->sendIndividualLead($leadId, $user, $user->email);

            return json_encode(array('success' => 'Lead #' . $leadId . ' successfully transferred to agent: ' . $user->email));
        } else {
            return json_encode(array('error' => 'User ID: ' . $userId . ' not found'));
        }

    }

    public function sendLeads(Request $request) {

        $user = \Auth::user();

        $currentTime = 1 * (explode(':', explode(' ', \Carbon\Carbon::now()->setTimeZone('America/New_York'))[1])[0] . explode(':', explode(' ', \Carbon\Carbon::now()->setTimeZone('America/New_York'))[1])[1]);
        $time_set_init = 1 * (explode(':', $user->time_set_init)[0] . explode(':', $user->time_set_init)[1]);
        $time_set_final = 1 * (explode(':', $user->time_set_final)[0] . explode(':', $user->time_set_final)[1]);

        if (LeadMails::where('agent_id', $user->id)->where('updated_at', '>', Carbon::now()->subDay())->count() < $user->leads_allowed) {
            if ($currentTime >= $time_set_init && $currentTime <= $time_set_final) {

                if ($user->user_group == 1) {
                    $leadMails = LeadMails::where('rejected', 0)
                        ->where('agent_id', 0)
                        ->where('to_group', $user->user_group)
                        ->orderBy('to_group', 'desc')
                        ->orderBy('priority')
                        ->orderBy('updated_at')
                        ->limit(1)
                        ->get(['id', 'email_from', 'agent_id', 'subject', 'body', 'attachment', 'received_date', 'priority', 'rejected', 'to_veteran']);
                } else {
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

                foreach ($leadMails as $lead) {
                    Mail::to($user->email)->send(new LeadSent($lead));
                    $lead->agent_id = $user->id;
                    $lead->assigned_date = \Carbon\Carbon::now();
                    $lead->save();
                }

            } else {
                return array('type' => 'ERROR', 'message' => 'You are not in the Allowed Period!');
            }
        } else {
            return array('type' => 'ERROR', 'message' => 'You have reached your 24h leads limit!');
        }

        return array('type' => 'SUCCESS', 'message' => count($leadMails) . ' Lead ' . (count($leadMails) > 1 ? 'have' : 'has') . ' been sent to your e-mail!', 'leads' => count($leadMails));

    }

    public function sendIndividualLead($leadId, $user, $forceEmail = '') {
        $lead = LeadMails::find($leadId);

        if ( ! $user) { // Sending an e-mail to a non-user
            $lead->agent_id = -1;

            $mailable = Mail::to($forceEmail)->send(new LeadSent($lead));
        } else {
            if ($lead->agent_id > 0) {
                $lead->old_agent_id = $lead->agent_id;
                $lead->old_assigned_date = $lead->assigned_date;
            }
            $lead->agent_id = $user->id;
            $lead->assigned_date = \Carbon\Carbon::now();

            $mailable = Mail::to($user->email)->send(new LeadSent($lead));
        }

        $lead->save();

        return $mailable;
    }

    public function downloadAttachment($leadId) {
        $lead = LeadMails::find($leadId);

        return redirect(\Storage::url($lead->attachment));
    }

    public function getBody($leadId) {
        $lead = LeadMails::find($leadId);

        return json_encode(array('body' => base64_encode($lead->body)));
    }

    public function getReassigned($leadId) {
        $lead = LeadMails::find($leadId);

        return json_encode(array('body' => base64_encode(explode('On', $lead->reassigned_message)[0])));
    }

    public function getRejected($leadId) {
        $lead = LeadMails::find($leadId);

        return json_encode(array('body' => base64_encode(explode('On', $lead->rejected_message)[0])));
    }

    public function report(Request $request, $dateFrom, $dateTo) {

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

    public function reportEmail(Request $request, $dateFrom, $dateTo) {

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
            ->cc('visiontocode2022@gmail.com')
            ->send(new ReportMail($leads, $dateFrom, $dateTo));

        return json_encode(array('type' => 'SUCCESS', 'message' => 'E-mail Report was sent to ' . \Auth::user()->email));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     * @return void
     */
    public
    function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $leadId
     * @return Response
     */
    public
    function destroy($leadId): Response {
        $lead = LeadMails::find($leadId);
        $lead->delete();

        return redirect()->route('emails.manage')->withStatus(__('Lead successfully deleted.'));
    }

    /**
     * @param $color
     * @param $text
     * @param $line
     */
    private
    function echod($color, $text, $line) {
        Colors::nobr()->blue("Line: " . $line . ' ');
        Colors::{$color}($text);

    }
}
