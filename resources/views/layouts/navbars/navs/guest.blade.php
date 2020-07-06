<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute">
    <div class="container">
        <div class="navbar-wrapper pt-4">
            <a class="navbar-brand" href="/"><img style="border-radius: 4px;"  src="/light-bootstrap/img/logo.jpg"></a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-bar burger-lines"></span>
                <span class="navbar-toggler-bar burger-lines"></span>
                <span class="navbar-toggler-bar burger-lines"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse justify-content-end" id="navbar">
            <ul class="navbar-nav">

                
                <li class="nav-item @if($activePage == 'login') active @endif">
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="nc-icon nc-mobile"></i> {{ __('Login') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>