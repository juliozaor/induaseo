<!-- resources/views/layouts/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"="width=device, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', env('APP_NAME', 'Dashboard'))</title>

     <!-- Bootstrap CSS CDN -->
     <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

     <link href="https://fonts.cdnfonts.com/css/neo-sans-std" rel="stylesheet">

    <!-- CSS y fuentes -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}?v={{ time() }}">
    
    
</head>
<body>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Incluir el menú responsivo en dispositivos móviles -->
   {{--  @include('components.responsive_menu') --}}

    <!-- Contenedor principal con flex para alinear el menú lateral y el contenido -->
    <div class="dashboard-wrapper">
        <!-- Menú lateral con un solo enlace "Inicio" -->
        <div class="sidebar">
            <div class="logo">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
                <button class="close-sidebar" id="closeSidebar">&times;</button>
            </div>
            <ul class="menu" id="menuList">
                <!-- Los menús se cargarán aquí desde el localStorage -->
            </ul>
        </div>

        <!-- Contenedor para la barra superior y el contenido principal -->
        <div class="main-content">
            <!-- Barra superior -->
            <div class="topbar">
              <div class="hamburger-menu" id="hamburgerMenu">
                <!-- Aquí puedes usar un ícono de hamburguesa, como el de Font Awesome -->
                <img src="{{ asset('assets/icons/hamburger.svg') }}" alt="Menú" class="hamburger-icon">
            </div>
                <div class="menu-title">
                  <div class="selected-menu-icon"></div>
                   {{--  <img src="{{ asset('assets/icons/home.svg') }}" alt="Ícono de Menú" class="selected-menu-icon"> --}}
                    <span id="menuTitle">Inicio</span>
                </div>
                <div class="user-info">
                  <span>Hola, {{ Auth::user()->nombres }}</span>
                  <div class="user-icon-circle" id="userDropdownToggle">
                      <img src="{{ asset('assets/icons/usuario.png') }}" alt="Usuario" class="user-icon">
                  </div>
                  
                  <!-- Menú desplegable de usuario -->
                  <div class="dropdown-menu" id="userDropdownMenu">
                      <p>ROL: {{ Auth::user()->rol }}</p>
                      <div class="divider"></div>
                      <a href="#" class="sub-text">
                          <img src="{{ asset('assets/icons/configuracion.png') }}" alt="Configuración" class="sub-icon">Configuración de Cuenta
                      </a>
                      
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sub-text">
                        <img src="{{ asset('assets/icons/salir.png') }}" alt="Salir" class="sub-icon">Salir
                    </a>
                    
                  </div>
              </div>
            </div>
            
            <!-- Contenido principal -->
            <div class="content">
                @yield('content', view('default_content'))
            </div>
          </div>
    </div>

    <!-- Incluir el modal de alerta -->
    @include('components.alert_modal')

    <!-- jQuery y Bootstrap JS CDN para que el modal funcione -->
    
    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

      <!-- Script para activar el modal con alertas dinámicas -->
        <!-- Script para el modal de alerta dinámico -->
    <script>
      function showAlertModal(iconName, alertText) {
          const iconSrc = `{{ asset('assets/images/') }}/${iconName}`; // Construct the full path
          $('#alertIcon').attr('src', iconSrc);
          $('#alertText').text(alertText);
          $('#alertModal').modal('show');
      }

      @if(isset($menus))
          // Guardar los menús en el localStorage
          const menus = @json($menus);
          localStorage.setItem('menus', JSON.stringify(menus));
      @endif

      // Cargar los menús desde el localStorage
      const storedMenus = JSON.parse(localStorage.getItem('menus'));
      if (storedMenus) {
          const menuList = document.getElementById('menuList');
          storedMenus.forEach(menu => {
              const li = document.createElement('li');
              const currentRoute = "{{ Route::currentRouteName() }}";
             
              const isActive = currentRoute.startsWith(menu.route_name) ? 'active' : '';
              li.className = isActive;
              li.innerHTML = `<a href="{{ url('${menu.route}') }}">${menu.icon}<span>${menu.name}</span></a>`;
              menuList.appendChild(li);

              // Set the selected menu name and icon in the top bar
              if (isActive) {
                  const icono = document.querySelector('.selected-menu-icon');
                  if (icono) {
                      icono.innerHTML = menu.icon; // Insert the SVG directly
                      document.getElementById('menuTitle').textContent = menu.name;
                  }
              }
          });
      }

  </script>

  <!-- JavaScript para el menú desplegable de usuario -->
  <script>
    $(document).ready(function () {
    const userDropdownMenu = $('#userDropdownMenu');

    // Delegación de evento para el clic en #userDropdownToggle
    $(document).on('click', '#userDropdownToggle', function (event) {
        event.stopPropagation(); // Evita que el clic se propague
        userDropdownMenu.toggle();
    });

    // Ocultar el menú desplegable al hacer clic fuera del mismo
    $(document).on('click', function (e) {
        if (!userDropdownMenu.is(e.target) && userDropdownMenu.has(e.target).length === 0 && !$('#userDropdownToggle').is(e.target)) {
            userDropdownMenu.hide();
        }
    });
});

document.getElementById('hamburgerMenu').addEventListener('click', function () {
    document.querySelector('.sidebar').classList.toggle('active'); // Añadir o quitar la clase 'active'
});

$(document).ready(function () {
    const sidebar = $('#sidebar');
    const hamburgerIcon = $('#hamburgerIcon');
    const closeSidebar = $('#closeSidebar');

    // Abrir el sidebar al hacer clic en el ícono de hamburguesa
    hamburgerIcon.on('click', function () {
        sidebar.addClass('open');
    });

    // Cerrar el sidebar al hacer clic en la X
    closeSidebar.on('click', function () {
        sidebar.removeClass('open');
    });
});

  </script>

  @stack('scripts')

</body>
</html>
