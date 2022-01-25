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
                <div class="col-12 box-item my-2">
<a target="_blank" rel="noreferrer" href="https://cruisertravels.com">cruiser travels</a> |
<a target="_blank" rel="noreferrer" href="https://fs8.formsite.com/loundo1/s5qym0uua9/index.html">report a new booking</a> |
<a target="_blank" rel="noreferrer" href="http://www.cruisertravels.com/ta-training.html">training videos</a> 
</div>
<div class="col-12 box-item my-2"> 
<a target="_blank" rel="noreferrer" href="https://www.goccl.com">carnival</a> |
<a target="_blank" rel="noreferrer" href="https://www.cruisingpower.com">royal/celebrity/azamara</a> |
<a target="_blank" rel="noreferrer" href="https://www.firstmates.com">virgin voyages</a> 
</div>
<div class="col-12 box-item my-2">
<a target="_blank" rel="noreferrer" href="https://accounts.havail.sabre.com/login/cruises/home?goto=https://cruises.sabre.com/scdo/login.jsp">sabre gds </a> |
<a href=" https://www.vaxvacationaccess.com">vax land gds </a> |
<a target="_blank" rel="noreferrer" href="http://rccl.force.com/directtransfers/dttroyal">royal transfer link</a> 
</div>
<div class="col-12 box-item my-2">
<a target="_blank" rel="noreferrer" href="http://rccl.force.com/directtransfers/dttcelebrity">celebrity transfer link</a> |
<a target="_blank" rel="noreferrer" href="http://www.americanexpress.com/asdonline">amex platinum perks</a> |
<a target="_blank" rel="noreferrer" href="http://www.agent.uplift.com">uplift</a>
</div>
<div class="col-12 box-item my-2">
<a target="_blank" rel="noreferrer" href="https://fs8.formsite.com/loundo1/a7s3a3w83i/index.html">cancellation form in-house</a> |
<a target="_blank" rel="noreferrer" href="https://fs8.formsite.com/loundo1/hbuvnb1wg3/index.html">modify booking form</a>
<a target="_blank" rel="noreferrer" href="https://fs8.formsite.com/loundo1/dqbz3lajsj/index.html">sold add on form</a>

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