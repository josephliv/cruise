@extends('layouts.app', ['activePage' => 'user-management', 'activeButton' => 'laravel', 'title' => 'Cruiser Travels Leadbox Management System', 'navName' => 'Edit Users'])

@section('content')
    <div class="content">
        <div class="container-fluid mt--6">
            <div class="row">
                <div class="col-xl-12 order-xl-1">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Edit Agent') }}</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('user.index') }}" class="btn btn-sm btn-default">{{ __('Back to list') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('user.update', $user) }}" autocomplete="off"
                                enctype="multipart/form-data">
                                @csrf
                                @method('put')

                                @include('alerts.success')
                                @include('alerts.errors' )

                                <h6 class="heading-small text-muted mb-4">{{ __('User information') }}</h6>
                                <div class="pl-lg-4">
                                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-name">
                                            <i class="w3-xxlarge fa fa-user"></i>{{ __('Name') }}
                                        </label>
                                        <input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name', $user->name) }}" required autofocus autocomplete>

                                        @include('alerts.feedback', ['field' => 'name'])
                                    </div>
                                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                                        <input type="email" name="email" id="input-email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" value="{{ old('email', $user->email) }}" required autocomplete>

                                        @include('alerts.feedback', ['field' => 'email'])
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-password">{{ __('Password') }}</label>
                                        <input type="password" name="password" id="input-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" value="">

                                        @include('alerts.feedback', ['field' => 'password'])
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label" for="input-password-confirmation">{{ __('Confirm Password') }}</label>
                                        <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control" placeholder="{{ __('Confirm Password') }}" value="">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label" for="input-user-group">{{ __('User Level') }}</label>
                                        <select name="user_group" id="input-user_group" class="form-control" onchange="groupchange(this)">
                                        @foreach($groups as $group)
                                            <option {{$group->id == $user->user_group ? 'selected': ''}} value="{{$group->id}}">{{$group->name}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="user-attributes form-group" style="@if($user->user_group == 5) {{'display:none'}} @endif" >
                                        <label class="form-control-label" for="input-leads-allowed">{{ __('Leads Allowed') }}</label>
                                        <input type="number" name="leads_allowed" id="input-leads_allowed" class="form-control" placeholder="50" value="{{ old('leads_allowed', $user->leads_allowed) }}">
                                    </div>
                                    <label class="user-attributes form-control-label" for="time_set_init"  style="@if($user->user_group == 5) {{'display:none'}} @endif" >{{ __('Select Time Period:') }}</label>
                                    <div class="user-attributes row" style="@if($user->user_group == 5) {{'display:none'}} @endif" >
                                        <div class="col-md-6 form-group">
                                            <label class="form-control-label" for="input-time_set_init">{{ __('Initial') }}</label>
                                            <input type="time" name="time_set_init" id="time_set_init" class="user-attributes form-control" placeholder="09:00" value="{{ old('time_set_init', $user->time_set_init) }}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-control-label" for="input-time_set_final">{{ __('Final') }}</label>
                                            <input type="time" name="time_set_final" id="time_set_final" class="user-attributes form-control" placeholder="17:00" value="{{ old('time_set_final', $user->time_set_final) }}">
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-default mt-4">{{ __('Save changes') }}</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
function groupchange(obj){
    o = $(obj);
    if(parseInt(o.val()) == 5 ){
        $('#input-leads_allowed').val('0');
        $('#time_set_init').val('00:00');
        $('#time_set_final').val('00:00');
        $('.user-attributes').hide();
    } else {
        $('.user-attributes').show();
    }
}
</script>
@endsection
