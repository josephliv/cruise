Original Date:{{\Carbon\Carbon::parse($lead->received_date)->format('m/d/Y g:i A')}}<br/>
Original Sender:{{$lead->email_from}}<br/>
Original Attachment:<a href="https://leads.cruisertravels.com/storage/{{$lead->attachment}}">Attachment</a><br/>
<hr/>
{!!$lead->body!!}