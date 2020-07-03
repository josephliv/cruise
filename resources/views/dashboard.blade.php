@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'Leadbox Management System', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">{{ __('Emails Sent') }}</h4>
                            <p class="card-category">{{ __('Daily Performance') }}</p>
                        </div>
                        <div class="card-body ">
                            <div class="legend">
                                <ul class="list-group">
                                    <!-- This number must reset every 24 hours but be stored in the database to -->
                                    <li class="list-group-item"><i class="fa fa-circle text-success"></i> Sent: <span id="emails-sent">50</span></li>
                                    <li class="list-group-item"><i class="fa fa-circle text-danger"></i>Spam: <span id="reject-spam">5</span></li>
                                    <li class="list-group-item"> <i class="fa fa-circle text-warning"></i>Duplicates: <span id="reject-duplicate">2</span> </li>
                                    <li class="list-group-item"> 
                                        <a class="btn btn-info" href="{{route('page.index', 'table')}}">
                                    {{ __("View Details") }}
                                        </a> 
                                    </li>

                                </ul>
                              </div>   
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">{{ __('Weekly Report') }}</h4>
                            <p>This shows the reports for the last 5 days</p>
                        </div>
                        <div class="card-body ">
                            <ul class="list-group">
                                    <!-- These numbers must reset every 24 hours but be stored in the database to -->
                                    <li class="list-group-item"><i class="fa fa-circle text-success"></i> Sent: <span id="emails-sent">500</span></li>
                                    <li class="list-group-item"><i class="fa fa-circle text-danger"></i>Spam: <span id="reject-spam">15</span></li>
                                    <li class="list-group-item"> <i class="fa fa-circle text-warning"></i>Duplicates: <span id="reject-duplicate">25</span> </li>
                                </ul>
                        </div>
                        <div class="card-footer ">
                        
                            <hr>
                            <div class="stats">
                                <i class="fa fa-history"></i> {{ __('Updated 3 minutes ago') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection