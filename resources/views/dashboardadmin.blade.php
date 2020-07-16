@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'Leadbox Management System', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')

<div class="container mt-4">
    <div class="row justify-content-around" >
      <div class="col-12 col-md-4"><h2>Admin Dashboard</h2></div>
    </div>
</div>
<div class="container mt-4">
    <div class="row justify-content-around" >
      <div class="col-12 col-md-4">
        <div class="card text-center" style="box-shadow: 0 0 5px #555;">
          <ul class="list-group list-group-flush">
            <li class="list-group-item active"> <h3> Today's Stats</h3>
              <small>This resets every 24 hours</small>
            </li>
            <li class="list-group-item">Emails Received: <span id="emails-sent">{{$leadMails['total24h']}}</span></li>
            <li class="list-group-item">Emails Sent: <span id="emails-sent">{{$leadMails['totalSent24h']}}</span></li>
            <li class="list-group-item">Emails rejected: <span id="emails-sent">{{$leadMails['totalReject24h']}}</span></li>
           
          </ul>
        </div>
      </div>
        <div class="col-12 col-md-6">
          <div class="card text-center" style="box-shadow: 0 0 5px #555;">
              <ul class="list-group list-group-flush">
                <li class="list-group-item active"> <h3> Total Stats</h3>
              <small>This shows the total stats.</small>
                </li>
                <li class="list-group-item">Emails Received: <span id="emails-sent">{{$leadMails['total']}}</span></li>
                <li class="list-group-item">Emails Sent: <span id="emails-sent">{{$leadMails['totalSent']}}</span></li>
                <li class="list-group-item">Emails rejected: <span id="emails-sent">{{$leadMails['totalReject']}}</span></li>
              </ul>
            </div>
        </div>
    </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover" style="box-shadow: 0 0 5px #555;">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Detailed Reports</h3>
                            <p class="card-category ">Here you can view the progress of each agent.</p>
                            <div class="p-4">
                                <label for="time-set">Run the report by dates: </label>
                                <input type="date" id="from-date" name="from-date" value="{{explode(' ', \Carbon\Carbon::now())[0]}}" > to <input type="date" id="to-date" name="to-date" value="{{explode(' ', \Carbon\Carbon::now())[0]}}" >
                                <a href="#" class="btn btn-primary" id="detailedReportBtn">Submit</a>
                                <a href="#" class="btn btn-secondary" id="detailedEmailBtn">Send to E-mail</a>
                            </div>
                        </div>
                        <div class="card-body table-full-width table-responsive" >
                            <table id="detailedReportTable" class="table table-hover table-striped" >
                                <thead>
                                    <th>Name</th>
                                    <th class="text-center">Most Recent</th>
                                    <th class="text-center">Leads</th>
                                    <th class="text-center">Reassigned</th>
                                    <th class="text-center">Rejected</th>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
  
</div>
@endsection