Original Date:{{\Carbon\Carbon::parse($lead->received_date)->format('m/d/Y g:i A')}}<br/>
Original Sender:{{$lead->email_from}}<br/>
@if($lead->attachment)
Original Attachment:<a href="https://leads.cruisertravels.com/storage/{{$lead->attachment}}">Attachment</a><br/>
@endif
<hr/>
{!!$lead->body!!}