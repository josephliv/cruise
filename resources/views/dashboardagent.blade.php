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
}
.btn-primary:hover {
    background-color: white!important;
    color: #0089BA!important; 
}
.disabled {
    color: #eee!important;
    background-color: #888;
    cursor: not-allowed;
}

</style>
<div class="content">
    <div class="container-fluid">
        <div class="grid-container">
            <div class="dash-content">
                <div class="dash-logo ">
                    <img src="/light-bootstrap/img/logo.jpg" class="img-thumbnail my-4"> 
                </div>
                <ul>
                    <!-- This should show how many leads that are in the database that hasn't been sent to an agents email yet. -->
                    <li>Available Leads: <span class="amount" id="availableLeads">300</span></li>
                    <!-- This should show how many that the logged in agent have generated. -->
                    <li>Leads Sent: <span class="amount" id="leadsGenerated">5</span></li>
                </ul>
                  <a href="#" id="generateLeadBtn" class="btn btn-primary" onclick="lead()">Send a Lead</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    const leadBtn = document.querySelector("#generateLeadBtn") 
function lead() {
    leadBtn.disabled = true;
    leadBtn.classList.add("disabled")
    leadBtn.innerText = "Check your email."
    setTimeout(function() {
    leadBtn.disabled = false;
    leadBtn.classList.remove("disabled")
    leadBtn.innerText = "Send a lead."
    }, 5000);
}
    
</script>
@endsection