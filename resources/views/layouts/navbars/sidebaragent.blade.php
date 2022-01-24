<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <div class="top-part-side-bar">
    <a href="{{route('dashboard')}}">
      <img style="border-radius: 4px; " src="/light-bootstrap/img/logo.jpg">
    </a><p></p>
    <p>Logged in as {{ \Auth::user()->name }}</p>
    <div class="text-center">
    <a class="d-flex " href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      <i class="nc-icon nc-sun-fog-29"></i>&nbsp;{{ __('Log out') }} </a>
    </div>
  </div>
  <hr style="border-color: #fff; width: 80%; margin: 20px auto;">
  <!-- Nav links -->
  <div class="side-bar-links">
    <ul class="nav">
      <li class="nav-item">
        <a class="@if($activePage == 'dashboard') highlight @endif" href="{{route('dashboard')}}" title="Return to the dashboard"><i class="nc-icon nc-chart-pie-35"></i>&nbsp;{{ __("Dashboard") }}</a>
      </li>
      <li class="nav-item">
        <a class="@if($activePage == 'user') highlight @endif" href="{{route('profile.edit')}}" title="View or edit your profile"><i class="nc-icon nc-single-02"></i>&nbsp;{{ __("My Profile") }}</a>
      </li>
     </ul>
  </div>   
     <hr style="border-color: #fff; width: 80%; margin: 10px auto;">
         <a class="usefulLinks" title="View a list of links" data-toggle="collapse" href="#usefulLinks" @if($activeButton=='laravel') aria-expanded="true" @endif><i class="nc-icon nc-tap-01"></i>&nbsp;{{ __('Useful Links') }}</a>
      
    
    <div class="collapse @if($activeButton =='laravel') hidden @endif" id="usefulLinks">
    <ul class="nav ">
      <li ><a target="_blank" rel=”noreferrer" href="https://www.cruisertravels.com">CRUISER TRAVELS</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/s5qym0uua9/index.html">REPORT A NEW BOOKING</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="http://www.cruisertravels.com/ta-training.html">TRAINING VIDEOS</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="https://WWW.GOCCL.COM">CARNIVAL</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="https://WWW.CRUISINGPOWER.COM">ROYAL/CELEBRITY/AZAMARA</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="https://WWW.FIRSTMATES.COM">VIRGIN VOYAGES</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="https://accounts.havail.sabre.com/login/cruises/home?goto=https://cruises.sabre.com/SCDO/login.jsp">SABRE GDS </a></li>
      <li ><a target="_blank" rel=”noreferrer" href="https://www.vaxvacationaccess.com">VAX LAND GDS </a></li>
      <li ><a target="_blank" rel=”noreferrer" href="http://rccl.force.com/directtransfers/DTTRoyal">ROYAL TRANSFER LINK</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="http://rccl.force.com/directtransfers/DTTCelebrity">CELEBRITY TRANSFER LINK</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="http://www.americanexpress.com/asdonline">AMEX PLATINUM PERKS</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="http://www.agent.uplift.com ">UPLIFT</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/a7s3a3w83i/index.html">CANCELLATION FORM IN-HOUSE</a> </li>
      <li ><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/hbuvnb1wg3/index.html">MODIFY BOOKING FORM</a></li>
      <li ><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/dqbz3lajsj/index.html">SOLD ADD ON FORM</a></li>
    </ul>
  </div>
  
</div>



<span style="margin: 30px;font-size:30px;cursor:pointer;color:#001f8b;" onclick="openNav()">&#9776;</span>

<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
  document.querySelector(".main-panel").style.width = "calc(100% - 250px)"
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.querySelector(".main-panel").style.width = "100%"
}
</script>