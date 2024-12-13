<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', env('APP_NAME', 'Mobile'))</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.cdnfonts.com/css/neo-sans-std" rel="stylesheet">

    <!-- CSS y fuentes -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/mobile.css') }}?v={{ time() }}">
   {{--  <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}?v={{ time() }}"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="mobile-wrapper">
        <!-- Encabezado -->
        <header class="mobile-header">
            <div class="logo">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            </div>
            <div class="header-icons">
                <img src="{{ asset('assets/images/alerta.png') }}" alt="Alertas" class="header-icon">
                <div class="user-info">
                    <div class="user-icon-circle" id="userDropdownToggle">
                        <img src="{{ asset('assets/images/user.png') }}" alt="Usuario" class="user-icon">
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
        </header>

        <!-- Contenido principal -->
        <main class="mobile-content">
            @yield('content', view('default_content'))
        </main>

        <!-- Footer -->
        <footer class="mobile-footer">
            <div class="footer-menu">
                <a href="{{ url('/seguimiento-actividades') }}" class="footer-icon {{ request()->is('seguimiento-actividades') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22.209" height="22.193" viewBox="0 0 22.209 22.193">
                        <g id="Grupo_22916" data-name="Grupo 22916" transform="translate(-537.266 -262.737)">
                          <path id="Trazado_17353" data-name="Trazado 17353" d="M539.623,279.3c0-.966-.01-1.932,0-2.9,0-.332-.094-.512-.447-.543a1.968,1.968,0,0,1-1.789-1.411,2.045,2.045,0,0,1,.6-2.245q2.949-2.953,5.9-5.9c.958-.958,1.955-1.882,2.863-2.885a2.336,2.336,0,0,1,3.275-.015q4.34,4.436,8.773,8.779a2.154,2.154,0,0,1-.759,3.607l-.041.013c-.827.227-.827.227-.827,1.077q0,2.595,0,5.19a2.774,2.774,0,0,1-.787,2.166,2.385,2.385,0,0,1-1.635.686c-1.139.018-2.278.012-3.417,0-.589,0-.844-.265-.846-.848,0-1.586,0-3.172,0-4.757,0-.967-.362-1.324-1.331-1.327-.548,0-1.1,0-1.643,0a1.067,1.067,0,0,0-1.2,1.194c-.008,1.557,0,3.114,0,4.671,0,.871-.2,1.069-1.067,1.07q-1.406,0-2.811,0a2.53,2.53,0,0,1-2.808-2.773C539.612,281.2,539.623,280.249,539.623,279.3Zm1.414-.524h0c0,1.167-.008,2.335,0,3.5a1.077,1.077,0,0,0,1.223,1.224c.706,0,1.413-.019,2.118.008.4.016.524-.132.519-.523-.018-1.268-.021-2.537,0-3.8a2.462,2.462,0,0,1,2.645-2.608c.648,0,1.3-.013,1.945,0a2.343,2.343,0,0,1,2.18,1.441,3.1,3.1,0,0,1,.236,1.352c.006,1.211.012,2.421,0,3.632,0,.368.105.521.492.506.648-.025,1.3-.006,1.945-.007,1.086,0,1.425-.335,1.426-1.414q0-3.113,0-6.226c0-1.332.026-1.361,1.347-1.367.393,0,.766-.041.942-.456s-.086-.676-.349-.939q-4.313-4.309-8.623-8.62c-.549-.549-.816-.546-1.375.012q-2.707,2.7-5.412,5.411-1.666,1.666-3.332,3.333a.7.7,0,0,0-.227.779.729.729,0,0,0,.711.472c.245.012.49,0,.735.007.636.01.85.222.853.866C541.041,276.5,541.037,277.636,541.037,278.774Z"/>
                        </g>
                      </svg>
                      
                    <span>Inicio</span>
                </a>
                <a href="#" class="footer-icon {{ request()->is('actividades-turno') ? 'active' : '' }}" onclick="location.href = '{{ route('actividades.turno') }}' + '?id=' + getTurnoData().turnoId + '&sede_id=' + getTurnoData().sedeId;">
                    <svg id="Grupo_23258" data-name="Grupo 23258" xmlns="http://www.w3.org/2000/svg" width="20.3" height="25.805" viewBox="0 0 20.3 25.805">
                        <path id="Trazado_17395" data-name="Trazado 17395" d="M133.509,1346.4a5.268,5.268,0,1,1,5.265,5.276A5.274,5.274,0,0,1,133.509,1346.4Zm5.274-3.886a3.891,3.891,0,1,0,3.88,3.886A3.9,3.9,0,0,0,138.783,1342.51Z" transform="translate(-128.644 -1331.736)" />
                        <path id="Trazado_17396" data-name="Trazado 17396" d="M196.6,1420.54a.816.816,0,0,1,.577.258c.224.221.447.443.667.669.074.076.124.086.206,0q.872-.885,1.753-1.76a3.178,3.178,0,0,1,.344-.321.664.664,0,0,1,.933.08.7.7,0,0,1-.037.954c-.565.574-1.137,1.141-1.706,1.711-.291.291-.58.585-.874.873a.68.68,0,0,1-.978.037c-.364-.335-.706-.694-1.057-1.044-.107-.107-.215-.212-.312-.328a.652.652,0,0,1-.093-.732A.7.7,0,0,1,196.6,1420.54Z" transform="translate(-188.474 -1406.574)" />
                        <path id="Trazado_17397" data-name="Trazado 17397" d="M17.383,1131.735q0-4.336,0-8.672a2.64,2.64,0,0,1,2.212-2.635,2.368,2.368,0,0,1,.4-.024c.684,0,1.369-.007,2.053,0,.185,0,.249-.059.239-.242-.012-.237-.005-.474,0-.712a.531.531,0,0,1,.463-.558,2.337,2.337,0,0,1,.521-.038h1.3a.215.215,0,0,0,.236-.124,2.727,2.727,0,0,1,2.65-1.691,3,3,0,0,1,1.921.587,2.6,2.6,0,0,1,.794,1.034.291.291,0,0,0,.319.2c.482-.007.963,0,1.445,0a1.514,1.514,0,0,1,.414.053.532.532,0,0,1,.426.547c0,.244.008.489,0,.733-.007.167.061.217.221.216.684-.006,1.368,0,2.053,0a2.619,2.619,0,0,1,2.625,2.621c.006,2.311,0,4.622,0,6.933,0,3.442-.03,6.885.013,10.326a2.536,2.536,0,0,1-2.565,2.562c-5.069-.013-10.138,0-15.207-.008a2.489,2.489,0,0,1-2.251-1.286,2.139,2.139,0,0,1-.276-1.125q0-1.561,0-3.121Q17.383,1134.521,17.383,1131.735Zm1.306-.032q0,4.346,0,8.692a1.016,1.016,0,0,0,.392.859,1.637,1.637,0,0,0,1.044.308H34.93c.07,0,.14,0,.209,0a1.177,1.177,0,0,0,1.224-1.308q0-2.346,0-4.692,0-6.252,0-12.5a1.3,1.3,0,0,0-1.333-1.338c-.677,0-1.354,0-2.032,0-.173,0-.234.059-.23.231.009.4.008.81,0,1.215a.648.648,0,0,1-.578.663,2.246,2.246,0,0,1-.354.028q-4.325,0-8.65,0a1.954,1.954,0,0,1-.271-.014.655.655,0,0,1-.633-.719c0-.384-.009-.768,0-1.152.006-.192-.058-.256-.251-.254-.642.009-1.286.033-1.926,0a1.39,1.39,0,0,0-1.428,1.429C18.705,1125.992,18.689,1128.847,18.689,1131.7Zm8.831-9.159c1.228,0,2.456,0,3.685,0,.183,0,.263-.047.26-.247-.01-.635-.008-1.27,0-1.905,0-.173-.059-.234-.232-.231-.439.008-.879,0-1.319,0a.722.722,0,0,1-.814-.64,1.671,1.671,0,0,0-1.989-1.233,1.635,1.635,0,0,0-1.237,1.239.773.773,0,0,1-.828.639c-.4-.024-.809,0-1.214-.008-.181,0-.246.057-.243.241.008.614,0,1.228,0,1.842,0,.3,0,.3.307.3Z" transform="translate(-17.383 -1117.042)"/>
                      </svg>
                      
                    <span>Actividades</span>
                </a>
                <a href="#" class="footer-icon {{ request()->is('inventario-turno') ? 'active' : '' }}" onclick="location.href = '{{ route('inventarios.turno') }}' + '?sede_id=' + getTurnoData().sedeId;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25.408" height="25.368" viewBox="0 0 25.408 25.368">
                        <g id="Grupo_23275" data-name="Grupo 23275" transform="translate(16.683 -330.966)">
                          <path id="Trazado_17466" data-name="Trazado 17466" d="M-15.345,343.956c0,1.949.006,3.9-.006,5.846a.476.476,0,0,0,.332.512q5.087,2.249,10.165,4.519a.635.635,0,0,0,.583-.067.678.678,0,0,1,.813.158.651.651,0,0,1,.051.775,1.6,1.6,0,0,1-1.63.511q-5.557-2.47-11.114-4.937a.825.825,0,0,1-.53-.839q0-6.8,0-13.591a.822.822,0,0,1,.534-.835q3.948-1.743,7.889-3.5c1.084-.482,2.171-.959,3.252-1.446a.947.947,0,0,1,.826.005q5.528,2.462,11.062,4.91a.855.855,0,0,1,.573.892c-.015,2-.007,4-.006,5.994a.794.794,0,0,1-.125.537.665.665,0,0,1-1.162-.2,1.343,1.343,0,0,1-.049-.47q0-2.315,0-4.629c0-.383,0-.383-.335-.234q-4.934,2.191-9.866,4.388a1.154,1.154,0,0,1-1.02,0q-4.115-1.842-8.238-3.667c-.569-.253-1.141-.5-1.7-.765-.244-.115-.3-.056-.3.2C-15.342,340-15.345,341.978-15.345,343.956ZM5.086,336.7a.725.725,0,0,0-.118-.1q-2.031-.9-4.062-1.808a.41.41,0,0,0-.366.029l-7.991,3.552c-.484.215-.966.435-1.442.649.019.105.081.119.134.142,1.318.586,2.638,1.169,3.954,1.759a.5.5,0,0,0,.45-.02q4.184-1.863,8.371-3.719C4.363,337.026,4.709,336.867,5.086,336.7Zm-6.045-2.678c-.069-.118-.175-.132-.26-.17-1-.448-2.009-.882-3-1.344a.848.848,0,0,0-.779.009q-4.506,2.017-9.02,4.016c-.077.034-.2.041-.205.142s.124.112.2.146c1.028.46,2.061.911,3.086,1.38a.674.674,0,0,0,.614-.007q3.613-1.618,7.233-3.218C-2.384,334.657-1.675,334.338-.959,334.018Z" transform="translate(0 0)" />
                          <path id="Trazado_17467" data-name="Trazado 17467" d="M197.1,550.213a6.359,6.359,0,1,1,6.365,6.374A6.354,6.354,0,0,1,197.1,550.213Zm1.337,0a5.019,5.019,0,1,0,5.079-5.012A5.013,5.013,0,0,0,198.438,550.212Z" transform="translate(-201.093 -200.253)" />
                          <path id="Trazado_17468" data-name="Trazado 17468" d="M261.519,619.483a.665.665,0,0,1-.225.5c-1.028,1.028-2.053,2.059-3.086,3.082a.659.659,0,0,1-1.021-.007q-.752-.737-1.49-1.49a.667.667,0,1,1,.932-.945c.293.28.584.563.858.861.159.173.261.17.426,0,.8-.812,1.6-1.614,2.41-2.415a.682.682,0,0,1,1.021-.048A.639.639,0,0,1,261.519,619.483Z" transform="translate(-255.975 -270.763)"/>
                        </g>
                      </svg>
                    <span>Inventario</span>
                </a>
                <a href="#" class="footer-icon {{ request()->is('info') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24.295" height="24.301" viewBox="0 0 24.295 24.301">
                        <g id="Grupo_23266" data-name="Grupo 23266" transform="translate(1367.378 -425.607)">
                          <path id="Trazado_17452" data-name="Trazado 17452" d="M-1355.157,425.607a12.14,12.14,0,0,0-12.221,12.046,12.142,12.142,0,0,0,12.012,12.255,12.138,12.138,0,0,0,12.282-12.108A12.14,12.14,0,0,0-1355.157,425.607Zm-.156,22.782a10.662,10.662,0,0,1-10.546-10.715,10.663,10.663,0,0,1,10.712-10.549A10.664,10.664,0,0,1-1344.6,437.84,10.664,10.664,0,0,1-1355.313,448.389Z" />
                          <path id="Trazado_17453" data-name="Trazado 17453" d="M-1320.4,468.026c0,1.358-.009,2.717,0,4.075.006.658-.207,1.194-.915,1.221-.788.03-1.022-.526-1.02-1.239q.012-4.075,0-8.15c0-.657.2-1.193.916-1.22.789-.03,1.025.524,1.019,1.237C-1320.411,465.31-1320.4,466.668-1320.4,468.026Z" transform="translate(-33.863 -27.897)" />
                          <path id="Trazado_17454" data-name="Trazado 17454" d="M-1320.705,446.674c-.152.737-.534,1.184-1.276,1.115a1.042,1.042,0,0,1-1.018-1.17,1.064,1.064,0,0,1,1.242-1.073C-1321.07,445.6-1320.8,446.094-1320.705,446.674Z" transform="translate(-33.361 -14.987)" />
                        </g>
                      </svg>
                      
                    <span>Información</span>
                </a>
            </div>
        </footer>
    </div>

    <!-- jQuery y Bootstrap JS CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
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

    function getTurnoData() {
        return {
            turnoId: localStorage.getItem('turno_id'),
            sedeId: localStorage.getItem('sede_id')
        };
    }
    </script>
    <script src="{{ asset('assets/js/mobile.js') }}?v={{ time() }}"></script>

    @stack('scripts')
</body>
</html>
