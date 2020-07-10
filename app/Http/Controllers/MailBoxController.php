<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webklex\IMAP\Client;
use Carbon\Carbon;
use App\LeadMails;

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
                echo '<a href="/files/' . $filename . '">Download</a>';

                $path = substr($path, -1) == DIRECTORY_SEPARATOR ? $path : $path.DIRECTORY_SEPARATOR;
                $filename = str_replace("'", "", $path."/".$filename);

                \Illuminate\Support\Facades\File::put($filename, $masked_attachment->getContent());
            }


            $lead = LeadMails::where('email_imap_id', $oMessage->message_id);
            dump($lead->get());
            if(!$lead->get()->count()){
                $lead = new LeadMails();
                $lead->email_imap_id    = $oMessage->message_id;
                $lead->email_from       = $oMessage->getFrom()[0]->mail;
                $lead->agent_id         = 0;
                $lead->subject          = $oMessage->getSubject();
                $lead->body             = $oMessage->getHTMLBody(true);
                $lead->attachment       = $filename;
                $lead->received_date    = $oMessage->date;
                $lead->priority         = 0;
                $lead->save();
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
    public function destroy($id)
    {
        //
    }
}
