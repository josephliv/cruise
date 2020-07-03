<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute">
    <div class="container">
        <div class="navbar-wrapper pt-4">
            <a class="navbar-brand" href="/"><img style="border-radius: 4px;"  src="/light-bootstrap/img/logo.jpg"></a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-bar burger-lines"></span>
                <span class="navbar-toggler-bar burger-lines"></span>
                <span class="navbar-toggler-bar burger-lines"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse justify-content-end" id="navbar">
            <ul class="navbar-nav">

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="nc-icon "></i> {{ __('Important Links') }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                      <a class="dropdown-item" target="_blank" href="https://www.cruisertravels.com">CRUISER TRAVELS</a>
                      <a class="dropdown-item" target="_blank" href="https://fs8.formsite.com/loundo1/s5qym0uua9/index.html">REPORT A NEW BOOKING</a>
                      <a class="dropdown-item" target="_blank" href="http://www.cruisertravels.com/ta-training.html">TRAINING VIDEOS</a>
                      <a class="dropdown-item" target="_blank" href="https://WWW.GOCCL.COM">CARNIVAL</a>
                      <a class="dropdown-item" target="_blank" href="https://WWW.CRUISINGPOWER.COM">ROYAL/CELEBRITY/AZAMARA</a>
                      <a class="dropdown-item" target="_blank" href="https://WWW.FIRSTMATES.COM">VIRGIN VOYAGES</a>
                      <a class="dropdown-item" target="_blank" href="https://accounts.havail.sabre.com/login/cruises/home?goto=https://cruises.sabre.com/SCDO/login.jsp">SABRE GDS </a>
                      <a class="dropdown-item" href=" https://www.vaxvacationaccess.com">VAX LAND BDS </a>
                      <a class="dropdown-item" target="_blank" href="http://rccl.force.com/directtransfers/DTTRoyal">ROYAL TRANSFER LINK</a>
                      <a class="dropdown-item" target="_blank" href="http://rccl.force.com/directtransfers/DTTCelebrity">CELEBRITY TRANSFER LINK</a>
                      <a class="dropdown-item" target="_blank" href="www.americanexpress.com/asdonline">AMEX PLATINUM PERKS</a>
                      <a class="dropdown-item" target="_blank" href="www.americanexpress.com/asdonline">UPLIFT</a>
                      <a class="dropdown-item" target="_blank" href="https://fs8.formsite.com/loundo1/a7s3a3w83i/index.html">CANCELLATION FORM IN-HOUSE</a>
                    </div>
                </li>
                <li class="nav-item @if($activePage == 'login') active @endif">
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="nc-icon nc-mobile"></i> {{ __('Login') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>