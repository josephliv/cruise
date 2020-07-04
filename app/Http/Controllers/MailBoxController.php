<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webklex\IMAP\Client;

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
    $aFolder = $oClient->getFolders();
    dd($aFolder);

	//Loop through every Mailbox
	/** @var \Webklex\IMAP\Folder $oFolder */
	foreach($aFolder as $oFolder){

		//Get all Messages of the current Mailbox $oFolder
		/** @var \Webklex\IMAP\Support\MessageCollection $aMessage */
		$aMessage = $oFolder->messages()->all()->get();
		
		/** @var \Webklex\IMAP\Message $oMessage */
		foreach($aMessage as $oMessage){
			if(! $oMessage->getAttachments()->count()){
				continue;
			}
			echo $oMessage->getSubject().'<br />';
			echo 'Attachments: '.$oMessage->getAttachments()->count().'<br />';

			$attachment = $oMessage->getAttachments()->first();
			$masked_attachment = $attachment->mask();

			$token = implode('-', [$masked_attachment->id, $masked_attachment->getMessage()->getUid(), $masked_attachment->name]);
			$token = 'attc' . str_replace(' ', '_', $token);

			$path = public_path('files');
			$filename = $token;
			echo '<a href="/files/' . $filename . '">Download</a>';

        	$path = substr($path, -1) == DIRECTORY_SEPARATOR ? $path : $path.DIRECTORY_SEPARATOR;

			\Illuminate\Support\Facades\File::put(str_replace("'", "", $path."/".$filename), $masked_attachment->getContent());
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
