<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webklex\IMAP\Client;
use Carbon\Carbon;
use App\LeadMails;
use App\Priority;
use App\Mail\LeadSent;
use App\Mail\ReportMail;
use App\Mail\ErrorMail;
use App\User;
use Illuminate\Support\Facades\Log;
use App\DataTables\LeadsDataTable;

class MailBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    $oClient = \Webklex\IMAP\Facades\Client::account('default');

	//Connect to the IMAP Server
	$oClient->connect();

	//Get all Mailboxes
    /** @var \Webklex\IMAP\Support\FolderCollection $aFolder */
    //$aFolder = [$oClient->getFolder('INBOX'), $oClient->getFolder('[Gmail]/Spam')];
    //$aFolder = $oClient->getFolders();
    $aFolder[] = $oClient->getFolder('INBOX');
    //$oFolder = $oClient->getFolder('Gmail/SPAM');
    dump($aFolder);

    //$aFolder[] = $oClient->getFolder('[Gmail]/Spam');
    //$oFolder = $oClient->getFolder('Gmail/SPAM');
    //dump($oFolder);

	//Loop through every Mailbox
	/** @var \Webklex\IMAP\Folder $oFolder */
	foreach($aFolder as $oFolder){

        //dump($oFolder);

		//Get all Messages of the current Mailbox $oFolder
		/** @var \Webklex\IMAP\Support\MessageCollection $aMessage */
        //$aMessage = $oFolder->messages()->all()->get();
        $aMessage = $oFolder->query()->unseen()->get();
        //$aMessage = $oFolder->query()->since(Carbon::now()->subDays(5))->get();
		
        /** @var \Webklex\IMAP\Message $oMessage */
        $i = 0;
		foreach($aMessage as $oMessage){
            dump($oMessage);
			echo $oMessage->getSubject().'<br />';
			echo 'Attachments: '.$oMessage->getAttachments()->count().'<br />';

            $filename = null;
            if($oMessage->getAttachments()->count()){
                $attachment = $oMessage->getAttachments()->first();
                $masked_attachment = $attachment->mask();

                $token = implode('-', [$masked_attachment->id, $masked_attachment->getMessage()->getUid(), $masked_attachment->name]);
                $token = 'attc' . str_replace(' ', '_', $token);

                $path = public_path('files');
                $filename = $token;
                /*echo '<a href="/files/' . $filename . '">Download</a>';*/

                $path = substr($path, -1) == DIRECTORY_SEPARATOR ? $path : $path.DIRECTORY_SEPARATOR;
                $filename = str_replace("/", "", str_replace("'", "", $filename));

                /*\Illuminate\Support\Facades\File::put($filename, $masked_attachment->getContent());*/
                \Storage::disk('public')->put($filename, $masked_attachment->getContent());

            }


            $lead = LeadMails::where('email_imap_id', $oMessage->message_id);
            $body = $oMessage->getHTMLBody(true);
            $body = $body ? $body : $oMessage->getTextBody();

            $emailFirstWord = trim(strtolower(explode(' ', strip_tags(preg_replace('#(<title.*?>).*?(</title>)#', '$1$2', $body)))[0]));
            $emailContent   = strip_tags(str_replace('<br/>', ' ', str_replace('<br>', ' ', $body)));

            Log::debug('First Word: ' . $emailFirstWord);

            if(strpos($emailFirstWord,'spam') !== false){
                if(count(explode('-||', $oMessage->getSubject()))){
                    $originalMessageId = explode('-||', $oMessage->getSubject())[1];

                    $lead = LeadMails::find($originalMessageId);
                    $lead->rejected = 1;
                    $lead->rejected_message = str_replace('spam', '', $emailContent);
                    $lead->save();

                } else {
                    continue;
                }
            } elseif(filter_var(explode('!', $emailFirstWord)[0], FILTER_VALIDATE_EMAIL)){
                $originalMessageId = explode('-||', $oMessage->getSubject())[1];
                $newUser = User::where('email', explode('!', $emailFirstWord)[0])->get(['id', 'email']);
                if($newUser->count()){

                    $lead = LeadMails::find($originalMessageId);
                    $lead->reassigned_message = str_replace(explode(' ', strip_tags($body))[0], '', $emailContent);
                    $lead->save();

                    $this->sendIndividualLead($originalMessageId, $newUser->first());
                } else {
                    $lead = LeadMails::find($originalMessageId);
                    //\Mail::to('dyegofern@gmail.com')->send(new ErrorMail($lead, 'Agent not found with e-mail: ' . explode('!', $emailFirstWord)[0] . '. Please check the spelling.'));
                    \Mail::to($lead->agent()->first()->email)->cc('dyegofern@gmail.com')->send(new ErrorMail($lead, 'Agent not found with e-mail: ' . explode('!', $emailFirstWord)[0] . '. Please check the spelling.'));
                }
                
            } else { //Count as a new Lead
                if(!$lead->get()->count()){
                    $lead = new LeadMails();
                    $lead->email_imap_id    = $oMessage->message_id;
                    $lead->email_from       = $oMessage->getFrom()[0]->mail;
                    $lead->agent_id         = 0;
                    $lead->subject          = $oMessage->getSubject();
                    $lead->body             = $body;
                    $lead->attachment       = $filename;
                    $lead->received_date    = $oMessage->date;
                    $lead->priority         = 100;
                    $lead->save();

                    foreach(Priority::all() as $priority){

                        switch($priority->field){
                            case 1: {
                                dump(array('Subject Line',$priority, array('subject' => $lead->subject, 'cond' => $priority->condition,'conditional' => strpos(strtolower($lead->subject), strtolower($priority->condition)))));
                                if(strpos(strtolower($lead->subject), strtolower($priority->condition)) !== false){
                                    dump(array(strtolower($lead->subject), strtolower($priority->condition)));
                                    $lead->priority = $priority->priority;
                                    
                                    if(trim($priority->send_to_email) != ''){
                                        dump(array('to_email', $priority->send_to_email));
                                        $newUser = User::where('email', $priority->send_to_email)->get(['id', 'email']);
                                        
                                        if($newUser->count()){
                                            dump(array('to_email_user', $newUser));
                                            $this->sendIndividualLead($lead->id, $newUser->first());
                                        } else {
                                            dump(array('to_email_user', 'not_user'));
                                            $this->sendIndividualLead($lead->id, null, $priority->send_to_email);
                                        }
                                    }

                                    $lead->priority = $priority->priority;
                                    $lead->save();
                                }
                                break;
                            }

                            case 2: {
                                //dump(array($priority->field,$priority));
                                if(strtolower($lead->email_from) == strtolower($priority->condition)){

                                    if(trim($priority->send_to_email) != ''){
                                        $newUser = User::where('email', $priority->send_to_email)->get(['id', 'email']);

                                        if($newUser->count()){
                                            $this->sendIndividualLead($lead->id, $newUser->first());
                                        } else {
                                            $this->sendIndividualLead($lead->id, null, $priority->send_to_email);
                                        }
                                    }

                                    $lead->priority = $priority->priority;
                                    $lead->save();                                    
                                }
                                break;
                            }
                            default:{
                                dump(array('default',$priority));
                                break;
                            }
                        }
                    }

                    //DONOTREPLY@royalcaribbean.com
                }
            }


            
			//\Illuminate\Support\Facades\Storage::put($path.'/'.$filename, $masked_attachment->getContent());

			//dump($masked_attachment);
			//echo $oMessage->getHTMLBody(true);

			//Move the current Message to 'INBOX.read'
			/*if($oMessage->moveToFolder('INBOX.read') == true){
				echo 'Message has ben moved';
			}else{
				echo 'Message could not be moved';
			}*/
		}
	}
    }

    public function manage(Request $request){
    //public function manage(LeadsDataTable $dataTable){

        if(!count($request->input())){

            $leadMails = LeadMails::orderBy('id', 'asc')
                ->limit(1)
                ->get()
                ->first();

            if($leadMails){
                //$dateFrom   = \Carbon\Carbon::parse($leadMails->created_at)->startOfDay();

                $dateFrom   = \Carbon\Carbon::now()->subDays(30)->startOfDay();
            } else {
                $dateFrom   = \Carbon\Carbon::now()->startOfDay();
            }
            
            $dateTo     = \Carbon\Carbon::now()->endOfDay();
        } else {
            $dateFrom   = \Carbon\Carbon::parse($request->input('from-date'))->startOfDay();
            $dateTo     = \Carbon\Carbon::parse($request->input('to-date'))->endOfDay();
        }


        
       // dd($leadMails);

        $leadMails = LeadMails::where('updated_at', '>=', $dateFrom)
                        ->where('updated_at', '<=', $dateTo)
                        ->orderBy('id', 'desc')->get();

        return view('pages.emailsmanage', compact('leadMails', 'dateFrom', 'dateTo'));

//        return $dataTable->render('pages.emailsmanagedatatable');

    }

    public function datatables(LeadsDataTable $dataTable){

        //dataTable($query)
    }

    public function sendLeads(Request $request){

        $user = \Auth::user();

        $currentTime    = 1 * (explode(':', explode(' ', \Carbon\Carbon::now()->setTimeZone('America/New_York'))[1])[0] . explode(':', explode(' ', \Carbon\Carbon::now()->setTimeZone('America/New_York'))[1])[1]);
        $time_set_init  = 1 * (explode(':',$user->time_set_init)[0] . explode(':',$user->time_set_init)[1]);
        $time_set_final = 1 * (explode(':',$user->time_set_final)[0] . explode(':',$user->time_set_final)[1]);

        if(LeadMails::where('agent_id', $user->id)->where('updated_at', '>', Carbon::now()->subDay())->count() < $user->leads_allowed){
            if($currentTime >= $time_set_init && $currentTime <= $time_set_final){
                if($user->user_group >= 2){
                    $leadMails = LeadMails::where('rejected', 0)
                                    ->where('agent_id', 0)
                                    ->orderBy('to_group', 'desc')
                                    ->orderBy('priority')
                                    ->orderBy('updated_at')
                                    ->limit(1)
                                    ->get(['id', 'email_from', 'agent_id', 'subject', 'body', 'attachment', 'received_date', 'priority', 'rejected', 'to_veteran']);
                } else {
                    $leadMails = LeadMails::where('rejected', 0)
                                    ->where('agent_id', 0)
                                    ->where('to_group', 0)
                                    ->whereNull('to_veteran')
                                    ->orderBy('priority')
                                    ->orderBy('updated_at')
                                    ->limit(1)
                                    ->get(['id', 'email_from', 'agent_id', 'subject', 'body', 'attachment', 'received_date', 'priority', 'rejected', 'to_veteran']);
                }

                foreach($leadMails as $lead){
                    \Mail::to($user->email)->send(new LeadSent($lead));
                    $lead->agent_id = $user->id;
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

    public function sendIndividualLead($leadId, $user, $forceEmail = ''){
        $lead = LeadMails::find($leadId);

        if(!$user){ // Sending an e-mail to a non-user
            $lead->agent_id             = -1;

            $mailable = \Mail::to($forceEmail)->send(new LeadSent($lead));
        } else {
            if($lead->agent_id > 0){
                $lead->old_agent_id         = $lead->agent_id;
                $lead->old_assigned_date    = $lead->assigned_date;
            }
            $lead->agent_id             = $user->id;
            $lead->assigned_date        = \Carbon\Carbon::now();

            $mailable = \Mail::to($user->email)->send(new LeadSent($lead));
        }

        $lead->save();

        return  $mailable;
    }

    public function downloadAttachment($leadId){
        $lead = LeadMails::find($leadId);
        return redirect(\Storage::url($lead->attachment));
    }

    public function getBody($leadId){
        $lead = LeadMails::find($leadId);
        return  json_encode(array('body' => base64_encode($lead->body)));
    }

    public function getReassigned($leadId){
        $lead = LeadMails::find($leadId);
        return  json_encode(array('body' => base64_encode(explode('On', $lead->reassigned_message)[0])));
    }

    public function getRejected($leadId){
        $lead = LeadMails::find($leadId);
        return  json_encode(array('body' => base64_encode(explode('On', $lead->rejected_message)[0])));
    }

    public function report(Request $request, $dateFrom, $dateTo){

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

        return $leads;
    }

    public function reportEmail(Request $request, $dateFrom, $dateTo){

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

        \Mail::to(\Auth::user()->email)
                ->bcc('dyegofern@gmail.com')
                ->cc('visiontocode2022@gmail.com')
                ->send(new ReportMail($leads, $dateFrom, $dateTo));

        return json_encode(array('type' => 'SUCCESS', 'message' => 'E-mail Report was sent to ' . \Auth::user()->email ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($leadId)
    {
        $lead = LeadMails::find($leadId);

        $lead->delete();

        return redirect()->route('emails.manage')->withStatus(__('Lead successfully deleted.'));
    }
}
