<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="{{route('dashboard')}}">
        <img style="border-radius: 4px; " src="/light-bootstrap/img/main-logo.png">
    </a>
    <div class="sidenav-menu">
        <span class="logged-in-as"><small>Welcome back<br>{{ \Auth::user()->name }}</small></span>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="nc-icon nc-sun-fog-29"></i>&nbsp;{{ __('Log out') }} </a>
        <hr style="border-color: #fff; width: 80%; margin: 20px auto;">
        <a class="@if($activePage == 'dashboard') highlight @endif" href="{{route('dashboard')}}" title="Return to the dashboard"><i class="nc-icon nc-chart-pie-35"></i>&nbsp;{{ __("Dashboard") }}</a>
        <a class="@if($activePage == 'user') highlight @endif" href="{{route('profile.edit')}}" title="View or edit your profile"><i class="nc-icon nc-single-02"></i>&nbsp;{{ __("My Profile") }}</a>
        <a class="@if($activePage == 'user-management') highlight @endif" href="{{route('user.index')}}" title="View or edit your agents"><i class="nc-icon nc-circle-09"></i>&nbsp;{{ __("Manage Agents") }}</a>
        <a class="@if($activePage == 'leads-management') highlight @endif" href="{{route('emails.manage')}}" title="View or edit the leads"><i class="nc-icon nc-email-85"></i>&nbsp;{{ __("Manage Leads") }}</a>
        <a class="@if($activePage == 'priority-management') highlight @endif" href="{{route('priorities.index')}}" title="View or edit the priorities"><i class="nc-icon nc-preferences-circle-rotate"></i>&nbsp;{{ __("Rules & Priorities") }}</a>

        <a title="View a list of links" data-toggle="collapse" href="#usefulLinks" @if($activeButton=='laravel') aria-expanded="true" @endif><i class="nc-icon nc-tap-01"></i>&nbsp;{{ __('Useful Links') }}</a>
    </div>
    <div class="collapse @if($activeButton =='laravel') hidden @endif" id="usefulLinks">
        <ul class="nav usefulLinks">
            <li class="nav-item @if($activePage == 'user') active @endif">
                <a class="nav-link" target="_blank" rel="noreferral" href="https://fs8.formsite.com/loundo1/s5qym0uua9/index.html">REPORT A NEW BOOKING</a>
            </li>
            <li>
                <a class="nav-link" target="_blank" rel="noreferral" href="http://www.cruisertravels.com/ta-training.html">TRAINING VIDEOS</a>
            </li>
            <li>
                <a class="nav-link" target="_blank" rel="noreferral" rel="noreferral" href="https://WWW.GOCCL.COM">CARNIVAL</a>
            </li>
            <li><a class="nav-link" target="_blank" rel="noreferral" href="https://WWW.CRUISINGPOWER.COM">ROYAL/CELEBRITY/AZAMARA</a></li>
            <li><a class="nav-link" target="_blank" rel="noreferral" href="https://WWW.FIRSTMATES.COM">VIRGIN VOYAGES</a></li>
            <li><a class="nav-link" target="_blank" rel="noreferral" href="https://accounts.havail.sabre.com/login/cruises/home?goto=https://cruises.sabre.com/SCDO/login.jsp">SABRE GDS </a></li>
            <li><a class="nav-link" target="_blank" rel="noreferral" href=" https://www.vaxvacationaccess.com">VAX LAND BDS </a></li>
            <li><a class="nav-link" target="_blank" rel="noreferral" href="http://rccl.force.com/directtransfers/DTTRoyal">ROYAL TRANSFER LINK</a></li>
            <li><a class="nav-link" target="_blank" rel="noreferral" href="http://rccl.force.com/directtransfers/DTTCelebrity">CELEBRITY TRANSFER LINK</a></li>
            <li><a class="nav-link" target="_blank" rel="noreferral" href="http://www.americanexpress.com/asdonline">AMEX PLATINUM PERKS</a></li>
            <li><a class="nav-link" target="_blank" rel="noreferral" href="www.agent.uplift.com ">UPLIFT</a></li>
        </ul>
</div>

</div>

<span style="margin: 30px;font-size:30px;cursor:pointer;color:#001f8b;" onclick="openNav()">&#9776;</span>

<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "260px";
  document.querySelector(".main-panel").style.width = "calc(100% - 260px)"
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.querySelector(".main-panel").style.width = "100%"
}
</script>
