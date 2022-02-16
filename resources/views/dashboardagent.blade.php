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

        <script type="text/javascript">
            const leadBtn = document.querySelector("#generateLeadBtn");

            function lead() {

                leadBtn.innerHTML = 'Processing...';
                leadBtn.classList.add('disabled');
                leadBtn.style.color = "#555!important";
                leadBtn.style.backgroundColor = "#000";
                leadBtn.style.cursor = "not-allowed";


                $.ajax({
                    url: "/leads/get",
                    success: function(result) {
                        leadBtn.innerHTML = result.message;
                        leadBtn.title = result.message;
                        setTimeout(() => {
                            leadBtn.classList.remove('disabled');
                            location.reload();
                        }, 10000);
                    },
                    error: function(a, b, c) {
                        alert('Something Went Wrong!');
                        console.log(a, b, c);
                    }
                });
            }
        </script>
    @else
        <div class="text-center">
            <h1>The Site is undergoing Maintenance.</h1>
            <h2>It will be available again as soon as possible!</h2>
        </div>
    @endif
@endsection
