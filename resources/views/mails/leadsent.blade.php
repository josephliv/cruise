Original Date:{{\Carbon\Carbon::parse($lead->received_date)->format('m/d/Y g:i A')}}<br/>
Original Sender:{{$lead->email_from}}<br/>
<hr/>
{!!$lead->body!!}