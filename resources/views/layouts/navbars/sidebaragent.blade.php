<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <div class="top-part-side-bar">
    <a href="{{route('dashboard')}}">
      <img style="border-radius: 4px; " src="/light-bootstrap/img/logo.jpg">
    </a><p></p>
    Logged in as: <br> {{ \Auth::user()->name }}
  </div>
  <div class="text-center">
    <button class="btn-outline-dark text-light"  href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      <i class="nc-icon nc-sun-fog-29"></i>&nbsp;{{ __('Log out') }} </button>
  </div>
  <hr style="border-color: #fff; width: 80%; margin: 20px auto;">
  <!-- Nav links -->
  <div class="side-bar-links">
    <ul class="nav" >
      <li class="nav-item">
        <a class="@if($activePage == 'dashboard') highlight @endif" href="{{route('dashboard')}}" title="Return to the dashboard"><i class="nc-icon nc-chart-pie-35"></i>&nbsp;{{ __("Dashboard") }}</a>
      </li>
      <li class="nav-item">
        <a class="@if($activePage == 'user') highlight @endif" href="{{route('profile.edit')}}" title="View or edit your profile"><i class="nc-icon nc-single-02"></i>&nbsp;{{ __("My Profile") }}</a>
      </li>
     </ul>
  </div>   
     <hr style="border-color: #fff; width: 80%; margin: 10px auto;">
     <div class="side-bar-links">
      <ul class="nav" >
        <li class="nav-item text-center">
          <button class="btn-outline-light" title="View a list of links" onclick="openLinks()"><i class="nc-icon nc-tap-01"></i>&nbsp;{{ __('Useful Links') }}</button>
        </li>
      </ul>
    </div>     
      
    
    <div class="panel d-none" id="usefulLinks"> 
      <ul class="nav ">
        <li ><a target="_blank" rel=”noreferrer" href="https://www.cruisertravels.com">cruiser travels</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/s5qym0uua9/index.html">report a new booking</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="http://www.cruisertravels.com/ta-training.html">training videos</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="https://www.goccl.com">carnival</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="https://www.cruisingpower.com">royal/celebrity/azamara</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="https://www.firstmates.com">virgin voyages</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="https://accounts.havail.sabre.com/login/cruises/home?goto=https://cruises.sabre.com/scdo/login.jsp">sabre gds </a></li>
        <li ><a target="_blank" rel=”noreferrer" href="https://www.vaxvacationaccess.com">vax land gds </a></li>
        <li ><a target="_blank" rel=”noreferrer" href="http://rccl.force.com/directtransfers/dttroyal">royal transfer link</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="http://rccl.force.com/directtransfers/dttcelebrity">celebrity transfer link</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="http://www.americanexpress.com/asdonline">amex platinum perks</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="http://www.agent.uplift.com ">uplift</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/a7s3a3w83i/index.html">cancellation form in-house</a> </li>
        <li ><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/hbuvnb1wg3/index.html">modify booking form</a></li>
        <li ><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/dqbz3lajsj/index.html">sold add on form</a></li>
      </ul>
      <div id="toTop"></div>
    </div>
  
</div>



<span style="margin: 30px;font-size:30px;cursor:pointer;color:#001f8b;" onclick="openNav()">&#9776;</span>

<script>
  function openLinks() {
    let panel = document.querySelector(".panel")
    panel.classList.toggle("d-none");
    panel.scrollIntoView({behavior: "smooth" });
  }
</script>
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