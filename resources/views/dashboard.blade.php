@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'Leadbox Management System', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')

<div class="container mt-4">
    <div class="row justify-content-around" >
      <div class="col-12 col-md-4">
        <div class="card text-center" style="box-shadow: 0 0 5px #555;">
          <ul class="list-group list-group-flush">
            <li class="list-group-item active"> <h3> Today's Stats</h3>
              <small>This resets every 24 hours</small>
            </li>
            <li class="list-group-item">Emails Sent: <span id="emails-sent">13</span></li>
            <li class="list-group-item">Emails rejected: <span id="emails-sent">5</span></li>
           
          </ul>
        </div>
      </div>
        <div class="col-12 col-md-6">
          <div class="card text-center" style="box-shadow: 0 0 5px #555;">
              <ul class="list-group list-group-flush">
                <li class="list-group-item active"> <h3> Total Stats</h3>
              <small>This shows the total stats.</small>
                </li>
                <li class="list-group-item">Emails Sent: <span id="emails-sent">135</span></li>
                <li class="list-group-item">Emails rejected: <span id="emails-sent">15</span></li>
                
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
                                <input type="date" id="from-date" name="from-date"> to <input type="date" id="to-date" name="to-date">
                                <input type="submit">
                            </div>
                        </div>
                        <div class="card-body table-full-width table-responsive" >
                            <table class="table table-hover table-striped" >
                                <thead>
                                    <th>Agent ID</th>
                                    <th>Name</th>
                                    <th>Leads sent</th>
                                    <th>Most Recent</th>
                                    <th>Leads Rejected</th>
                                  
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>

                                    </tr> 
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>
                                        
                                    </tr> 
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>
                                        
                                    </tr> 
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">Agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>
                                        
                                    </tr>
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">Agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>
                                        
                                    </tr>     
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
  
</div>
@endsection