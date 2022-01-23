@extends('layouts.app', ['activePage' => 'leads-management', 'title' => 'Cruiser Travels Leadbox Management System', 'navName' => 'Leads Management', 'activeButton' => 'laravel'])

@section('content')
    @php
    $unassignedTotal = $assignedTotal = $rejectedTotal = $reassignedTotal = 0;
    @endphp
    <div class="content">
        <div class="container-fluid">
            <div class="col-12 mt-2">
                @include('alerts.success')
                @include('alerts.errors')
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card striped-tabled-with-hover">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Leads</h3>
                            <p class="card-category ">Here you can view or delete the leads.</p>
                        </div>
                        <div class="card-body">
                        @php
                        foreach ($leadMails as $leadMail){
                                    
                                    if(optional($leadMail)->agent_id == 0){
                                        $unassignedTotal++;
                                    } elseif(optional($leadMail)->rejected > 0){
                                        $rejectedTotal++;
                                    } elseif(optional($leadMail)->old_agent_id > 0){
                                        $reassignedTotal++;
                                    } else {
                                        $assignedTotal++;
                                    }
                        }
                        @endphp
                        <div class="p-4 text-center">
                            <label for="time-set">Run the report by dates: </label>
                            <form method='POST'>
                                @csrf
                                <input type="date" id="from-date" name="from-date" value="{{explode(' ', $dateFrom)[0]}}" > to <input type="date" id="to-date" name="to-date" value="{{explode(' ', $dateTo)[0]}}" >
                            <form>
                            <button type="submit" class="btn btn-primary">Submit</a>
                        </div>
                        <ul class="nav nav-tabs">
                        <li class="nav-item">
                        <button class="nav-link active" onclick="openReport(event, 'unassigned', this)">Unassigned ({{$unassignedTotal}})</button>
                        </li> 
                        <li class="nav-item">
                        <button class="nav-link" onclick="openReport(event, 'assigned', this)">Assigned ({{$assignedTotal}})</button>
                        </li>
                        <li class="nav-item">
                        <button class="nav-link" onclick="openReport(event, 'rejected', this)">Rejected ({{$rejectedTotal}})</button>
                        </li>
                        <li class="nav-item">
                        <button class="nav-link" onclick="openReport(event, 'reassigned', this)">Reassigned ({{$reassignedTotal}})</button>
                        </li>
                        </ul>
                        <div id="unassigned" class="type" >
                            <table class="table table-striped  table-responsive">
                                <thead>
                                    <th>#</th>
                                    <th>Sender </th>
                                    <th class="col-md-6">Subject Line </th>
                                    <th>Time/date</th>
                                    <th>Options</th>
                                </thead>
                                <tbody>
                                @foreach ($leadMails as $leadMail)
                                    @if(optional($leadMail)->agent_id == 0)
                                    <tr>
                                        <td><span id="mail-from">{{optional($leadMail)->id}}</span></td>
                                        <td><span id="mail-from">{{optional($leadMail)->email_from}}</span></td>
                                        <td class="col-md-6"><span id="mail-subject">{{optional($leadMail)->subject}}</span></td>
                                        
                                        <td class="col-md-2"><span id="mail-date">{{\Carbon\Carbon::parse(optional($leadMail)->received_date)->format('m/d/Y g:i A')}}</span> </td>
                                        <td class="d-flex justify-content-end">
                                                    @if(optional($leadMail)->attachment)
                                                        <a href="{{route('leads.download', optional($leadMail)->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip text-primary font-weight-bold"></i></a>
                                                    @else
                                                        <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                    @endif
                                                    <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-type="body" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file" title="Read full email."></i></a>
                                                    <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-original-user="{{optional(optional($leadMail)->agent)->id}}" data-type="body" data-target="#sendLeadModal" class="btn btn-link btn-warning direct-send-lead d-inline-block"><i class="fa fa-envelope" title="Manually Send Lead"></i></a>

                                                    <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', optional($leadMail)->id) }}' : ''"s><i class="fa fa-times" title="Delete."></i></a>
                                            </td>
                                    </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                            </div>
                            <div id="assigned" style="display:none" class="type" >
                                <table class="table table-striped  table-responsive">
                                    <thead>
                                        <th>#</th>
                                        <th>Sender </th>
                                        <th style="width:200px">Subject Line </th>
                                        <th style="width:200px">Agent</th>
                                        <th>Time/date</th>
                                        <th>Options</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($leadMails as $leadMail)
                                        @if($leadMail->agent_id > 0)
                                        <tr>
                                            <td><span id="mail-from">{{$leadMail->id}}</span></td>
                                            <td><span id="mail-from">{{$leadMail->email_from}}</span></td>
                                            <td>{{$leadMail->subject}}</td>
                                            <td >{{optional(optional($leadMail->agent())->first())->name}}</td>
                                            
                                            <td><span id="mail-date">{{\Carbon\Carbon::parse($leadMail->received_date)->format('m/d/Y g:i A')}}</span> </td>
                                            <td class="d-flex justify-content-end">
                                                        @if($leadMail->attachment)
                                                            <a href="{{route('leads.download', $leadMail->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip"></i></a>
                                                        @else
                                                            <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                        @endif
                                                        <a data-toggle="modal" data-id="{{$leadMail->id}}" data-type="body" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file" title="Read full email."></i></a>
                                                        <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-original-user="{{optional(optional($leadMail)->agent)->id}}" data-type="body" data-target="#sendLeadModal" class="btn btn-link btn-warning direct-send-lead d-inline-block"><i class="fa fa-envelope" title="Manually Send Lead"></i></a>

                                                        <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', optional($leadMail)->id) }}' : ''"s><i class="fa fa-times" title="Delete."></i></a>
                                                </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div id="rejected" style="display:none" class="type" >
                                <table class="table table-striped  table-responsive">
                                    <thead>
                                        <th>#</th>
                                        <th>Sender </th>
                                        <th style="width:200px">Subject Line </th>
                                        <th style="width:200px">Agent</th>
                                        <th>Time/date</th>
                                        <th>Options</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($leadMails as $leadMail)
                                        @if(optional($leadMail)->rejected != 0)
                                        <tr>
                                            <td><span id="mail-from">{{optional($leadMail)->id}}</span></td>
                                            <td><span id="mail-from">{{optional($leadMail)->email_from}}</span></td>
                                            <td>{{optional($leadMail)->subject}}</td>
                                            <td >{{optional(optional(optional($leadMail)->agent())->first())->name}}</td>
                                            
                                            <td><span id="mail-date">{{\Carbon\Carbon::parse(optional($leadMail)->received_date)->format('m/d/Y g:i A')}}</span> </td>
                                            <td class="d-flex justify-content-end">
                                                        @if(optional($leadMail)->attachment)
                                                            <a href="{{route('leads.download', optional($leadMail)->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip"></i></a>
                                                        @else
                                                            <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                        @endif
                                                        <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-type="rejected" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-paper-plane" title="Rejected message"></i></a>
                                                        <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-type="body" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file" title="Read full email."></i></a>
                                                        <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-original-user="{{optional(optional($leadMail)->agent)->id}}" data-type="body" data-target="#sendLeadModal" class="btn btn-link btn-warning direct-send-lead d-inline-block"><i class="fa fa-envelope" title="Manually Send Lead"></i></a>

                                                        <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', optional($leadMail)->id) }}' : ''"s><i class="fa fa-times" title="Delete."></i></a>
                                                </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div id="reassigned" style="display:none" class="type" >
                                <table class="table table-striped  table-responsive">
                                    <thead>
                                        <th>#</th>
                                        <th>Sender </th>
                                        <th style="width:200px">Subject Line </th>
                                        <th style="width:200px">Orig. Agent </th>
                                        <th style="width:200px">Curr. Agent </th>
                                        <th>Time/date</th>
                                        <th>Options</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($leadMails as $leadMail)
                                        @if(optional($leadMail)->old_agent_id != 0 && !$leadMail->rejected)
                                        <tr>
                                            <td><span id="mail-from">{{optional($leadMail)->id}}</span></td>
                                            <td><span id="mail-from">{{optional($leadMail)->email_from}}</span></td>
                                            <td><span id="mail-subject">{{optional($leadMail)->subject}}</span></td>
                                            <td >{{optional(optional(optional($leadMail)->old_agent())->first())->name}}</td>
                                            <td >{{optional(optional(optional($leadMail)->agent())->first())->name}}</td>
                                            
                                            <td><span id="mail-date">{{\Carbon\Carbon::parse(optional($leadMail)->received_date)->format('m/d/Y g:i A')}}</span> </td>
                                            <td class="d-flex justify-content-end">
                                                        @if(optional($leadMail)->attachment)
                                                            <a href="{{route('leads.download', $leadMail->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip"></i></a>
                                                        @else
                                                            <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                        @endif
                                                        <a data-toggle="modal" data-id="{{$leadMail->id}}" data-type="reassigned" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-paper-plane" title="Reassigned message"></i></a>
                                                        <a data-toggle="modal" data-id="{{$leadMail->id}}" data-type="body" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file" title="Read full email."></i></a>
                                                        <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-original-user="{{optional(optional($leadMail)->agent)->id}}" data-type="body" data-target="#sendLeadModal" class="btn btn-link btn-warning direct-send-lead d-inline-block"><i class="fa fa-envelope" title="Manually Send Lead"></i></a>

                                                        <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', $leadMail->id) }}' : ''"s><i class="fa fa-times" title="Delete."></i></a>
                                                </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>


    <div class="modal fade " id="leadsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="leadsModalBody" style="border:solid darkgray 1px!important; padding:25px; min-height:400px">
        ...
        </div>
    </div>
    </div>

    <div class="modal fade " id="sendLeadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="sendLeadModalBody" style="border:solid darkgray 1px!important; padding:25px; min-height:400px">
            <input type="hidden" id="transferLeadId" value="" />
            <input type="hidden" id="transferLeadOriginalAgent" value="" />
            <div class="form-group">
                <label for="exampleFormControlInput1">Select the user to transfer this lead:</label>
                <select class="form-control" id="transferLeadNewAgent">
                    @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary direct-send-lead-button">Send Lead</button>
            </div>
        </div>
    </div>
    </div>

<script>
function openReport(e, report, caller) {
  var i;
  var x = document.getElementsByClassName("type");

  e.preventDefault();

  $('.nav-link').removeClass('active');
  $(this).addClass('active');

  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none"; 
  }
  document.getElementById(report).style.display = "block";
}
</script>
@endsection
