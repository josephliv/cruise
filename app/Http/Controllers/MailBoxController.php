<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webklex\IMAP\Client;
use Carbon\Carbon;
use App\LeadMails;
use App\Priority;
use App\Mail\LeadSent;
use App\User;

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
    $aFolder = $oClient->getFolders();
    $aFolder[] = $oClient->getFolder('[Gmail]/Spam');
    //$oFolder = $oClient->getFolder('Gmail/SPAM');
    //dump($oFolder);

	//Loop through every Mailbox
	/** @var \Webklex\IMAP\Folder $oFolder */
	foreach($aFolder as $oFolder){

        dump($oFolder);

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

            $emailFirstWord = explode(' ', strip_tags($body))[0];

            if(strpos($emailFirstWord,'duplicate') !== false){
                if(count(explode('-||', $oMessage->getSubject()))){
                    $originalMessageId = explode('-||', $oMessage->getSubject())[1];

                    $lead = LeadMails::find($originalMessageId);
                    $lead->rejected = 1;
                    $lead->save();

                } else {
                    continue;
                }
            } elseif(filter_var(explode('!', $emailFirstWord)[0], FILTER_VALIDATE_EMAIL)){
                $originalMessageId = explode('-||', $oMessage->getSubject())[1];
                $newUser = User::where('email', explode('!', $emailFirstWord)[0])->get(['id', 'email']);
                $this->sendIndividualLead($originalMessageId, $newUser->first());
                
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
                    $lead->priority         = 0;
                    $lead->save();

                    foreach(Priority::all() as $priority){

                        switch($priority->field){
                            case 1: {
                                if(strpos($lead->subject, $priority->condition)){
                                    $lead->priority = $priority->priority;
                                    
                                    if(trim($priority->send_to_email) != ''){
                                        $newUser = User::where('email', $priority->send_to_email)->get(['id', 'email']);
                                        $this->sendIndividualLead($lead->id, $newUser->first());
                                    }

                                    $lead->priority = $priority->priority;
                                    $lead->save();
                                }
                                break;
                            }

                            case 2: {
                                if($lead->email_from == $priority->condition){

                                    if(trim($priority->send_to_email) != ''){
                                        $newUser = User::where('email', $priority->send_to_email)->get(['id', 'email']);
                                        $this->sendIndividualLead($lead->id, $newUser->first());
                                    }

                                    $lead->priority = $priority->priority;
                                    $lead->save();                                    
                                }
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

        $leadMails = LeadMails::orderBy('id', 'desc')->paginate(10);
        return view('pages.emailsmanage', compact('leadMails'));

    }

    public function sendLeads(Request $request){

        $user = \Auth::user();

        $currentTime    = 1 * (explode(':', explode(' ', \Carbon\Carbon::now()->setTimeZone('America/New_York'))[1])[0] . explode(':', explode(' ', \Carbon\Carbon::now()->setTimeZone('America/New_York'))[1])[1]);
        $time_set_init  = 1 * (explode(':',$user->time_set_init)[0] . explode(':',$user->time_set_init)[1]);
        $time_set_final = 1 * (explode(':',$user->time_set_final)[0] . explode(':',$user->time_set_final)[1]);

        if(LeadMails::where('agent_id', $user->id)->where('updated_at', '>', Carbon::now()->subDay())->count() < $user->leads_allowed){
            if($currentTime >= $time_set_init && $currentTime <= $time_set_final){
                if($user->is_veteran){
                    $leadMails = LeadMails::where('rejected', 0)
                                    ->where('agent_id', 0)
                                    ->orderBy('to_veteran', 'desc')
                                    ->orderBy('priority')
                                    ->limit($user->leads_allowed)
                                    ->get(['id', 'email_from', 'agent_id', 'subject', 'body', 'attachment', 'received_date', 'priority', 'rejected', 'to_veteran']);
                } else {
                    $leadMails = LeadMails::where('rejected', 0)
                                    ->where('agent_id', 0)
                                    ->whereNull('to_veteran')
                                    ->orderBy('priority')
                                    ->limit($user->leads_allowed)
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

        return array('type' => 'SUCCESS', 'message' => count($leadMails) . ' Leads ' . (count($leadMails) > 1 ? 'have' : 'has') . ' been sent to your e-mail!', 'leads' => count($leadMails));

    }

    public function sendIndividualLead($leadId, $user){
        $lead = LeadMails::find($leadId);
        $lead->agent_id = $user->id;
        $lead->save();

        return  \Mail::to($user->email)->send(new LeadSent($lead));
    }

    public function downloadAttachment($leadId){
        $lead = LeadMails::find($leadId);
        return redirect(\Storage::url($lead->attachment));
    }

    public function getBody($leadId){
        $lead = LeadMails::find($leadId);
        return  json_encode(array('body' => base64_encode($lead->body)));
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
