@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'Leadbox Management System', 'navName' => 'Dashboard',
'activeButton' => 'laravel'])

@section('content')
    @if (ENABLE_MAILER)
        {
        <style>
            .agent {
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                height: 100vh;
                overflow-y: hidden;
            }

            .main-panel::-webkit-scrollbar {
                display: none;
                -ms-overflow-style: none;
                /* IE and Edge */
                scrollbar-width: none;
                /* Firefox */
            }

        </style>
        <div class="agent">

            <div class="jumbotron bg-transparent">
                <div class="text-lg-left">
                    Level: {{$user['group']->name}}<br>
                    <p>Operating Hours: {{$user['userInfo']->time_set_init}} to {{$user['userInfo']->time_set_final}}<br>
                        Local Time: <span id="time"></span>
                </div>
                <div class="row">
                    <table class="table table-bordered " style="width: 400px; ">
                        <thead>
                        <tr>
                            <td colspan="3">
                                <img class="m-4" src="/light-bootstrap/img/logo.png">
                            </td>
                        </tr>
                        <tr>
                            <th scope="col">Available leads</th>
                            <th scope="col">Leads Sent</th>
                            <th scope="col">Leads Rejected</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $leadMails['available'] }}</td>
                            <td>{{ $leadMails['totalSent'] }}</td>
                            <td>{{ $leadMails['totalReject'] }}</td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div class="btn-group dropright">
                                    <button type="button" id="generateLeadBtn" class="btn btn-primary btn-lg dropright"
                                            onclick="lead()" title="Click here to send a lead to your inbox.">
                                        Request A Lead
                                    </button>
                                    <button style="padding: 0 14px;" type="button"
                                            class="btn btn-primary btn-sm dropdown-toggle-split" data-toggle="dropdown"
                                            aria-expanded="false" mtitle="Show Email Tips">
                                        <span style="font-size: 14px; "
                                              class="text-dark font-weight-lighter font-italic">Email
                                            Rules</span>
                                    </button>
                                    <div class="dropdown-menu">
                                        <div class="emailRules p-2">
                                            <h4>Spam emails:</h4>
                                            <p class="lead">Just hit reply and in the body make sure the first
                                                word is
                                                spam followed by the message as to why it is spam.</p>
                                            <p class="lead">Example:<br>
                                                spam <em>&nbsp;This is advertising.</em></p>

                                            <h4>Send to another Agent:</h4>
                                            <p class="lead">If you receive a lead that belongs to someone else,
                                                hit reply
                                                and in the body type their email followed by the exclamation mark(!).
                                                followed by
                                                reason or comment.</p>
                                            <p class="lead">Example:<br>
                                                agent2@cruisertravels.com! <em>&nbsp;your comment or the reason here.</em>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            let time_start = '{{$user['userInfo']->time_set_init}}';
            let time_end = '{{$user['userInfo']->time_set_final}}';

            const leadBtn = document.querySelector("#generateLeadBtn");

            function lead() {
                leadBtn.innerHTML = 'Processing...';
                leadBtn.classList.add('disabled');
                leadBtn.style.color = "#555!important";
                leadBtn.style.backgroundColor = "#000";
                leadBtn.style.cursor = "not-allowed";

                $.ajax({
                    url: "/leads/get",
                    success: function (result) {
                        leadBtn.innerHTML = result.message;
                        leadBtn.title = result.message;
                        setTimeout(() => {
                            leadBtn.classList.remove('disabled');
                            location.reload();
                        }, 10000);
                    },
                    error: function (a, b, c) {
                        alert('Something Went Wrong!');
                        console.log(a, b, c);
                    }
                });
            }

            let timer = document.getElementById('time');
            let refreshLeadBtnDisableTimeout = 0;

            let today = new Date();
            let currentTime = today.toLocaleTimeString('it-IT');
            let allReadyDisabledLeadBtnFlag = false;
            let allReadyEnabledLeadBtnFlag = false;
            checkRequestTimePeriod();

            function checkRequestTimePeriod() {
                if (currentTime < time_end && currentTime > time_start) {
                    if (allReadyEnabledLeadBtnFlag === false) {
                        console.log('You Can process a Lead');
                        leadBtn.disabled = false;
                        leadBtn.classList.remove('btnDisabled');
                        leadBtn.innerHTML = 'Request A Lead';
                        allReadyEnabledLeadBtnFlag = true;
                        allReadyDisabledLeadBtnFlag = false;
                    }
                } else {
                    if (allReadyDisabledLeadBtnFlag === false) {
                        console.log('You cannot process a Lead');
                        leadBtn.disabled = true;
                        leadBtn.classList.add('btnDisabled');
                        leadBtn.innerHTML = 'Outside Hours';
                        allReadyDisabledLeadBtnFlag = true;
                        allReadyEnabledLeadBtnFlag = false;
                    }
                }
            }

            function startTime() {
                let today = new Date();
                currentTime = today.toLocaleTimeString('it-IT');
                timer.innerHTML = currentTime;
                if (refreshLeadBtnDisableTimeout > 2) {
                    refreshLeadBtnDisableTimeout = 0;
                    checkRequestTimePeriod();
                } else {
                    refreshLeadBtnDisableTimeout += 1;
                }
                t = setTimeout(function () {
                    startTime()
                }, 500);
            }

            startTime();
        </script>
    @else
        <div class="text-center">
            <h1>The Site is undergoing Maintenance.</h1>
            <h2>It will be available again as soon as possible!</h2>
        </div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script>


@endsection
