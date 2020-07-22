@extends('layouts/app', ['activePage' => 'welcome', 'title' => 'Leadbox Management System'])

@section('content')
    <div class="full-page section-image" data-color="black" data-image="{{asset('light-bootstrap/img/full-screen-image-2.jpg')}}">
        <div class="content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-md-8">
                        <h2 class="text-white text-center mb-4">Welcome To Cruiser Travels <br> Leadbox Management System.</h2>
                        <hr style=" border-color: #bbb; margin: 2em;">
                    </div>
                </div>
            </div>
            <!-- Added box with links -->
            <div class="container">
                <div class="row text-center box-of-links">
                    <div class="col-12  box-item my-2">
                          <a target="_blank" href="https://cruisertravels.com">CRUISER TRAVELS</a> |
                          <a target="_blank" href="https://fs8.formsite.com/loundo1/s5qym0uua9/index.html">REPORT A NEW BOOKING</a> |
                          <a target="_blank" href="http://www.cruisertravels.com/ta-training.html">TRAINING VIDEOS</a> 
                    </div>
                    <div class="col-12  box-item my-2"> 
                          <a target="_blank" href="https://WWW.GOCCL.COM">CARNIVAL</a> |
                          <a target="_blank" href="https://WWW.CRUISINGPOWER.COM">ROYAL/CELEBRITY/AZAMARA</a> |
                          <a target="_blank" href="https://WWW.FIRSTMATES.COM">VIRGIN VOYAGES</a> 
                    </div>
                    <div class="col-12  box-item my-2">
                        <a target="_blank" href="https://accounts.havail.sabre.com/login/cruises/home?goto=https://cruises.sabre.com/SCDO/login.jsp">SABRE GDS </a> |
                        <a href=" https://www.vaxvacationaccess.com">VAX LAND GDS </a> |
                        <a target="_blank" href="http://rccl.force.com/directtransfers/DTTRoyal">ROYAL TRANSFER LINK</a> 
                    </div>
                    <div class="col-12  box-item my-2">
                        <a target="_blank" href="http://rccl.force.com/directtransfers/DTTCelebrity">CELEBRITY TRANSFER LINK</a> |
                        <a target="_blank" href="http://www.americanexpress.com/asdonline">AMEX PLATINUM PERKS</a> |
                        <a target="_blank" href="http://www.agent.uplift.com">UPLIFT</a>
                    </div>
                    <div class="col-12  box-item my-2">
                        <a target="_blank" href="https://fs8.formsite.com/loundo1/a7s3a3w83i/index.html">CANCELLATION FORM IN-HOUSE</a> |
                        <a target="_blank" href="https://fs8.formsite.com/loundo1/hbuvnb1wg3/index.html">MODIFY BOOKING FORM</a>
                        <a target="_blank" href="https://fs8.formsite.com/loundo1/dqbz3lajsj/index.html">SOLD ADD ON FORM</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
@push('js')
    <script>
        $(document).ready(function() {
            demo.checkFullPageBackgroundImage();

            setTimeout(function() {
                // after 1000 ms we add the class animated to the login/register card
                $('.card').removeClass('card-hidden');
            }, 700)
        });
    </script>
@endpush