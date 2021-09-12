<nav class="navbar navbar-expand-lg " color-on-scroll="500">
    <a class="navbar-brand" href="/home"> Leadbox Management System </a>

    
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
           <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
        </button>
         
       
        <div class="collapse navbar-collapse justify-content-end" id="navigation">
            
            <ul class="navbar-nav   d-flex align-items-center mr-4">
           
            <li class="nav-item mx-4">
            <a href="#" class="collapsed list-group-item-action mobileLink" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" >
                     Links
                    </a>
                
               
                <div id="collapseTwo" class="collapse dropCustom" aria-labelledby="headingTwo" data-parent="#accordionExample">
                  <div class="">
                    <ul class="list-group ">
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://www.cruisertravels.com">CRUISER TRAVELS</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/s5qym0uua9/index.html">REPORT A NEW BOOKING</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="http://www.cruisertravels.com/ta-training.html">TRAINING VIDEOS</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://WWW.GOCCL.COM">CARNIVAL</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://WWW.CRUISINGPOWER.COM">ROYAL/CELEBRITY/AZAMARA</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://WWW.FIRSTMATES.COM">VIRGIN VOYAGES</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://accounts.havail.sabre.com/login/cruises/home?goto=https://cruises.sabre.com/SCDO/login.jsp">SABRE GDS </a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://www.vaxvacationaccess.com">VAX LAND GDS </a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="http://rccl.force.com/directtransfers/DTTRoyal">ROYAL TRANSFER LINK</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="http://rccl.force.com/directtransfers/DTTCelebrity">CELEBRITY TRANSFER LINK</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="http://www.americanexpress.com/asdonline">AMEX PLATINUM PERKS</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="http://www.agent.uplift.com ">UPLIFT</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/a7s3a3w83i/index.html">CANCELLATION FORM IN-HOUSE</a> </li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/hbuvnb1wg3/index.html">MODIFY BOOKING FORM</a></li>
                      <li class="list-group-item"><a target="_blank" rel=”noreferrer" href="https://fs8.formsite.com/loundo1/dqbz3lajsj/index.html">SOLD ADD ON FORM</a></li>
                    </ul>
                  </div>
                </div>
            </li>
            
       
                 <li class="nav-item">
                      <span class="no-icon mr-2 nav-link"> logged in as: {{ \Auth::user()->name }} </span>
                </li>
                <li class="nav-item text-center">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <a class="text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> {{ __('Log out') }} </a>
                    </form>
                </li>
            </ul>
        </div>

</nav>