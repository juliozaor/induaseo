@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/maestras.css') }}?v={{ time() }}">

@section('content')
    <div class="maestra-container">
        <!-- Selección de Tabla Maestra -->
        <div class="select-container">
            <h2 class="select-titulo">Tabla maestra</h2>
            <div class="select-container2">
                <select class="select-maestra" id="tablaMaestraSelect">
                    <option value="">Seleccionar</option>
                    @foreach ($tablasMaestras as $tabla)
                        <option value="{{ $tabla['id'] }}">{{ $tabla['nombre'] }}</option>
                    @endforeach
                </select>
                <button class="btn-consultar" id="consultarBtn">Consultar</button>
            </div>
        </div>

        <!-- Contenedor para cargar el contenido de clientes -->
        <div id="clientesContainer"></div>
    </div>

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {

            const consultarBtn = document.getElementById("consultarBtn");
            const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
            const clientesContainer = document.getElementById("clientesContainer");

            consultarBtn.addEventListener("click", function() {
                const tablaSeleccionada = tablaMaestraSelect.value;
                if (!tablaSeleccionada) {
                    showAlertModal(
                        "error.png",
                        "Por favor, seleccione una tabla maestra."
                    );
                    /* alert("Por favor, seleccione una tabla maestra."); */
                    return;
                }

                fetch(`{{ route('maestras.clientes') }}?tabla=${tablaSeleccionada}`)
                    .then(response => response.text())
                    .then(html => {
                        clientesContainer.innerHTML = html;
                        // Remove the existing script if it is already loaded
                        const scriptsToRemove = [
                            'clientes.js',
                            'sedes.js',
                            'turnos.js',
                            'areas.js',
                            'activos.js',
                            'insumos.js',
                            'regionales.js'
                        ];

                        scriptsToRemove.forEach(fileName => {
                            document.querySelectorAll('script').forEach(script => {
                                if (script.src.includes(`assets/js/${fileName}`)) {
                                    script.remove();
                                }
                            });
                        });

                        // Load the script after content is inserted
                        const script = document.createElement('script');
                        switch (tablaSeleccionada) {
                            case 'clientes':
                                script.src =
                                    "{{ asset('assets/js/clientes.js') }}?v={{ time() }}";
                                break;
                            case 'sedes':
                                script.src =
                                "{{ asset('assets/js/sedes.js') }}?v={{ time() }}";
                                break;
                            case 'turnos':
                                script.src =
                                    "{{ asset('assets/js/turnos.js') }}?v={{ time() }}";
                                break;
                            case 'areas':
                                script.src =
                                "{{ asset('assets/js/areas.js') }}?v={{ time() }}";
                                break;
                            case 'activos':
                                script.src =
                                    "{{ asset('assets/js/activos.js') }}?v={{ time() }}";
                                break;
                            case 'insumos':
                                script.src =
                                    "{{ asset('assets/js/insumos.js') }}?v={{ time() }}";
                                break;
                            case 'regionales':
                                script.src =
                                    "{{ asset('assets/js/regionales.js') }}?v={{ time() }}";
                                break;
                            case 'actividades':
                                script.src =
                                    "{{ asset('assets/js/actividades.js') }}?v={{ time() }}";
                                break;
                            default:
                                console.error('Tabla no encontrada');
                                return;
                        }
                        document.body.appendChild(script);
                    })
                    .catch(error => console.error('Error:', error));
            });

        });
    </script> --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const consultarBtn = document.getElementById("consultarBtn");
            const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
            const clientesContainer = document.getElementById("clientesContainer");

            consultarBtn.addEventListener("click", function () {
                const tablaSeleccionada = tablaMaestraSelect.value;
                if (!tablaSeleccionada) {
                    showAlertModal(
                        "error.png",
                        "Por favor, seleccione una tabla maestra."
                    );
                    return;
                }

                fetch(`{{ route('maestras.clientes') }}?tabla=${tablaSeleccionada}`)
                    .then(response => response.text())
                    .then(html => {
                        clientesContainer.innerHTML = html;

                        // Mapeo de tabla a arreglo de scripts
                        const tablaScriptsMap = {
                            'clientes': ['clientes.js'],
                            'sedes': ['sedes.js'],
                            'turnos': ['turnos.js'],
                            'areas': ['areas.js'],
                            'activos': ['activos.js'],
                            'insumos': ['insumos.js'],
                            'regionales': ['regionales.js'],
                            'actividades': ['actividades.js'],
                            // Ejemplo si una tabla requiere más de un script
                            // 'multi': ['script1.js', 'script2.js']
                        };

                        const scriptsToLoad = tablaScriptsMap[tablaSeleccionada];

                        if (!scriptsToLoad || scriptsToLoad.length === 0) {
                            console.error('No se encontraron scripts para esta tabla.');
                            return;
                        }

                        // Eliminar scripts existentes relacionados
                        document.querySelectorAll('script').forEach(script => {
                            scriptsToLoad.forEach(fileName => {
                                if (script.src.includes(`assets/js/${fileName}`)) {
                                    script.remove();
                                }
                            });
                        });

                        // Cargar los scripts necesarios con versión única
                        scriptsToLoad.forEach(fileName => {
                            const newScript = document.createElement('script');
                            newScript.src = `{{ asset('assets/js') }}/${fileName}?v=${Date.now()}`;
                            newScript.defer = true;
                            document.body.appendChild(newScript);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            });

        });
    </script>



@endsection
