@extends('layouts.app', ['activePage' => 'leads-management', 'title' => 'Cruiser Travels Leadbox Management System', 'navName' => 'Leads management', 'activeButton' => 'laravel'])

@section('content')
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<meta name="viewport" content="width=device-width, initial-scale=1">

<body>

<div class="container">
  <h2>Leads</h2>
  <p>This will house all 3 tables without taking up a lot of realestate. Each tab will display a new table. click on them here to view what they will do.</p>
</div>
<div class="container">
<ul class="nav nav-tabs">
    <li class="nav-item">
  <button class="nav-link active" onclick="openReport('unassigned')">Unassigned (13)</button>
  </li> 
  <li class="nav-item">
  <button class="nav-link" onclick="openReport('assigned')">Assigned (2)</button>
  </li>
  <li class="nav-item">
  <button class="nav-link" onclick="openReport('rejected')">Rejected (3)</button>
</li>
<li class="nav-item">
  <button class="nav-link" onclick="openReport('reassigned')">Reassigned (4)</button>
</li>
</ul>
<div id="unassigned" class="type" >
  <div class="card striped-tabled-with-hover">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Unassigned Leads</h3>
                            <p class="card-category ">Here are the leads that hasn't yet been assigned to an agent.</p>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <th>Sender </th>
                                    <th>Subject Line </th>
                                    <th>Time/date</th>
                                    <th>Options</th>
                                </thead>
                                <tbody>
                                @foreach ($leadMails as $leadMail)
                                    <tr> 
                                        <td><span id="mail-from">{{$leadMail->email_from}}</span></td>
                                        <td style="width: 50px;"><span id="mail-subject">{{$leadMail->subject}}</span></td>
                                        
                                        <td><span id="mail-date">{{$leadMail->received_date}}</span> </td>
                                        <td class="d-flex justify-content-end">
                                                    @if($leadMail->attachment)
                                                        <a href="{{route('leads.download', $leadMail->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip"></i></a>
                                                    @else
                                                        <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                    @endif
                                                    <a data-toggle="modal" data-id="{{$leadMail->id}}" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file" title="Read full email."></i></a>

                                                    <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', $leadMail->id) }}' : ''"s><i class="fa fa-times" title="Delete."></i></a>
                                            </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $leadMails->links() }}
                        </div>
                    </div>
</div>

<div id="assigned" class="type" style="display:none">
  <div class="card striped-tabled-with-hover">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Assigned Leads</h3>
                            <p class="card-category ">Here you can view to what agent a lead was sent to.</p>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    
                                    <th>agent </th>
                                    <th>Subject Line </th>
                                    <th>Time/date</th>
                                    <th>Options</th>
                                </thead>
                                <tbody>
                                @foreach ($leadMails as $leadMail)
                                    <tr>
                                        <td><span id="mail-from">{{$leadMail->id}}</span></td>
                                        <td><span id="mail-from">Jimmy</span></td>
                                        <td style="width: 50px;"><span id="mail-subject">{{$leadMail->subject}}</span></td>
                                        <!-- Time stamp mm/dd/yyyy @ 3:45pm -->
                                        <td><span id="mail-date">{{$leadMail->received_date}}</span> </td>

                                        <td class="d-flex justify-content-end">
                                                    @if($leadMail->attachment)
                                                        <a href="{{route('leads.download', $leadMail->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip"></i></a>
                                                    @else
                                                        <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                    @endif
                                                    <a data-toggle="modal" data-id="{{$leadMail->id}}" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file" title="Read full email."></i></a>

                                                    <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', $leadMail->id) }}' : ''"s><i class="fa fa-times" title="Delete."></i></a>
                                            </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $leadMails->links() }}
                        </div>
                    </div>
</div>

<div id="rejected" class="type" style="display:none">
  <div class="card striped-tabled-with-hover">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Rejected Leads</h3>
                            <p class="card-category ">This will display a table showing the details of the leads that were rejected and why.</p>
                            
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    
                                    <th>Sender </th>
                                    <th>Subject Line </th>
                                    <th>Time/date</th>
                                    <th>Agent's msg</th>
                                    <th>Options</th>
                                </thead>
                                <tbody>
                                @foreach ($leadMails as $leadMail)
                                    <tr>
                                        <td><span id="mail-from">{{$leadMail->id}}</span></td>
                                        <td><span id="mail-from">{{$leadMail->email_from}}</span></td>
                                        <td style="width: 50px;"><span id="mail-subject">{{$leadMail->subject}}</span></td>
                                        
                                        <td><span id="mail-date">{{$leadMail->received_date}}</span> </td>
                                        <!-- This should show the icon and on click, display the message the agent sent after the word spam -->
                                        <td><a data-toggle="modal" data-id="{ data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file" title="Read full email."></i></a></td>
                                        <td class="d-flex justify-content-end">
                                                    @if($leadMail->attachment)
                                                        <a href="{{route('leads.download', $leadMail->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip"></i></a>
                                                    @else
                                                        <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                    @endif
                                                    <a data-toggle="modal" data-id="{{$leadMail->id}}" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file" title="Read full email."></i></a>

                                                    <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', $leadMail->id) }}' : ''"s><i class="fa fa-times" title="Delete."></i></a>
                                            </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $leadMails->links() }}
                        </div>
                    </div>
</div>
<div id="reassigned" class="type" style="display:none">
  <div class="card striped-tabled-with-hover">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Reassigned Leads</h3>
                            <p class="card-category ">Here are the leads that has been sent to an agent but assigned to another agent.</p>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    
                                    <th>Sender </th>
                                    <th>Subject Line </th>
                                    <th>Time/date</th>
                                
                                    <th>Agent Assigned</th>
                                    <th>Agent Reassigned</th>
                                    <th>Options</th>
                                </thead>
                                <tbody>
                                @foreach ($leadMails as $leadMail)
                                    <tr>
                                        
                                        <td><span id="mail-from">{{$leadMail->email_from}}</span></td>
                                        <td style="width: 50px;"><span id="mail-subject">{{$leadMail->subject}}</span></td>
                                        
                                        <td><span id="mail-date">{{$leadMail->received_date}}</span> </td>
                                           <td>assigned Agent</td>
                                          <td>redirected to agent</td>

                                        <td class="d-flex justify-content-end">
                                                    @if($leadMail->attachment)
                                                        <a href="{{route('leads.download', $leadMail->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip"></i></a>
                                                    @else
                                                        <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                    @endif
                                                    <a data-toggle="modal" data-id="{{$leadMail->id}}" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file" title="Read full email."></i></a>

                                                    <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', $leadMail->id) }}' : ''"s><i class="fa fa-times" title="Delete."></i></a>
                                            </td>
                                         
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $leadMails->links() }}
                        </div>
                    </div>
  </div>
</div>
<script>
function openReport(report) {
  var i;
  var x = document.getElementsByClassName("type");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none"; 
  }
  document.getElementById(report).style.display = "block";
}
</script>

</body>
</html>
