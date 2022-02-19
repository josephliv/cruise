<?php /** @noinspection ALL */

namespace App\Http\Controllers;

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
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Mail;
use Storage;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Facades\Client;

defined('EMAIL_TYPE_DEFAULT') || define('EMAIL_TYPE_DEFAULT', 0);
defined('EMAIL_TYPE_TRANSFER') || define('EMAIL_TYPE_TRANSFER', 1);
defined('EMAIL_TYPE_REASSIGNMENT') || define('EMAIL_TYPE_REASSIGNMENT', 2);

class MailBoxController extends Controller {

    private $attachment_filename;
    private $body;
    private $newUser;
    private $emailLeadId;

    // Default priority and Group
    private $default_priority = 50;
    private $default_group = 1;

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

        $this->echod('red', "Processing Mailboxes - Version 2.0", __LINE__);

        $oClient = $this->connect_to_imap_server();
        // Create an array of Mailbox Folder(s) we want to check
        $aFolder = $this->determine_mailboxes($oClient);

        // Process Each Mail Box
        foreach ($aFolder as $oFolder) {
            $this->lead = new LeadMails();

            $this->echod('yellow', 'Processing: ' . $oFolder->name, __LINE__);
            $aMessage = $oFolder->query(NULL)->unseen()->limit(5)->since(Carbon::now()->subDays(14))->get();

            // Process Each Message
            foreach ($aMessage as $oMessage) {

                $this->echod('yellow', 'Email: ' . $oMessage->getSubject(), __LINE__);

                $subject_array = explode('-||', $oMessage->getSubject());
                $emailSubject = strtolower(trim($subject_array[0]));
                $this->emailLeadId = $subject_array[1] ?? FALSE; // Either get a ID or its a new Lead

                // Does the lead exist by the provided Lead ID or it's IMAP ID
                if ($this->emailLeadId) {
                    $this->lead = LeadMails::find($this->emailLeadId);
                } else {
                    $this->lead = LeadMails::where('email_imap_id', $oMessage->message_id)->first();
                }

                if ($this->lead && $this->lead->attachment) {
                    $this->attachment_filename = $this->lead->attachment;
                    $this->emailLeadId = $this->lead->id;
                    $this->echod('bgWhite', 'Existing Attachment ' . $this->emailLeadId . ' ' . $this->attachment_filename, __LINE__);
                } else {
                    $this->save_attachment_if_present($oMessage);
                }

                $this->body = $oMessage->getHTMLBody() ?: $oMessage->getTextBody();

                $emailSubjectFirstWord = trim(strtolower(explode(' ', $emailSubject)[0]));
                // Removes the Text in any Title Tags, then removes all the tags
                $emailBodyFirstWord = trim(strtolower(explode(' ', strip_tags(preg_replace('#(<title.*?>).*?(</title>)#', '$1$2', $this->body)))[0]));
                // Replace all occurrences of <br/> or <br> with a space then strip all tags
                $emailBody = strip_tags(str_replace('<br/>', ' ', str_replace('<br>', ' ', $this->body)));

                $isExistingLead = $this->lead && $this->lead->count();
                // Determine the Special Cases
                $isMessageSpam = strpos($emailBodyFirstWord, 'spam') !== FALSE;
                $isMessageTest = (strpos($emailBodyFirstWord, 'test') !== FALSE) || (strpos($emailSubjectFirstWord, 'test') !== FALSE);
                $isMessageReassignment = filter_var(explode('!', $emailBodyFirstWord)[0], FILTER_VALIDATE_EMAIL);

                // Process the Lead
                if ($isExistingLead) {
                    $this->echod('info', "This is an Existing Lead : $this->emailLeadId", __LINE__);
                    if ($isMessageSpam) {
                        $this->lead->rejected = 1;
                        $this->lead->rejected_message = $this->extract_rejected_message_from_body($emailBody, $this->lead->body);
                        $this->echod('yellow', 'OLD Lead - Detected as SPAM - Rejected', __LINE__);
                        $this->lead->save();
                    } elseif ($isMessageTest) {
                        $this->lead->rejected = 1;
                        $this->lead->rejected_message = "Detected as a TEST EMAIL\r\n";
                        $this->echod('yellow', 'OLD Lead - Detected as Test Email - Rejected', __LINE__);
                        $this->lead->save(); // Save it but reject it immediately
                    } elseif ($isMessageReassignment) {
                        $this->echod('red', 'Agent Reassigning the Lead', __LINE__);
                        $this->newUser = User::where('email', explode('!', $emailBodyFirstWord)[0])->get(['id', 'email']);
                        $isValidAgentEmail = $this->newUser->count();
                        $agent_email = $isValidAgentEmail ? $this->newUser->values()[0]->email : $emailBodyFirstWord . ' But it Does not exist';
                        $this->echod('yellow', 'Trying to Send to ' . $agent_email, __LINE__);
                        if ($isValidAgentEmail) {
                            $this->echod('green', 'Agent Email is Valid', __LINE__);
                            $this->lead->reassigned_message = $this->extract_reassignment_message_from_body($emailBody, $this->lead->body)['reassignment_message'];
                            $this->lead->save();
                            $this->sendIndividualLead(EMAIL_TYPE_REASSIGNMENT, $this->emailLeadId, $this->newUser->first());
                        } else {
                            if (defined('ENABLE_MAILER') && ENABLE_MAILER) {
                                $this->echod('red', 'Agent Email is INVALID', __LINE__);
                                Mail::to($this->lead->agent()->first()->email)->bcc('timbrownlaw@gmail.com')->send(new ErrorMail($this->lead, 'Agent not found with e-mail: ' . explode('!', $emailBodyFirstWord)[0] . '. Please check the spelling.'));
                            }
                        }
                    }
                } else {
                    if ($isMessageTest) {
                        $this->save_new_lead($oMessage, TRUE); // Save it but reject it immediately
                    } else {
                        $this->save_new_lead($oMessage);
                        $this->echod('red', 'New Lead Saved', __LINE__);
                    }
                }
            }
        }
    }
    /**
     * We want to
     * 1. Remove the existing email body from the DB
     * 2. Remove the Email Address
     * 3. Detect the Reassignment Message ( assume it is on it's own NEW LINE?)
     *
     * RULES: Reassignment Email and Message must be on a Single Line
     *
     * agent@email.com!Whatever they like
     * <enter>
     *
     * @param $sBody
     * @param $dbBody
     * @return array|string|string[]
     */
    private function extract_reassignment_message_from_body($sBody, $dbBody) {
        $email = [];
        $email['email'] = '';
        $email['reassignment_message'] = '';
        // 1. Remove the Old Body Message
        $message_to_check = $this->extract_new_body_from_old($sBody, $dbBody);
        // 2. Find and strip the Email address
        preg_match('/([\s\S]*?!)([\s\S]*)/', $message_to_check, $matches);
        $email['email'] = trim($matches[1] ?? '', '!');
        $remaining_body_to_process = $matches[2] ?? '';
        // 3. Get to End of Line
        if ($remaining_body_to_process !== '') {
            preg_match('/^([\s\S]*?)(?=[\r\n])/', $remaining_body_to_process, $new_matches);
            $email['reassignment_message'] = $new_matches[1] ?? '';
        }

        return $email;
    }

    /**
     * Extract any added text to the beginning of an existing string
     *
     * @param $sBody
     * @param $dbBody
     * @return string
     */
    private function extract_rejected_message_from_body($sBody, $dbBody) {
        $aMessage = $this->extract_new_body_from_old($sBody, $dbBody);
        $message = str_ireplace('spam', '', $aMessage);

        return trim(preg_replace('/[^a-z0-9 \'.]/i', '', $message));
    }

    private function extract_new_body_from_old($sBody, $dbBody) {
        $dbBody = trim($dbBody);
        $sBody = trim($sBody);
        $dbBodyLength = strlen($dbBody);
        $sBodyLength = strlen($sBody);

        return substr($sBody, 0, $sBodyLength - $dbBodyLength);
    }

    /**
     * Saves a New Lead.
     * Except for the case where a lead has to be Rejected so it does not end up in the Unassigned Leads
     * @param      $oMessage
     * @param bool $test Is this a lead that we need to auto reject.
     */
    private function save_new_lead($oMessage, bool $test = FALSE) {
        $this->lead = new LeadMails();
        $this->lead->agent_id = 0;
        $this->lead->email_imap_id = $oMessage->message_id;
        $this->lead->email_from = $oMessage->getFrom()[0]->mail; // @TB Correct

        $this->lead->subject = $oMessage->getSubject();
        $this->lead->body = $this->body;
        $this->lead->attachment = $this->attachment_filename;
        $this->lead->received_date = $oMessage->date;
        $this->lead->priority = 100;

        if ($test != FALSE) {
            $this->echod('yellow', 'New Lead - Detected as Test Email - Rejected', __LINE__);
            $this->lead->agent_id = 1;
            $this->lead->old_agent_id = 0;
            $this->lead->rejected = 1;
            $this->lead->rejected_message = "Detected as a TEST EMAIL\r\n";
        }

        // we now have a new $emailLeadId
        $this->lead->save();
        $this->emailLeadId = $this->lead->id;

        if ($test === FALSE) {
            $this->apply_rules_and_priorities();
        }

    }

    /**
     * Apply the Rules and Priorities to each Email
     *
     *
     */
    private function apply_rules_and_priorities() {
        $this->echod('yellow', 'Checking Rules and Priorities: ', __LINE__);

        foreach (Priority::all() as $priority) {
            $this->echod('green', 'Rules Checked: ' . $priority->condition, __LINE__);

            switch ($priority->field) {

                case 1: // Subject
                {
                    if (strpos(strtolower($this->lead->subject), strtolower($priority->condition)) !== FALSE) {
                        echo $priority->id . " - " . $priority->description . " - " . $priority->condition . "\r\n";
                        $this->echod('green', 'P Subject: ' . $this->lead->subject . ' - ' . $priority->condition, __LINE__);
                        $this->send_lead_to_destination($priority);
                        break 2;
                    }
                    break;
                }

                case 2: // From Email Address
                {
                    if (strtolower($this->lead->email_from) == strtolower($priority->condition)) {
                        echo $priority->id . " - " . $priority->description . " - " . $priority->condition . "\r\n";
                        $this->echod('green', 'P Email: ' . $this->lead->subject . ' - ' . $priority->condition, __LINE__);
                        $this->send_lead_to_destination($priority);
                        break 2;
                    }
                    break;
                }
                default:
                {
                    break;
                }
            }

            // So if we get here then we did not have a match
            $this->lead->priority = $this->default_priority;
            $this->lead->to_group = $this->default_group;
            $this->lead->save();
        }
    }


    /**
     * Send Email as set in rules
     *
     * Question: Why does the Email Address have to belong to an Agent?
     *
     * @param  $priority
     *
     * @uses  $this->message_id
     */
    private function send_lead_to_destination($priority) {

        if (trim($priority->send_to_email) != '') {
            $this->newUser = User::where('email', $priority->send_to_email)->get(['id', 'email']);
            if ($this->newUser->count()) {
                $this->sendIndividualLead(EMAIL_TYPE_DEFAULT, $this->emailLeadId, $this->newUser->first());
                $this->echod('yellow', 'Send Lead to: ' . $priority->send_to_email, __LINE__);
            } else {
                // When do we come here?
                $this->sendIndividualLead(EMAIL_TYPE_DEFAULT, $this->emailLeadId, NULL, $priority->send_to_email);
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
     * The Name is generated from IMAP information.
     *
     * @param    $oMessage
     * @return void
     * @property $attachment_filename
     */
    private function save_attachment_if_present($oMessage) {

        if ($oMessage->getAttachments()->count()) {
            $attachment = $oMessage->getAttachments()->first();
            $masked_attachment = $attachment->mask();
            $token = implode('-', [$masked_attachment->id, $masked_attachment->getMessage()->getUid(), $masked_attachment->name]);
            $token = 'attc' . str_replace(' ', '_', $token);
            $this->attachment_filename = str_replace("/", "", str_replace("'", "", $token));
            Storage::disk('public')->put($this->attachment_filename, $masked_attachment->getContent());
            $this->echod('red', 'Attachment Saved: ' . $this->attachment_filename, __LINE__);
        }
    }

    /**
     * Prepare the data to be displayed in the Lead Manage View
     *
     * @param Request $request
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manage(Request $request) {
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
        $leadMails = LeadMails::where('lead_mails.updated_at', '>=', $dateFrom)
                              ->where('lead_mails.updated_at', '<=', $dateTo)
                              ->join('groups', 'groups.id', '=', 'lead_mails.to_group', 'left outer')
                              ->orderBy('lead_mails.id', 'desc')->get(['lead_mails.*', 'groups.name as group_name']);

        return view('pages.emailsmanage', compact('leadMails', 'dateFrom', 'dateTo', 'users'));
    }

    /**
     * External Request
     *
     * @param Request $request
     * @param         $leadId
     * @param         $userId
     * @return false|string
     */
    public function transferLead(Request $request, $leadId, $userId) {

        $user = User::where('id', $userId)->first();
        if ($user) {
            $this->sendIndividualLead(EMAIL_TYPE_TRANSFER, $leadId, $user);

            return json_encode(array('success' => 'Lead #' . $leadId . ' successfully transferred to agent: ' . $user->email));
        } else {
            return json_encode(array('error' => 'User ID: ' . $userId . ' not found'));
        }
    }

    /**
     * When an Agent requests a leads,
     * This locates the highest lead available.
     *
     * @param $request
     * @return array|string[]
     * @todo - 5 - Check-re-enabled the non new group checking
     *
     */
    public function sendLeads() {
        $user = Auth::user();

        $currentTime = 1 * (explode(':', explode(' ', Carbon::now()->setTimeZone('America/New_York'))[1])[0] . explode(':', explode(' ', Carbon::now()->setTimeZone('America/New_York'))[1])[1]);

        $time_set_init = 1 * (explode(':', $user->time_set_init)[0] . explode(':', $user->time_set_init)[1]);
        $time_set_final = 1 * (explode(':', $user->time_set_final)[0] . explode(':', $user->time_set_final)[1]);

        if (LeadMails::where('agent_id', $user->id)->where('updated_at', '>', Carbon::now()->subDay())->count() < $user->leads_allowed) {

            if ($currentTime >= $time_set_init && $currentTime <= $time_set_final) {
                $leadMails = $this->get_lead_queue($user->user_group);
                foreach ($leadMails as $lead) {
                    if (defined('ENABLE_MAILER') && ENABLE_MAILER) {
                        Mail::to($user->email)->send(new LeadSent($lead));
                        $lead->agent_id = $user->id;
                        $lead->assigned_date = Carbon::now();
                    }
                    $lead->save();
                }
            } else {
                return array('type' => 'ERROR', 'message' => 'You are operating outside of your allowed allocated time! ' . $currentTime . ' ' . $time_set_init . ' ' . $time_set_final);
            }
        } else {
            return array('type' => 'ERROR', 'message' => 'You have reached your 24h leads limit!');
        }

        return array('type' => 'SUCCESS', 'message' => count($leadMails) . ' Lead ' . (count($leadMails) > 1 ? 'have' : 'has') . ' been sent to your e-mail!', 'leads' => count($leadMails));
    }

    /**
     * Leads are fetched by lowest priority and oldest updated_at.
     * New Group Agents only get New Group Leads.
     *
     * For Non New Group Members, they are not sorted by group
     *
     * @param $group_id
     * @return LeadMails[]|Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     *
     * @todo -2- Review the get columns. Which ones do we actually need?
     */
    public function get_lead_queue($group_id, $limit = 1) {
        $operator = ($group_id == 1) ? '=' : '<=';
        $leadMails = LeadMails::where('rejected', 0)
                              ->where('agent_id', $operator, 0)
                              ->where('to_group', '<=', $group_id)
                              ->orderBy('priority')                     // Lowest Priority
                              ->orderBy('updated_at')                   // Oldest Lead
                              ->limit($limit)
                              ->get(['id', 'email_from', 'agent_id', 'subject', 'body', 'attachment', 'received_date', 'priority', 'to_group', 'rejected']);

        return $leadMails;
    }

    /**
     *
     * @param        $leadId
     * @param        $user
     * @param string $forceEmail
     * @return void
     *
     * @todo What is $forceEmail set to? It is being set in places.
     *
     * There are a few cases to deal with when we are in here. Which means that the code isn't overly optimal.
     * OR we just need to tell it who sent us and for what reason
     *
     */
    public function sendIndividualLead(int $type, $leadId, $user, string $forceEmail = '') {
        $mailable = '';

        // As we Fetch the Email from here, if we need to alter the body etc, it's a lil bit hard
        // This is a general Function:
        // This is called from:
        //      transferLead - Initiated from Admin
        //      Index > $isValidAgentEmail
        //
        $this->lead = LeadMails::find($leadId);

        $isHtml = $this->isHTML($this->lead->body);

//        $newlines = $this->isHTML($this->lead->body) ? "<br><br>" : "\r\n\r\n";
//        $newlines = "\r\n\r\n";
        $newlines = "<br><br>";

        if ( ! $user) {
            $this->lead->agent_id = -1;
            $mail_to = $forceEmail;
        } else {
            if ($this->lead->agent_id > 0) {
                $this->lead->old_agent_id = $this->lead->agent_id;
                $this->lead->old_assigned_date = $this->lead->assigned_date;
            }
            $this->lead->agent_id = $user->id;
            $this->lead->assigned_date = Carbon::now();
            $mail_to = $user->email;
        }

        $this->lead->save();

        switch ($type) {
            case EMAIL_TYPE_REASSIGNMENT:
                $this->lead->reassigned_message = $this->lead->reassigned_message ?? "No Message Included";
                $this->lead->body = "Reassigned Message: " . $this->lead->reassigned_message . $newlines . $this->lead->body;
                break;
            case EMAIL_TYPE_TRANSFER:
                $this->lead->body = "Transfered by Admin" . $newlines . $this->lead->body;
                break;
            default:
                break;
        }

        if (defined('ENABLE_MAILER') && ENABLE_MAILER) {
            $mailable = Mail::to($mail_to)->send(new LeadSent($this->lead));
        }

//        return $mailable;
    }

    /**
     * @param $leadId
     * @return Application|RedirectResponse|Redirector
     */
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

    /**
     * @param Request $request
     * @param         $dateFrom
     * @param         $dateTo
     * @return array
     */
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
                ->bcc('timbrownlawswebsites@gmail.com')
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
            Colors::nobr()->green("Line: " . $line . "\t\t");
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
