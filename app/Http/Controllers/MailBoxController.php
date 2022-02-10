<?php

namespace App\Http\Controllers;

use App\DataTables\LeadsDataTable;
use App\LeadMails;
use App\Mail\ErrorMail;
use App\Mail\LeadSent;
use App\Mail\ReportMail;
use App\Priority;
use App\User;
use Auth;
use Carbon\Carbon;
use Colors;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mail;
use Storage;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Facades\Client;

class MailBoxController extends Controller {

    private $attachment_filename;
    private $body;
    private $newUser;

    /**
     * @var LeadMails[]|Builder[]|Collection
     */
    private $lead;

    public function __construct() {
        $this->lead = new LeadMails();
    }

    /**
     * This is called by the schedule:run setup in CRONTAB
     *
     * This performs the handling of any emails in the IMAP Inbox
     *
     * @return void
     * @throws ConnectionFailedException
     */
    public function index() {
        $this->detect_console_command();

        $this->echod('red',"Processing Mailboxes - Version 2.0", __LINE__);

        $oClient = $this->connect_to_imap_server();
        // Create an array of Mailbox Folder(s) we want to check
        $aFolder = $this->determine_mailboxes($oClient);
        // Process Each Mail Box
        foreach ($aFolder as $oFolder) {
            $this->echod('yellow', 'Processing: ' . $oFolder->name, __LINE__);
            $aMessage = $oFolder->query(NULL)->unseen()->limit(5)->since(Carbon::now()->subDays(9))->get();

            // Process Each Message
            foreach ($aMessage as $oMessage) {
                $subject_array = explode('-||', $oMessage->getSubject());
                $originalMessageId = $subject_array[1] ?? FALSE; // Either get a ID or its 0

                if ($originalMessageId) {
                    $this->lead = LeadMails::find($originalMessageId);
                } else {
                    $this->lead = LeadMails::where('email_imap_id', $oMessage->message_id)->first();
                }

                if ($this->lead && $this->lead->attachment) {
                    $this->attachment_filename = $this->lead->attachment;
                } else {
                    $this->save_attachment($oMessage);
                }

                $this->body = $oMessage->getHTMLBody() ?: $oMessage->getTextBody();
                $emailFirstWord = trim(strtolower(explode(' ', strip_tags(preg_replace('#(<title.*?>).*?(</title>)#', '$1$2', $this->body)))[0]));
                $emailContent = strip_tags(str_replace('<br/>', ' ', str_replace('<br>', ' ', $this->body)));

                $isMessageSpam = strpos($emailFirstWord, 'spam') !== FALSE;
                $isMessageTest = strpos($emailFirstWord, 'test') !== FALSE;
                $isMessageReassignment = filter_var(explode('!', $emailFirstWord)[0], FILTER_VALIDATE_EMAIL);
                $isExistingLead = $this->lead && $this->lead->count();

                if ($isExistingLead) {
                    if ($isMessageSpam) {
                        $this->lead->rejected = 1;
                        $this->lead->rejected_message = $this->extract_rejected_message_from_body($emailContent, $this->lead->body);
                        // Need to save
                        $this->lead->save();
                    } elseif ($isMessageTest) {
                        $this->save_new_lead($oMessage, TRUE); // Save it but reject it immediately
                    } elseif ($isMessageReassignment) {
                        $this->newUser = User::where('email', explode('!', $emailFirstWord)[0])->get(['id', 'email']);
                        $isValidAgentEmail = $this->newUser->count();
                        if ($isValidAgentEmail) {
                            $this->lead->reassigned_message = str_replace(explode(' ', strip_tags($this->body))[0], '', $emailContent);
                            $this->lead->save();
                            $this->sendIndividualLead($this->newUser->first());
                        } else {
                            if (defined('ENABLE_MAILER') && ENABLE_MAILER) {
                                // @todo - 1 - ErrorMail Class does not attach the attachment
                                Mail::to($this->lead->agent()->first()->email)->bcc('timbrownlaw@gmail.com')->send(new ErrorMail($this->lead, 'Agent not found with e-mail: ' . explode('!', $emailFirstWord)[0] . '. Please check the spelling.'));
                            }
                        }
                    }
                } else { //Count as a new Lead
                    // Where we have no subject line Message ID so check it against all known Imap Ids
                    $lead = LeadMails::where('email_imap_id', $oMessage->message_id);
                    if ( ! $lead->get()->count()) {
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
    private function extract_rejected_message_from_body($sBody, $dbBody) {
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
    private function save_new_lead($oMessage, bool $test = FALSE) {
        $this->lead->email_imap_id = $oMessage->message_id;
        $this->lead->email_from = $oMessage->getFrom()[0]->mail; // @TB Correct
        $this->lead->agent_id = 0;
        $this->lead->subject = $oMessage->getSubject();
        $this->lead->body = $this->body;
        $this->lead->attachment = $this->attachment_filename;
        $this->lead->received_date = $oMessage->date;
        $this->lead->priority = 100;

        if ($test != FALSE) {
            $this->lead->rejected = 1;
            $this->lead->rejected_message = "Test Email - IGNORE";
            $this->lead->agent_id = 1;
            $this->lead->old_agent_id = 1;
        }

        $this->lead->save();
        $this->apply_rules_and_priorities();

    }

    /**
     * Apply the Rules and Priorities to each Email
     *
     *
     */
    private function apply_rules_and_priorities() {
        foreach (Priority::all() as $priority) {
            switch ($priority->field) {
                case 1: // Subject
                {
                    if (strpos(strtolower($this->lead->subject), strtolower($priority->condition)) !== FALSE) {
                        echo $priority->id . " - " . $priority->description . " - " . $priority->condition . "\r\n";
                        $this->echod('green', 'P Subject: ' . $this->lead->subject . ' - ' . $priority->condition, __LINE__);

                        $this->send_lead_to_destination($this->lead, $priority);
                        break 2;
                    }
                    break;
                }
                case 2: // From Email Address
                {
                    if (strtolower($this->lead->email_from) == strtolower($priority->condition)) {
                        echo $priority->id . " - " . $priority->description . " - " . $priority->condition . "\r\n";
                        $this->echod('green', 'P Email: ' . $this->lead->subject . ' - ' . $priority->condition, __LINE__);

                        $this->send_lead_to_destination($this->lead, $priority);
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
     * @param $priority
     */
    private function send_lead_to_destination($priority) {
        if (trim($priority->send_to_email) != '') {
            $this->newUser = User::where('email', $priority->send_to_email)->get(['id', 'email']);
            if ($this->newUser->count()) {
                $this->sendIndividualLead($this->newUser->first());
                $this->echod('yellow', 'Send Lead to: ' . $priority->send_to_email, __LINE__);
            } else {
                // When do we come here?
                $this->sendIndividualLead(NULL, $priority->send_to_email);
                $this->echod('yellow', 'Send Lead to: ' . $priority->send_to_email, __LINE__);
            }
        }

        $this->lead->priority = $priority->priority;
        $this->lead->to_group = $priority->user_group;
        $this->lead->save();
    }

    /**
     * Save a attachment if present
     * Can an email have more than one attachment?
     *  Answer: NO, there is only provision for One Attachment in the Table and Only One is looked for.
     *  Any other attachments will get lost.
     *
     * The Name is generated from IMAP information and then
     *
     * @param    $oMessage
     * @return void
     * @property $attachment_filename
     */
    private
    function save_attachment($oMessage) {

        if ($oMessage->getAttachments()->count()) {
            $attachment = $oMessage->getAttachments()->first();
            $masked_attachment = $attachment->mask();
            $token = implode('-', [$masked_attachment->id, $masked_attachment->getMessage()->getUid(), $masked_attachment->name]);
            $token = 'attc' . str_replace(' ', '_', $token);
            $this->attachment_filename = str_replace("/", "", str_replace("'", "", $token));
            Storage::disk('public')->put($this->attachment_filename, $masked_attachment->getContent());
            $this->echod('white', 'Attachment Saved: ' . $this->attachment_filename, __LINE__);
        }
    }

    public function manage($request) {
        if ( ! count($request->input())) {
            $leadMails = LeadMails::orderBy('id', 'asc')
                                  ->limit(1)
                                  ->get()
                                  ->first();
            if ($leadMails) {
                $dateFrom = Carbon::now()->subDays(30)->startOfDay();
            } else {
                $dateFrom = Carbon::now()->startOfDay();
            }

            $dateTo = Carbon::now()->endOfDay();
        } else {
            $dateFrom = Carbon::parse($request->input('from-date'))->startOfDay();
            $dateTo = Carbon::parse($request->input('to-date'))->endOfDay();
        }
        $users = User::all();
        $leadMails = LeadMails::where('updated_at', '>=', $dateFrom)
                              ->where('updated_at', '<=', $dateTo)
                              ->orderBy('id', 'desc')->get();

        return view('pages.emailsmanage', compact('leadMails', 'dateFrom', 'dateTo', 'users'));
    }

    public function datatables(LeadsDataTable $dataTable) {

        //dataTable($query)
    }

    /**
     * @param         $request
     * @param         $leadId
     * @param         $userId
     * @return false|string
     */
    public function transferLead($request, $leadId, $userId) {

        $user = User::where('id', $userId)->first();
        if ($user) {
            $lead = $this->sendIndividualLead($user, $user->email);

            return json_encode(array('success' => 'Lead #' . $leadId . ' successfully transferred to agent: ' . $user->email));
        } else {
            return json_encode(array('error' => 'User ID: ' . $userId . ' not found'));
        }

    }

    /**
     * So this processes the sending of leads based upon the the user_group and the priority
     * @param $request
     * @return array|string[]
     * @todo - 5 - Check-re-enabled the non new group checking
     *
     */
    public function sendLeads($request) {
        $user = Auth::user();

        $currentTime = 1 * (explode(':', explode(' ', Carbon::now()->setTimeZone('America/New_York'))[1])[0] . explode(':', explode(' ', Carbon::now()->setTimeZone('America/New_York'))[1])[1]);
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
                    //@todo - 2 - need to check the changes
                    $leadMails = LeadMails::where('rejected', 0)
                                          ->where('agent_id', '=', 0)
                                          ->where('to_group', '<=', $user->user_group)
                        //->whereIn('to_group', [null,0,$user->user_group])
                        //->whereNull('to_veteran')
                                          ->orderBy('priority')
                                          ->orderBy('updated_at')
                                          ->limit(1)
                                          ->get(['id', 'email_from', 'agent_id', 'subject', 'body', 'attachment', 'received_date', 'priority', 'rejected', 'to_veteran']);
                }

                foreach ($leadMails as $lead) {
                    if (defined('ENABLE_MAILER') && ENABLE_MAILER) {
                        Mail::to($user->email)->send(new LeadSent($lead));
                        $lead->agent_id = $user->id;
                        $lead->assigned_date = Carbon::now();
                    }
                    $lead->save();
                }

            } else {
//                return array('type' => 'ERROR', 'message' => 'You are not in the Allowed Period!');
                return array('type' => 'ERROR', 'message' => 'You are operating outside of your allowed allocated time!');
            }
        } else {
            return array('type' => 'ERROR', 'message' => 'You have reached your 24h leads limit!');
        }

        return array('type' => 'SUCCESS', 'message' => count($leadMails) . ' Lead ' . (count($leadMails) > 1 ? 'have' : 'has') . ' been sent to your e-mail!', 'leads' => count($leadMails));

    }

    /**
     *
     * @param        $user
     * @param string $forceEmail
     * @return mixed
     *
     * @todo What is $forceEmail set to? It is being set in places.
     */
    public function sendIndividualLead($user, string $forceEmail = '') {
        $mailable = '';

        // If the user has been removed
        // How can we get here if the user does not exist???
        if ( ! $user) { // Sending an e-mail to a non-user
            $this->lead->agent_id = -1;
            if (defined('ENABLE_MAILER') && ENABLE_MAILER) {
                $mailable = Mail::to($forceEmail)->send(new LeadSent($this->lead));
            }
            //@todo - 3 -Fix Logic - What is $forceEmail is not set?
        } else {
            if ($this->lead->agent_id > 0) {
                $this->lead->old_agent_id = $this->lead->agent_id;
                $this->lead->old_assigned_date = $this->lead->assigned_date;
            }
            $this->lead->agent_id = $user->id;
            $this->lead->assigned_date = Carbon::now();

            // We need to send the attachment as well
            if (defined('ENABLE_MAILER') && ENABLE_MAILER) {
                $mailable = Mail::to($user->email)->send(new LeadSent($this->lead));
            }
        }

        $this->lead->save();

        return $mailable;
    }

    public function downloadAttachment($leadId) {
        $lead = LeadMails::find($leadId);

        return redirect(Storage::url($lead->attachment));
    }

    /**
     * Called from the Admin Email View via AJAX Call to populate a Modal
     *
     * @param $leadId
     * @return false|string
     */
    public function getBody($leadId) {
        $lead = LeadMails::find($leadId);
        $content = $this->parseMailBody($lead->body);

        return json_encode(array('body' => base64_encode($content)));
    }

    /**
     * @param $leadId
     * @return false|string
     */
    public function getReassigned($leadId) {
        $lead = LeadMails::find($leadId);
        $content = $this->parseMailBody($lead->reassigned_message);

        return json_encode(array('body' => base64_encode(explode('On', $content)[0])));
    }

    public function getRejected($leadId) {
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
    private function parseMailBody($content) {
        //@todo - 2 - not tested with text email
        if ($this->isHTML($content)) {
            $content = preg_replace("/\r\n/", "", $content);
        } else {
            $content = nl2br($content);
        }

        return $content;
    }


    public function report(Request $request, $dateFrom, $dateTo) {

        $leads = \DB::select(\DB::raw(
            "
            SELECT 	LM.agent_id,
                    U.name AS agent_name,
                    COUNT(*) AS leads_count,
                    SUM(CASE WHEN IFNULL(LM.old_agent_id, 0) > 0 
                        THEN 1
                          ELSE 0
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
                    SUM(IF(IFNULL(LM.old_agent_id, 0) > 0, 1, 0)
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
        if (defined('ENABLE_MAILER') && ENABLE_MAILER) {
            Mail::to(Auth::user()->email)
                ->bcc('timbrownlaw@gmail.com')
                ->bcc('visiontocode2022@gmail.com')
                ->send(new ReportMail($leads, $dateFrom, $dateTo));
        }

        return json_encode(array('type' => 'SUCCESS', 'message' => 'E-mail Report was sent to ' . Auth::user()->email));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $leadId
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy($leadId): RedirectResponse {
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
    private function echod($color, $text, $line) {
        if (config('app.debug')) {
            Colors::nobr()->green("Line: " . $line . ' ');
            Colors::{$color}(' ' . $text);
        } else {
            echo $text;
        }
    }

    /**
     * @param $string
     * @return bool
     */
    private function isHTML($string) {
        return ($string != strip_tags($string));
    }

    /**
     * @return void
     */
    private function detect_console_command() {
        // Added for debug
        if (config('app.debug')) {
            if (app()->runningInConsole()) {
                $this->echod('red', "Running MalBoxController in Console", __LINE__);
            }
        }
    }

    /**
     * @return \Client|\Webklex\IMAP\Client
     * @throws ConnectionFailedException
     */
    private function connect_to_imap_server() {
        $oClient = Client::account('default');
        $oClient->connect();

        return $oClient;
    }

    /**
     * @param $oClient
     * @return array
     */
    private function determine_mailboxes($oClient) {
        if (strpos(config('app.url'), 'cruisertravels') !== FALSE) {
            // This is for the leads.cruisertravels.com
            $aFolder = [$oClient->getFolder('INBOX'), $oClient->getFolder('Junk Email')];
        } else if (strpos(config('app.url'), 'joesdigitalservices') !== FALSE) {
            // This is for cruiser.joesdigitalservices.com
            $aFolder = [$oClient->getFolder('INBOX'), $oClient->getFolder('INBOX.spam')];
        } else {
            // Fall back to the Base INBOX
            $aFolder = [$oClient->getFolder('INBOX')];
        }

        return $aFolder;
    }

}
