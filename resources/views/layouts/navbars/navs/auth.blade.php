<style>
  .dropdown-menu a {
    font-size: .8em;
  }
</style>

<nav class="navbar navbar-expand-lg " color-on-scroll="500">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"> LEADBOX MANAGEMENT SYSTEM </a>
        <button href="" class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navigation">
            
            <ul class="navbar-nav   d-flex align-items-center mr-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="no-icon">{{ __('Links') }}</span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                      <a class="dropdown-item" target="_blank" href="https://www.cruisertravels.com">CRUISER TRAVELS</a>
                      <a class="dropdown-item" target="_blank" href="https://fs8.formsite.com/loundo1/s5qym0uua9/index.html">REPORT A NEW BOOKING</a>
                      <a class="dropdown-item" target="_blank" href="http://www.cruisertravels.com/ta-training.html">TRAINING VIDEOS</a>
                      <a class="dropdown-item" target="_blank" href="https://WWW.GOCCL.COM">CARNIVAL</a>
                      <a class="dropdown-item" target="_blank" href="https://WWW.CRUISINGPOWER.COM">ROYAL/CELEBRITY/AZAMARA</a>
                      <a class="dropdown-item" target="_blank" href="https://WWW.FIRSTMATES.COM">VIRGIN VOYAGES</a>
                      <a class="dropdown-item" target="_blank" href="https://accounts.havail.sabre.com/login/cruises/home?goto=https://cruises.sabre.com/SCDO/login.jsp">SABRE GDS </a>
                      <a class="dropdown-item" href=" https://www.vaxvacationaccess.com">VAX LAND GDS </a>
                      <a class="dropdown-item" target="_blank" href="http://rccl.force.com/directtransfers/DTTRoyal">ROYAL TRANSFER LINK</a>
                      <a class="dropdown-item" target="_blank" href="http://rccl.force.com/directtransfers/DTTCelebrity">CELEBRITY TRANSFER LINK</a>
                      <a class="dropdown-item" target="_blank" href="http://www.americanexpress.com/asdonline">AMEX PLATINUM PERKS</a>
                      <a class="dropdown-item" target="_blank" href="http://www.agent.uplift.com ">UPLIFT</a>
                      <a class="dropdown-item" target="_blank" href="https://fs8.formsite.com/loundo1/a7s3a3w83i/index.html">CANCELLATION FORM IN-HOUSE</a> 
                      <a class="dropdown-item" target="_blank" href="https://fs8.formsite.com/loundo1/hbuvnb1wg3/index.html">MODIFY BOOKING/ADD INSURANCE</a>
                      <a target="_blank" href="https://fs8.formsite.com/loundo1/dqbz3lajsj/index.html">SOLD ADD ON FORM</a>

                    </div>
                </li>
                 <li class="nav-item">
                      <span class="no-icon mr-2 nav-link"> logged in as: {{ \Auth::user()->name }} </span>
                </li>
                <li class="nav-item">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <a class="text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> {{ __('Log out') }} </a>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>