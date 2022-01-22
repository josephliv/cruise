@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'Leadbox Management System', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')

                
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
                            <p class="card-text"><span class="amount" id="availableLeads">{{$leadMails['available']}}</span></p>
                          </div>
                        </div>
                      
                        <!-- This should show how many that the logged in agent have generated. -->
                         <div class="card bg-light mb-3" style="max-width: 18rem;">
                       
                          <div class="card-body">
                            <h5 class="card-title">Leads Sent:</h5>
                            <p class="card-text"><span class="amount" id="availableLeads">{{$leadMails['totalSent']}}</span></p>
                          </div>
                        </div>
                        <div class="card bg-light mb-3" style="max-width: 18rem;">
                       
                       <div class="card-body">
                         <h5 class="card-title">Leads Rejected:</h5>
                         <p class="card-text"><span class="amount" id="availableLeads">{{$leadMails['totalReject']}}</span></p>
                       </div>
                     </div>
                        
                    </div>
                </div>
                <!-- Box that covers the button on click -->  
<div style="position: relative">
    <div  class="cover" title="A new lead has been sent to your inbox.">
      A lead has been sent to your email.
    </div>
    <div class="btn-group dropright">
  <button type="button" id="generateLeadBtn" class="btn btn-primary btn-lg dropright" onclick="lead()" title="Click here to send a lead to your inbox.">
    Receive A Lead
  </button>
  <button type="button" class="btn btn-primary btn-sm  dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false"mtitle="Show Email Tips">
    <span style="font-size: 14px; line-height: 1px;" class="text-dark font-weight-lighter font-italic">Email<br>Rules</span>
  </button>
  <div class="dropdown-menu" >
      <div class="emailRules p-2">
        <h4>Spam emails:</h4>
        <p class="lead">Just hit reply and in the body make sure the first word is spam followed by the message as to why it is spam.</p>
        <p class="lead">Example:<br>
        spam <em>&nbsp;This is advertising.</em></p>
       
        <h4>Send to another Agent:</h4>
        <p class="lead">If you receive a lead that belongs to someone else, hit reply and in the body type their email followed by the exclamation mark(!). followed by reason or comment.</p>
        <p class="lead">Example:<br>
        agent2@cruisertravels.com! <em>&nbsp;your comment or the reason here.</em></p>
      </div>
  </div>
</div> 
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
            }, 10000);
        },
        error: function(a,b,c){
            alert('Something Went Wrong!');
            console.log(a,b,c);
        }
    });
}
    
</script>
<!-- Open the email tips -->
<script>
    function openTips() {
  let x = document.querySelector(".tips");
  
  if (x.style.display == "none") {
    x.style.display = "block";
    
  } else {
    x.style.display = "none";
  }

}
//Make the DIV element draggagle:
dragElement(document.getElementById("mydiv"));

function closeDragElement() {
    mydiv.style.display = "none"
}
function dragElement(elmnt) {
    
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
    /* if present, the header is where you move the DIV from:*/
    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
    /* otherwise, move the DIV from anywhere inside the DIV:*/
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    /* stop moving when mouse button is released:*/
    document.onmouseup = null;
    document.onmousemove = null;
  }
}
</script>
@endsection