<div class="sidebar" data-color="blue" data-image="{{ asset('light-bootstrap/img/sidebar-4.jpg') }}">
    <!--
Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

Tip 2: you can also add an image using data-image tag
-->

    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="/" class="simple-text">
                <img style="border-radius: 8px;"  src="/light-bootstrap/img/logo.jpg">
            </a>
        </div>
        <ul class="nav">
            <li class="nav-item @if($activePage == 'dashboard') active @endif">
                <a class="nav-link" href="{{route('dashboard')}}">
                    <i class="nc-icon nc-chart-pie-35"></i>
                    <p>{{ __("Dashboard") }}</p>
                </a>
            </li>
           
            <li class="nav-item">
               
                <div class="collapse @if($activeButton =='laravel') show @endif" id="laravelExamples">
                    <ul class="nav">
                        <li class="nav-item @if($activePage == 'user') active @endif">
                            <a class="nav-link" href="{{route('profile.edit')}}">
                                <i class="nc-icon nc-single-02"></i>
                                <p>{{ __("My Profile") }}</p>
                            </a>
                        </li>
                        <li class="nav-item @if($activePage == 'user-management') active @endif">
                            <a class="nav-link" href="{{route('user.index')}}">
                                <i class="nc-icon nc-circle-09"></i>
                                <p>{{ __("Manage Agents") }}</p>
                            </a>
                        </li>
                        
                        <li class="nav-item @if($activePage == 'user-management') active @endif">
                            <a class="nav-link" href="{{route('emails.manage')}}">
                                <i class="nc-icon nc-email-85"></i>
                                <p>{{ __("Manage Leads") }}</p>
                            </a>
                        </li>
                        <li class="nav-item @if($activePage == 'priority-management') active @endif">
                            <a class="nav-link" href="{{route('priorities.index')}}">
                                <i class="nc-icon nc-email-85"></i>
                                <p>{{ __("Manage Priorities") }}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>