<div class="fixed-top d-md-none">
    <div class="collapse" id="navbarToggleExternalContent">
        <div class="bg-dark p-4">
            <div class="logo mb-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            </div>
            <ul class="menu">
                <li class="nav-item {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nav-link text-white">
                        <img src="{{ asset('assets/icons/home.svg') }}" alt="Inicio" class="menu-icon"> Inicio
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <nav class="navbar navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <span class="navbar-text text-white ml-auto">
            {{ Auth::user()->nombres }}
            <img src="{{ asset('assets/icons/usuario.png') }}" alt="Usuario" class="user-icon ml-2" id="userDropdownToggle">
        </span>
    </nav>
</div>