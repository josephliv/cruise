@extends('layouts.app', ['activePage' => 'table', 'title' => 'Cruiser Travels Leadbox Management System', 'navName' => 'Table List', 'activeButton' => 'laravel'])

@section('content')
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<style type="text/css">
table{
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

table thead th {
 text-align: left;
} 


table tbody{
    display: block;
    overflow-y: scroll;
    max-height: 500px;
}

 table thead{
     width:100%;
     display: table;
 }
</style>
</head>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card striped-tabled-with-hover">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Leads</h3>
                            <p class="card-category ">Here you can view the leads and set the priority or delete them.</p>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <th><input type="checkbox" onClick="toggle(this)"></th>
                                    <th>ID</th>
                                    <th>Sender </th>
                                    <th>Subject Line </th>
                                    <th>Attachment </th>
                                    <th>Message </th>
                                    <th>Time/date</th>
                                    
                                </thead>
                                <tbody>
                                     @foreach ($leadMails ?? '' as $leadMail)
                                    <tr>
                                        <td style="width: 50px!important"><input type="checkbox" name="lead-mail"></td>
                                        <td><span id="mail-from">{{$leadMail->id}}</span></td>
                                        <td style="width: 50px;"><span id="mail-subject">{{$leadMail->email_from}}</span></td>
                                        <!-- This should only display an attachment icon if there is an attachment sent. 
                                        This should link to that attachment for viewing. Also be available if admin wants to send this to their email. -->
                                        <td><a class="btn btn-info btn-fill" href="{{$leadMail->attachment}}"><i class="nc-icon nc-attach-87"></i></a>&nbsp;</td>
                                        <td style="max-width: 300px;"><span id="mail-body">This is the text in the body of the email. Not sure what you want to do if this is a very long email?</span></td>
                                        <td><span id="mail-date">{{$leadMail->received_date}}</span> </td>
                                    </tr>  
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $leadMails ?? ''->links() }}
                            <p class="py-2">Select the leads you want to send to your inbox and click the button below.</p>
                            <a href="#" class="btn btn-primary m-2"> Send to email</a>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card ">
                        <div class="card-header">
                            <h3 class="card-title ">Settings</h3>
                            <p class="card-category ">Here you can set the priority or delete the selected leads.</p>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group item my-2">
                                    Pull from: <select name="keywords" id="select">
                                         <option>Subject line</option>
                                         <option>body</option>
                                         <option>Sender</option>
                                        </select> 
                              </li>
                                <li class="list-group item my-2">
                                    Key words <textarea placeholder="Type key words or phrases separated by commas."></textarea> 
                                </li>
                                <li class="list-group item my-2">
                                    Select the priority:
                                            <select name="selectPriority" id="selectPriority"></select>
                                </li>
                                <li class="list-group item my-2">
                                Action:
                                             <select name="action" id="action">
                                        <option>Update</option>
                                        <option>Delete</option>
                                        </select>
                              </li>
                            </ul>
                            </div>
                             <button style="width: 100px; margin: 0 auto;" type="button" class="btn btn-secondary">Update</button>
                             
            </div>
        </div>
    </div>
    <script type="text/javascript">
   const select = document.getElementById("selectPriority");
    let contents;

for (let i = 1; i <= 100; i++) {
  contents += "<option>" + i + "</option>";
}

selectPriority.innerHTML = contents;
function toggle(source) {
  checkboxes = document.getElementsByName('lead-mail');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
    </script>
@endsection