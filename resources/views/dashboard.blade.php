@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'Leadbox Management System', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')
<style>
    
.grid-container {
    height: 100%;
    display: grid;
    grid-template-areas: repeat(20, 1fr);
    place-items: center center;
}

.dash-content ul li{
    list-style: none;
    float: left;
    padding: 20px;
}
.dash-logo {
    margin: 0 auto; 
    width: 200px;
}
.count {
    border: 1px solid #ccc;
}
#generateLeadBtn {
    display: grid;
    align-self: center;
}
.btn-success {
    background-color: #559900!important;
    color: white;
    font-size: 2em;
    transition: .5s ease;
}
.btn-success:hover {
    background-color: white!important;
}

</style>
    <div class="content">
        <div class="container-fluid">
            <div class="grid-container">
    <div class="dash-content">
     <div class="text-center"> 
            <!-- This should show the current agent's name that is logged in.  -->
           Logged in as: <span id="agent" style="color: green;">{{ \Auth::user()->name }}</span>
     </div>
        <div class="dash-logo "><img src="/light-bootstrap/img/logo.jpg" class="img-thumbnail my-4"> </div>
            <ul>
                <!-- This should show how many leads that are in the database that hasn't been generated yet. -->
                <li>Available Leads: <span class="amount" id="availableLeads">300</span></li>
                <!-- This should show how many that the logged in agent have generated. -->
                <li>Leads Sent: <span class="amount" id="leadsGenerated">5</span></li>
            </ul>
        <a href="#" id="generateLeadBtn" class="btn btn-success btn-lg">Send a Lead</a>
    </div>
</div>
        </div>
    </div>
@endsection