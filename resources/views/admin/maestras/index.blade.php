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

        <div class="btn-nuevo">
            <button class="btn-consultar" id="openModalBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
                    <g id="Grupo_22674" data-name="Grupo 22674" transform="translate(-19 -11.164)">
                        <line id="Línea_338" data-name="Línea 338" y1="15" transform="translate(27.5 12.164)"
                            fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2" />
                        <line id="Línea_339" data-name="Línea 339" y1="15"
                            transform="translate(35 19.664) rotate(90)" fill="none" stroke="#fff" stroke-linecap="round"
                            stroke-width="2" />
                    </g>
                </svg>
                Nuevo</button>
        </div>

        <!-- Listado de Clientes -->
        <div class="listado-clientes-container">
            <h2 class="listado-clientes-titulo">Listado de cliente</h2>
            <!-- Línea divisoria -->
            <div class="divider"></div>

            <div class="busqueda-container">
                <div class="input-container">
                    <input type="text" class="busqueda-input" id="busquedaInput" placeholder="">
                </div>


                {{-- <button class="busqueda-icono">
                
            </button> --}}
                <span class="registros-encontrados">Total: 0</span>
                <select class="registros-por-pagina" id="registrosPorPagina">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>

            <!-- Tabla de datos -->
            <div class="tabla-clientes-container">
                <table class="tabla-clientes" id="tablaClientes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre / Razón social</th>
                            <th>Tipo Identificación</th>
                            <th>Número de Identificación</th>
                            <th>Estado</th>
                            <th>País</th>
                            <th>Ciudad</th>
                            <th>Dirección</th>
                            <th>Celular</th>
                            <th>Correo Electrónico</th>
                            <th>Sector Económico</th>
                            <th>Creado por</th>
                            <th>Fecha de creación</th>
                            <th>Actualizado por</th>
                            <th>Fecha actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


    <div id="createClientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="contenedor-titulo">
                    <span id="modalTitle" class="titulo-modal">Crear nuevo cliente</span>
                    <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="clientForm">
                    <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="tipoIdentificacion">Tipo de Identificación:</label>
                        <select id="tipoIdentificacion" name="tipoIdentificacion" class="form-control" required>
                            <option value="">Seleccione</option>
                            <option value="1">Cédula de ciudadanía</option>
                            <option value="2">Tarjeta de extranjería</option>
                            <option value="3">Pasaporte</option>
                            <!-- Agrega más opciones según sea necesario -->
                        </select>
                        <span class="error-message" id="errorTipoIdentificacion"></span>
                    </div>
                    <div class="col-3">
                        <label for="numeroIdentificacion">Número de Identificación:</label>
                        <input type="text" id="numeroIdentificacion" name="numeroIdentificacion" class="form-control"
                            required>
                        <span class="error-message" id="errorNumeroIdentificacion"></span>
                    </div>
                    <div class="col-6">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                        <span class="error-message" id="errorNombre"></span>
                    </div>
                </div>

                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="pais">País:</label>
                        <select id="pais" name="pais" class="form-control" required>
                            <option value="">Seleccione</option>
                            @foreach ($paises as $pais)
                                <option value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="error-message" id="errorPais"></span>
                    </div>
                    <div class="col-3">
                        <label for="ciudad">Ciudad:</label>
                        <select id="ciudad" name="ciudad" class="form-control" required>
                            <option value="">Seleccione</option>
                        </select>
                        <span class="error-message" id="errorCiudad"></span>
                    </div>
                    <div class="col-6">
                        <label for="direccion">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" class="form-control">
                        <span class="error-message" id="errorDireccion"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="celular">Celular:</label>
                        <input type="text" id="celular" name="celular" class="form-control">
                        <span class="error-message" id="errorCelular"></span>
                    </div>
                    <div class="col-3">
                        <label for="sectorEconomico">Sector Economico:</label>
                        <select id="sectorEconomico" name="sectorEconomico" class="form-control" required>
                            <option value="">Seleccione</option>
                            @foreach ($sectorEconomico as $sector)
                                <option value="{{ $sector->id }}">{{ $sector->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="error-message" id="errorSectorEconomico"></span>
                    </div>
                    <div class="col-6">
                        <label for="correo">Correo:</label>
                        <input type="email" id="correo" name="correo" class="form-control">
                        <span class="error-message" id="errorCorreo"></span>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <label class="switch">
                    <input type="checkbox" id="estadoToggle" checked>
                    <span class="slider round"></span>
                </label>
                <label for="estadoToggle">Activo</label>
                <button id="modalActionBtn" class="btn-consultar">Crear Cliente</button>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const consultarBtn = document.getElementById("consultarBtn");
            const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
            const tablaClientesBody = document.querySelector("#tablaClientes tbody");
            const paginacionContainer = document.createElement('div');
            paginacionContainer.classList.add('paginacion');
            document.querySelector(".tabla-clientes-container").appendChild(paginacionContainer);

            const busquedaInput = document.getElementById("busquedaInput");
            const registrosPorPaginaSelect = document.getElementById("registrosPorPagina");
            const openModalBtn = document.getElementById("openModalBtn"); // Ensure this is defined

            function cargarDatos(page) {


                const tablaSeleccionada = tablaMaestraSelect.value;
                const buscar = busquedaInput.value;
                const registrosPorPagina = registrosPorPaginaSelect.value;

                if (!tablaSeleccionada) {
                    /*  alert("Por favor, seleccione una tabla maestra."); */
                    return;
                }

                const formData = new FormData();
                formData.append('tabla', tablaSeleccionada);
                formData.append('buscar', buscar);
                formData.append('registros_por_pagina', registrosPorPagina);

                fetch(`{{ route('maestras.consultar') }}?page=${page}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Error en la solicitud');
                        return response.json();
                    })
                    .then(data => {
                        // Limpiar la tabla y la paginación
                        tablaClientesBody.innerHTML = '';
                        paginacionContainer.innerHTML = '';

                        // Llenar la tabla con los datos
                        data.data.forEach(cliente => {
                            
                            const row = document.createElement("tr");
                            const estadoClase = cliente.estado ? 'estado-activo' : 'estado-inactivo';
                            row.innerHTML = `
                            <td>${cliente.id}</td>
                            <td>${cliente.nombre}</td>
                            <td>${cliente.tipo_documento?.nombre || 'N/A'}</td>
                            <td>${cliente.numero_documento}</td>
                            <td><div class="${estadoClase}">${cliente.estado ? 'Activo' : 'Inactivo'}</div></td>
                            <td>${cliente.ciudad?.pais?.nombre || 'N/A'}</td>
                            <td>${cliente.ciudad?.nombre || 'N/A'}</td>
                            <td>${cliente.direccion}</td>
                            <td>${cliente.celular}</td>
                            <td>${cliente.correo}</td>
                            <td>${cliente.sector_economico?.nombre || 'N/A'}</td>
                            <td>${cliente.creador?.nombres || 'N/A'}</td>
                            <td>${cliente.created_at}</td>
                            <td>${cliente.actualizador?.nombres || 'N/A'}</td>
                            <td>${cliente.updated_at}</td>
                            <td><img src="{{ asset('assets/icons/editar.svg') }}" alt="Editar" class="icono-editar" data-id="${cliente.id}"></td>

                        `;

                            tablaClientesBody.appendChild(row);
                        });

                        // Mostrar total de registros
                        document.querySelector('.registros-encontrados').textContent =
                            `Total: ${data.total}`;

                        // Generar paginación
                        const {
                            current_page,
                            last_page
                        } = data;

                        // Limpiar la paginación anterior
                        paginacionContainer.innerHTML = '';

                        // Botón de página anterior
                        const prevButton = document.createElement("button");
                        prevButton.textContent = "Ant.";
                        prevButton.classList.add("page-button");
                        prevButton.classList.add("ant");
                        prevButton.disabled = current_page === 1;
                        prevButton.addEventListener('click', () => {
                            cargarDatos(`${current_page - 1}`);
                        });
                        paginacionContainer.appendChild(prevButton);

                        // Crear botones de página (máximo 6 números)
                        const startPage = Math.max(1, current_page - 2);
                        const endPage = Math.min(last_page, current_page + 3);

                        for (let i = startPage; i <= endPage; i++) {
                            const pageButton = document.createElement("button");
                            pageButton.classList.add('page-button');
                            pageButton.textContent = i;
                            pageButton.style = i === current_page ? 'background: #000000;' :
                                'font: normal normal normal 12px/16px Neo Sans Std; color: #4B4B4B;';
                            if (i === current_page) pageButton.classList.add('active');

                            pageButton.addEventListener('click', () => {
                                cargarDatos(`${i}`);
                            });

                            paginacionContainer.appendChild(pageButton);
                        }

                        // Botón de página siguiente
                        const nextButton = document.createElement("button");
                        nextButton.textContent = "Sig.";
                        nextButton.classList.add("page-button");
                        nextButton.classList.add("sig");
                        nextButton.disabled = current_page === last_page;
                        nextButton.addEventListener('click', () => {
                            cargarDatos(`${current_page + 1}`);
                        });
                        paginacionContainer.appendChild(nextButton);

                    })
                    .catch(error => console.error('Error:', error));
            }

            // Eventos
            consultarBtn.addEventListener("click", cargarDatos);
            registrosPorPaginaSelect.addEventListener("change", cargarDatos);
            busquedaInput.addEventListener("input", cargarDatos);

            // Modal functionality
            const modal = document.getElementById("createClientModal");
            const modalTitle = document.getElementById("modalTitle");
            const modalActionBtn = document.getElementById("modalActionBtn");
            const clientForm = document.getElementById("clientForm");
            let editMode = false;
            let clientId = null;

            // Abrir el modal
            openModalBtn.addEventListener("click", function() {
                modal.style.display = "flex";
            });

            // Cerrar el modal al hacer clic fuera de él
            window.addEventListener("click", function(e) {
                if (e.target === modal) {
                    modal.style.display = "none";
                    resetForm();
                }
            });

            document.addEventListener("click", function(event) {
                if (event.target.classList.contains("icono-editar")) {
                    clientId = event.target.getAttribute("data-id");
                    if (!clientId) {
                        console.error("Error: No se encontró el ID del cliente en el botón.");
                        return;
                    }

                    editMode = true;

                    // Aquí continúa el código de apertura del modal y carga de datos
                    modalTitle.textContent = "Editar cliente";
                    modalActionBtn.textContent = "Guardar Cambios";

                    fetch(`{{ route('clientes.obtener') }}?id=${clientId}`)
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error("Error al cargar los datos del cliente.");
                            }
                            return response.json();
                        })
                        .then((cliente) => {
                            document.getElementById("tipoIdentificacion").value = cliente.tipo_documento_id;
                            document.getElementById("numeroIdentificacion").value = cliente.numero_documento;
                            document.getElementById("nombre").value = cliente.nombre;
                            document.getElementById("pais").value = cliente.ciudad.pais.id;
                            cargarCiudades(cliente.ciudad.pais.id, cliente.ciudad_id);
                            document.getElementById("direccion").value = cliente.direccion;
                            document.getElementById("celular").value = cliente.celular;
                            document.getElementById("sectorEconomico").value = cliente.sector_economico_id;
                            document.getElementById("correo").value = cliente.correo;
                            document.getElementById("estadoToggle").checked = cliente.estado === 1;
                            document.querySelector("label[for='estadoToggle']").textContent = cliente.estado ? "Activo" : "Inactivo";

                            modal.style.display = "flex"; // Muestra el modal



                            
                        })
                        .catch((error) => console.error("Error al cargar los datos del cliente:", error));
                }
            });

            // Cerrar modal
            document.getElementById("close").addEventListener("click", function() {
                modal.style.display = "none";
                resetForm();
            });

            // Guardar cambios
            modalActionBtn.addEventListener("click", function() {
                
                const url = editMode ? `{{ route('clientes.actualizar', ':id') }}`.replace(':id', clientId) : `{{ route('clientes.guardar') }}`;
                const method = editMode ? "PUT" : "POST";

                const formData = new FormData(clientForm);
                formData.append("estado", document.getElementById("estadoToggle").checked ? 1 : 0);

                if (editMode) {
                    formData.append("_method", "PUT");
                }

                fetch(url, {
                        method: "POST", // Always use POST for FormData
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData,
                    })
                    .then((response) => {
                        if (!response.ok) {
                            return response.json().then((data) => {
                                throw data;
                            });
                        }
                        return response.json();
                    })
                    .then((data) => {
                        if (data.errors) {
                            showErrors(data.errors);
                        } else {
                            showAlertModal(
                                "{{ asset('assets/images/ok.png') }}", // Ruta del ícono de éxito
                                data.message // Mensaje de éxito
                            );
                            modal.style.display = "none";
                            resetForm();
                            cargarDatos(1); // Add this line to reload the table
                        }
                    })
                    .catch((error) => {
                        if (error.errors) {
                            showErrors(error.errors);
                        } else {
                            console.error("Error al guardar el cliente:", error);
                        }
                    });
            });

            function showErrors(errors) {
                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                for (const [key, messages] of Object.entries(errors)) {
                    const errorElement = document.getElementById(`error${capitalizeFirstLetter(key)}`);
                    if (errorElement) {
                        errorElement.textContent = messages.join(', ');
                    }
                }
            }

            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            function cargarCiudades(paisId, ciudadId = null) {
                fetch(`{{ route('obtener.ciudades') }}?pais=${paisId}`)
                    .then((response) => response.json())
                    .then((ciudades) => {
                        const ciudadSelect = document.getElementById("ciudad");
                        ciudadSelect.innerHTML = '<option value="">Seleccione</option>';
                        ciudades.forEach((ciudad) => {
                            const option = document.createElement("option");
                            option.value = ciudad.id;
                            option.textContent = ciudad.nombre;
                            if (ciudad.id == ciudadId) option.selected = true;
                            ciudadSelect.appendChild(option);
                        });
                    })
                    .catch((error) => console.error("Error al cargar las ciudades:", error));
            }

            function resetForm() {
                clientForm.reset();
                modalTitle.textContent = "Crear nuevo cliente";
                modalActionBtn.textContent = "Crear Cliente";
                editMode = false;
                clientId = null;
            }
        });
    </script>


    {{-- Cargar ciudades --}}
    
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const paisSelect = document.getElementById("pais");
            const ciudadSelect = document.getElementById("ciudad");

            // Escucha los cambios en el select de país
            paisSelect.addEventListener("change", function() {
                const paisId = paisSelect.value;

                // Limpia el select de ciudad al cambiar de país
                ciudadSelect.innerHTML = '<option value="">Seleccione</option>';

                if (paisId) {
                    fetch(`{{ route('obtener.ciudades') }}?pais=${paisId}`)
                        .then(response => response.json())
                        .then(ciudades => {
                            // Agrega las opciones de ciudades al select de ciudad
                            ciudades.forEach(ciudad => {
                                const option = document.createElement("option");
                                option.value = ciudad.id;
                                option.textContent = ciudad.nombre;
                                ciudadSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error("Error al cargar las ciudades:", error));
                }
            });
        });
    </script>


    {{-- Actualizar estado del toggle --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const estadoToggle = document.getElementById("estadoToggle");
            const estadoLabel = document.querySelector("label[for='estadoToggle']");

            // Función para actualizar la clase según el estado del toggle
            function actualizarEstadoLabel() {
                if (estadoToggle.checked) {
                    estadoLabel.classList.add("estado-activo");
                    estadoLabel.classList.remove("estado-inactivo");
                    estadoLabel.textContent = "Activo";
                } else {
                    estadoLabel.classList.add("estado-inactivo");
                    estadoLabel.classList.remove("estado-activo");
                    estadoLabel.textContent = "Inactivo";
                }
            }

            // Llama a la función cuando cambia el estado del checkbox
            estadoToggle.addEventListener("change", actualizarEstadoLabel);

            // Llama a la función al cargar la página para establecer el estilo inicial
            actualizarEstadoLabel();
        });
    </script>
@endsection
