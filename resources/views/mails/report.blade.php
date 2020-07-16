<div style="width:100%; text-align:center"><h4><b>Report From {{\Carbon\Carbon::parse($dateFrom)->format('m/d/Y')}} To {{\Carbon\Carbon::parse($dateTo)->format('m/d/Y')}}</b></h4></div>
<hr style="border:dashed darkblue 1px"/>
<table id="detailedReportTable" style="border-collapse:collapse; width:100%" >
    <thead>
        <th style="height:38px;border:solid darkgray 1px">Agent ID</th>
        <th style="height:38px;border:solid darkgray 1px">Name</th>
        <th style="height:38px;border:solid darkgray 1px">Most Recent</th>
        <th style="height:38px;border:solid darkgray 1px">Leads sent</th>
        <th style="height:38px;border:solid darkgray 1px">Leads Rejected</th>
    </thead>
    <tbody>
    @php
        $i = 0;
        $leadsTotal = $rejectedTotal = 0;
    @endphp
    @foreach($leads as $lead)
    @if($lead->agent_id == 0)
        <tr>
            <td style="color:#80cee1; height:28px;border:solid darkgray 1px"><span id="agent-id"></span></td>
            <td style="color:#80cee1; height:28px;border:solid darkgray 1px"><span id="agent-name">{{$lead->agent_name}}</span></td>
            <td style="color:#80cee1; height:28px;border:solid darkgray 1px"><span id="time-sent">{{\Carbon\Carbon::parse($lead->last_lead)->format('m/d/Y g:i A')}}</span> </td>
            <td style="color:#80cee1; height:28px;border:solid darkgray 1px"><span id="leads-sent">{{$lead->leads_count}}</span></td>
            <td style="color:#80cee1; height:28px;border:solid darkgray 1px"><span id="leads-rejected">{{$lead->leads_rejected}}</span></td>
        </tr>
    @else
        <tr>
            <td style="@if($i % 2 != 0) {{'background-color:#EEE;'}} @else {{''}} @endif height:28px;border:solid darkgray 1px"><span id="agent-id">{{$lead->agent_id}}</span></td>
            <td style="@if($i % 2 != 0) {{'background-color:#EEE;'}} @else {{''}} @endif height:28px;border:solid darkgray 1px"><span id="agent-name">{{$lead->agent_name}}</span></td>
            <td style="@if($i % 2 != 0) {{'background-color:#EEE;'}} @else {{''}} @endif height:28px;border:solid darkgray 1px"><span id="time-sent">{{\Carbon\Carbon::parse($lead->last_lead)->format('m/d/Y g:i A')}}</span> </td>
            <td style="@if($i % 2 != 0) {{'background-color:#EEE;'}} @else {{''}} @endif height:28px;border:solid darkgray 1px"><span id="leads-sent">{{$lead->leads_count}}</span></td>
            <td style="@if($i % 2 != 0) {{'background-color:#EEE;'}} @else {{''}} @endif height:28px;border:solid darkgray 1px"><span id="leads-rejected">{{$lead->leads_rejected}}</span></td>
        </tr>
    @endif
    @php
        $i++;
        $leadsTotal     += $lead->leads_count;
        $rejectedTotal  += $lead->leads_rejected;
    @endphp
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">TOTAL</th><th>{{$leadsTotal}}</th><th>{{$rejectedTotal}}</th></tr>
    </tfoot>
</table>