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
              <p class="lead">This resets every 24 hours</p>
            </li>
            <li class="list-group-item">Emails Received: <span id="emails-sent">{{$leadMails['total24h']}}</span></li>
            <li class="list-group-item">Emails Sent: <span id="emails-sent">{{$leadMails['totalSent24h']}}</span></li>
            <li class="list-group-item">Emails rejected: <span id="emails-sent">{{$leadMails['totalReject24h']}}</span></li>

          </ul>
        </div>
      </div>
        <div class="col-12 col-md-4">
          <div class="card text-center" style="box-shadow: 0 0 5px #555;">
            <ul class="list-group list-group-flush">
            <li class="list-group-item active"> <h3> Take a lead.</h3>
              <p class="lead">Receive a lead in your inbox</p>
            </li>

            <li class="list-group-item">
                <div style="position: relative">
                        <div  class="cover" title="A new lead has been sent to your inbox.">
                         A lead has been sent to your email.</div>
                        <a href="#" id="generateLeadBtn" class="btn btn-primary btn-lg" onclick="lead()" title="Click here to send a lead to your inbox.">
                        Receive A Lead
                        </a>
                    </div>
            </div>
        </li>

          </ul>

        </div>

        <div class="col-12 col-md-4">
          <div class="card text-center" style="box-shadow: 0 0 5px #555;">
              <ul class="list-group list-group-flush">
                <li class="list-group-item active"> <h3> Total Stats</h3>
              <p class="lead">This shows the total stats.</p>
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
                            <p class=" lead">Here you can view the progress of each agent.</p>
                            <div class="p-4">
                                <label for="time-set"><p class=" lead">Run the report by dates:</p> </label>
                                <input class="lead" type="date" id="from-date" name="from-date" value="{{explode(' ', \Carbon\Carbon::now())[0]}}" > to <input class="lead" type="date" id="to-date" name="to-date" value="{{explode(' ', \Carbon\Carbon::now())[0]}}" >
                                <a href="#" class="btn btn-primary" id="detailedReportBtn">Submit</a>
                                <a href="#" class="btn btn-secondary" id="detailedEmailBtn">Send to E-mail</a>
                            </div>
                        </div>
                        <div class="card-body table-full-width table-responsive" >
                            <table id="detailedReportTable" class="table table-hover table-striped" role="table" >
                                <thead>
                                    <th>Name</th>
                                    <th>Most Recent</th>
                                    <th>Leads</th>
                                    <th>Reassigned</th>
                                    <th>Rejected</th>
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
<script type="text/javascript">
    const leadBtn = document.querySelector("#generateLeadBtn") ;
    const cover = document.querySelector(".cover")


function lead() {

    cover.innerHTML = 'Processing...';
    cover.style.visibility = "visible";
    cover.style.opacity = 1;
    leadBtn.classList.add('disabled');
    leadBtn.style.color = "#fff!important";
    cover.style.cursor = "not-allowed";

    $.ajax({
        url: "/leads/get",
        success: function(result){
            cover.innerHTML = result.message;
            setTimeout(() => {
                cover.style.visibility = "hidden";
                cover.style.opacity = 0;
                leadBtn.classList.remove('disabled');
                location.reload();
            }, 500);
        },
        error: function(a,b,c){
            alert('Something Went Wrong!');
            console.log(a,b,c);
        }
    });
}

</script>
@endsection
