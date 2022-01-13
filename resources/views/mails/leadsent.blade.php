Original Date:{{\Carbon\Carbon::parse($lead->received_date)->format('m/d/Y g:i A')}}<br/>
Original Sender:{{$lead->email_from}}<br/>
@if($lead->attachment)
Original Attachment:<a href="/storage/{{$lead->attachment}}">Attachment</a><br/>
@endif
<hr/>
{!!$lead->body!!}