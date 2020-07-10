@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'Leadbox Management System', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')
<style>
    
.grid-container {
    height: 80vh;
    display: grid;
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
.btn-primary {
    background-color: #0089BA!important;
    color: white;
    font-size: 1.8em;
    transition: .5s ease;
    width: ;
}
.btn-primary:hover {
    background-color: white!important;
    color: #0089BA!important; 
}
.cover {   
    text-shadow: 0 0 5px #555;
    position: absolute;
    width: 100%;
    top: 50%;
    height: 130px; 
    padding: 50px;
    text-align: center;
    font-size: 1em;
   background-color: rgba(88,88,88,0.9);
   color: #fff;
   border: 2px solid white;
   box-shadow: 5px 5px 15px #000;
   border-radius: 8px;
}
#cover {
    display: none;
}



</style>
<div class="content">
    <div class="container-fluid">
        <div class="grid-container">
            <div class="dash-content text-center" style="position: relative;">
                <div class="dash-logo height: 200px;">
                    <img src="/light-bootstrap/img/logo.jpg" class="img-thumbnail my-4"> 
                </div>
                <ul class=" text-center">
                    <!-- This should show how many leads that are in the database that hasn't been sent to an agents email yet. -->
                    <li>Available Leads: <span class="amount" id="availableLeads">300</span></li>
                    <!-- This should show how many that the logged in agent have generated. -->
                    <li>Leads Sent: <span class="amount" id="leadsGenerated">5</span></li>
                </ul>
                <div id="cover" class="cover" title="A new lead has been sent to your inbox.">A lead has been sent to your email.</div>
                  <a href="#" id="generateLeadBtn" class="btn btn-primary" onclick="lead()" title="Click here to send a lead to your inbox.">Send a Lead</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    const leadBtn = document.querySelector("#generateLeadBtn") ;
    const cover = document.querySelector("#cover")


function lead() {
    cover.style.display = "block"
    leadBtn.style.color = "#eee!important"
    setTimeout(() => cover.style.cursor = "not-allowed", 700);
    setTimeout(function() {
    cover.style.display = "none";
    leadBtn.style.color = "#fff!important"
    }, 10000);
}
    
</script>
@endsection