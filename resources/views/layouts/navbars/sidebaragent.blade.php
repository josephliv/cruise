<div class="sidebar" data-color="blue" data-image="{{ asset('light-bootstrap/img/sidebar-4.jpg') }}">
    <!--
Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

Tip 2: you can also add an image using data-image tag
-->
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="/home" class="simple-text">
                <img style="border-radius: 4px;" src="/light-bootstrap/img/logo.jpg">
            </a>
        </div>
        <ul class="nav">
            <div id="mobileSideMenu" class="d-none"></div>

            <li class="nav-item @if($activePage == 'dashboard') active @endif">
                <a class="nav-link" href="{{route('dashboard')}}">
                    <i class="nc-icon nc-chart-pie-35"></i>
                    <p>{{ __("Dashboard") }}</p>
                </a>
            </li>
            <!-- THIS MAY BE ENABLED AGAIN, LET'S SEE WHAT HE SAYS. -->
            <li class="nav-item @if($activePage == 'user') active @endif">
                <a class="nav-link" href="{{route('profile.edit')}}">
                    <i class="nc-icon nc-single-02"></i>
                    <p>{{ __("My Profile") }}</p>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#laravelExamples" @if($activeButton=='laravel') aria-expanded="true" @endif>
                    <i class="nc-icon nc-tap-01"></i>
                    <p>
                        {{ __('Important Links') }}

                    </p>
                </a>
                <div class="collapse @if($activeButton =='laravel') hidden @endif" id="laravelExamples">
                    <ul class="nav">
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
            </li>
            
        </ul>
    </div>
</div>
