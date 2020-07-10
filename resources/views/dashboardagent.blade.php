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
    color: #fff!important;
    font-size: 1.8em;
    transition: .5s ease;
}
.btn-primary:hover {
    background-color: white!important;
    color: #0089BA!important;
}

.cover { 
    visibility: hidden;
    position: absolute;
    opacity: 0;
    text-shadow: 0 0 5px #555;
    width: 500px;
    padding: 50px;
    text-align: center;
    font-size: 1.3em;
    top: -30px;
    left: -125px;
   background-color: rgba(9, 87, 170, 0.85);
   -webkit-transition: opacity 1800ms, visibility 1800ms;
   transition: opacity 1800ms, visibility 1800ms;
   color: #fff;
   border: 2px solid white;
   box-shadow: 5px 5px 15px #000;
   border-radius: 8px;
   z-index: 9999;
}
</style>

                
<div class="content">
    <div class="container-fluid">
        <div class="grid-container">
            <div class="dash-content text-center" >
                <div class="dash-logo height: 200px;">
                    <img src="/light-bootstrap/img/logo.jpg" class="img-thumbnail my-4"> 
                </div>

                <div class="container"> 
                    <div class="row justify-content-center">
                        <!-- This should show how many leads that are in the database that hasn't been sent to an agents email yet. -->
                        <div class="card bg-light mb-3" style="max-width: 18rem;">
                    
                          <div class="card-body">
                            <h5 class="card-title">Available Leads:</h5>
                            <p class="card-text"><span class="amount" id="availableLeads">300</span></p>
                          </div>
                        </div>
                      
                        <!-- This should show how many that the logged in agent have generated. -->
                         <div class="card bg-light mb-3" style="max-width: 18rem;">
                       
                          <div class="card-body">
                            <h5 class="card-title">Leads Sent:</h5>
                            <p class="card-text"><span class="amount" id="availableLeads">5</span></p>
                          </div>
                        </div>
                        
                    </div>
                </div>
                    <div style="position: relative">
                        <div  class="cover" title="A new lead has been sent to your inbox.">
                         A lead has been sent to your email.</div>
                        <a href="#" id="generateLeadBtn" class="btn btn-primary" onclick="lead()" title="Click here to send a lead to your inbox.">
                        Send a Lead
                        </a>
                        <!-- Box that covers the button on click -->     
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    const leadBtn = document.querySelector("#generateLeadBtn") ;
    const cover = document.querySelector(".cover")


function lead() {
    cover.style.visibility = "visible";
    cover.style.opacity = 1;
    leadBtn.classList.add('disabled');
    leadBtn.style.color = "#fff!important";
    cover.style.cursor = "not-allowed";
    setTimeout(() => {
    cover.style.visibility = "hidden";
    cover.style.opacity = 0;
    leadBtn.classList.remove('disabled'); 
    location.reload();
    }, 10000);
}
    
</script>
@endsection