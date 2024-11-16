@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/maestras.css') }}">

@section('content')
    <div class="maestra-container">
        <!-- Selección de Tabla Maestra -->
        <div class="select-container">
            <h2 class="select-titulo">Tabla maestra</h2>
            <div class="select-container2">
                <select class="select-maestra" id="tablaMaestraSelect">
                    <option value="">Seleccionar</option>
                    @foreach ($tablasMaestras as $tabla)
                        <option value="{{ $tabla }}">{{ ucfirst($tabla) }}</option>
                    @endforeach
                </select>
                <button class="btn-consultar" id="consultarBtn">Consultar</button>
            </div>
        </div>

        <!-- Contenedor para cargar el contenido de clientes -->
        <div id="clientesContainer"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const consultarBtn = document.getElementById("consultarBtn");
            const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
            const clientesContainer = document.getElementById("clientesContainer");

            consultarBtn.addEventListener("click", function() {
                const tablaSeleccionada = tablaMaestraSelect.value ;
                if (!tablaSeleccionada) {
                    alert("Por favor, seleccione una tabla maestra.");
                    return;
                }

                fetch(`{{ route('maestras.clientes') }}?tabla=${tablaSeleccionada}`)
                    .then(response => response.text())
                    .then(html => {
                        clientesContainer.innerHTML = html;
                        // Remove the existing script if it is already loaded
                        const existingScript = document.querySelector('script[src="{{ asset('assets/js/clientes.js') }}"]');
                        if (existingScript) {
                            existingScript.remove();
                        }

                        const existingScript2 = document.querySelector('script[src="{{ asset('assets/js/sedes.js') }}"]');
                        if (existingScript2) {
                            existingScript2.remove();
                        }

                        const existingScript3 = document.querySelector('script[src="{{ asset('assets/js/turnos.js') }}"]');
                        if (existingScript3) {
                            existingScript3.remove();
                        }
                        // Load the script after content is inserted
                        const script = document.createElement('script');
                        switch (tablaSeleccionada) {
                            case 'clientes':
                                script.src = "{{ asset('assets/js/clientes.js') }}";
                                break;
                            case 'sedes':
                                script.src = "{{ asset('assets/js/sedes.js') }}";
                                break;
                            case 'turnos':
                                script.src = "{{ asset('assets/js/turnos.js') }}";
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
    </script>

@endsection
