<div style="color:red"><b>Notice: {!!$message_text!!}</b></div><br/>
<hr/>
Original Date:{{\Carbon\Carbon::parse($lead->received_date)->format('m/d/Y g:i A')}}<br/>
<br/><br/><br/>
<hr/>
<br/>
{!!$lead->body!!}