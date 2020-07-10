<!-- To display the side bar options, just remove the class d-none. -->
<div class="fixed-plugin d-none">
    <div class="dropdown show-dropdown">
        <a href="#" data-toggle="dropdown">
            <i class="fa fa-cog fa-2x"> </i>
        </a>
        <ul class="dropdown-menu">
            <li class="header-title"> {{ __('Sidebar Style') }}</li>
            <li class="adjustments-line">
                <a href="javascript:void(0)" class="switch-trigger">
                    <p>{{ __('Background Image') }}</p>
                    <label class="switch">
                        <input type="checkbox" data-toggle="switch" checked="" data-on-color="primary" data-off-color="primary">
                        <span class="toggle"></span>
                    </label>
                    <div class="clearfix"></div>
                </a>
            </li>
            <li class="adjustments-line">
                <a href="javascript:void(0)" class="switch-trigger background-color">
                    <p>Options</p>
                    <div class="pull-right">
                        <a class="btn btn-info btn-fill" href="#"><i class="nc-icon nc-attach-87"></i></a>&nbsp;
                        <a class="btn btn-info btn-fill" data-toggle="modal" data-target="#leadsModal"><i class="nc-icon nc-paper-2"></i></a>&nbsp;
                        <a class="btn btn-danger btn-fill removeLead" href="#"><i class="nc-icon nc-simple-remove" ></i></a>
                    </div>
                    <div class="clearfix"></div>
                </a>
            </li>
            <li class="header-title">{{ __('Sidebar Images') }}</li>
            <li class="active">
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                    <img src="{{ asset('/light-bootstrap/img/sidebar-1.jpg') }}" alt="" />
                </a>
            </li>
            <li>
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                    <img src="{{ asset('light-bootstrap/img/sidebar-3.jpg') }}" alt="" />
                </a>
            </li>
            <li>
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                    <img src="{{ asset('light-bootstrap/img/sidebar-4.jpg') }}" alt="" />
                </a>
            </li>
            <li>
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                    <img src="{{ asset('light-bootstrap/img/sidebar-5.jpg') }}" alt="" />
                </a>
            </li>
        </ul>
    </div>
</div>