@extends('layouts.app', ['activePage' => 'priority-management', 'activeButton' => 'laravel', 'title' => 'Cruiser Travels Leadbox Management System', 'navName' => 'Create Priority'])

@section('content')
    <div class="content">
        <div class="container-fluid mt--6">
            <div class="row">
                <div class="col-xl-12 order-xl-1">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Create Priority') }}</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('priorities.index') }}" class="btn btn-sm btn-default">{{ __('Back to list') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('priorities.store') }}" enctype="multipart/form-data">
                                @csrf
                                <h6 class="heading-small text-muted mb-4">{{ __('Priorities') }}</h6>
                                <div class="pl-lg-4">
                                    <div class="form-group{{ $errors->has('description') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="description">{{ __('Title') }}</label>
                                        <input type="text" name="description" id="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="{{ __('Title') }}" value="{{ old('description') }}" required autofocus>

                                        @include('alerts.feedback', ['field' => 'description'])
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 form-group{{ $errors->has('field') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="condition">{{ __('Field') }}</label>
                                                <select name="field" id="field" class="form-control{{ $errors->has('field') ? ' is-invalid' : '' }}">
                                                    <option value ='1'>Subject Line Contains</option>
                                                    <option value ='2'>Sender is</option>
                                                </select>

                                                @include('alerts.feedback', ['field' => 'condition'])
                                        </div>
                                        <div class="col-md-9 form-group{{ $errors->has('condition') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="condition">{{ __('Conditional') }}</label>
                                            <input type="text" name="condition" id="condition" class="form-control{{ $errors->has('condition') ? ' is-invalid' : '' }}" placeholder="{{ __('Conditional') }}" value="{{ old('condition') }}" required autofocus>

                                            @include('alerts.feedback', ['field' => 'condition'])
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9 form-group{{ $errors->has('send_to_email') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="send_to_email">{{ __('Send to Specific Agent') }}</label>
                                            <input type="text" name="send_to_email" id="send_to_email" class="form-control{{ $errors->has('send_to_email') ? ' is-invalid' : '' }}" placeholder="{{ __('some@email.com') }}" value="{{ old('send_to_email') }}" autofocus>

                                            @include('alerts.feedback', ['field' => 'send_to_email'])
                                        </div>
                                        <div class="col-md-3 form-group{{ $errors->has('send_to_email') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="send_to_veteran">{{ __('Send only to Veterans') }}</label>
                                            <select name="send_to_veteran" id="send_to_veteran" class="form-control{{ $errors->has('field') ? ' is-invalid' : '' }}">
                                                <option value ='1'>Yes</option>
                                                <option value ='0' selected>No</option>
                                            </select>
                                            @include('alerts.feedback', ['field' => 'condition'])
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('priority') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="priority">{{ __('Priority Level') }}</label>
                                        <input type="number" name="priority" id="priority" class="form-control{{ $errors->has('priority') ? ' is-invalid' : '' }}" placeholder="1" value="{{ old('priority') }}" required autofocus>

                                        @include('alerts.feedback', ['field' => 'priority'])
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-default mt-4">{{ __('Create Priority') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection