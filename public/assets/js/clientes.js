(function() {
    const rutaRelativa = '/induaseo/public/';
    const consultarBtn = document.getElementById("consultarBtn");
    const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
    const tablaClientesBody = document.querySelector("#tablaClientes tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-container").appendChild(paginacionContainer);

    const busquedaInput = document.getElementById("busquedaInput");
    const registrosPorPaginaSelect = document.getElementById("registrosPorPagina");
    const openModalBtn = document.getElementById("openModalBtn"); // Ensure this is defined

    function cargarDatos(page = 1) {
        const tablaSeleccionada = tablaMaestraSelect.value;
        const buscar = busquedaInput.value;
        const registrosPorPagina = registrosPorPaginaSelect.value;

        if (!tablaSeleccionada) {
            return;
        }

        const formData = new FormData();
        formData.append('tabla', tablaSeleccionada);
        formData.append('buscar', buscar);
        formData.append('registros_por_pagina', registrosPorPagina);

        fetch(`${rutaRelativa}admin/maestras/consultar?page=${page}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error(`Error en la solicitud: ${response.statusText}`);
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
                    <td><img src="${rutaRelativa}assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${cliente.id}"></td>
                `;
                    tablaClientesBody.appendChild(row);
                });

                // Mostrar total de registros
                document.querySelector('.registros-encontrados').textContent = `Total: ${data.total}`;

                // Generar paginación
                const { current_page, last_page } = data;

                // Limpiar la paginación anterior
                paginacionContainer.innerHTML = '';

                // Botón de página anterior
                const prevButton = document.createElement("button");
                prevButton.textContent = "Ant.";
                prevButton.classList.add("page-button", "ant");
                prevButton.disabled = current_page === 1;
                prevButton.addEventListener('click', () => {
                    cargarDatos(current_page - 1);
                });
                paginacionContainer.appendChild(prevButton);

                // Crear botones de página (máximo 6 números)
                const startPage = Math.max(1, current_page - 2);
                const endPage = Math.min(last_page, current_page + 3);

                for (let i = startPage; i <= endPage; i++) {
                    const pageButton = document.createElement("button");
                    pageButton.classList.add('page-button');
                    pageButton.textContent = i;
                    pageButton.style = i === current_page ? 'background: #000000;' : 'font: normal normal normal 12px/16px Neo Sans Std; color: #4B4B4B;';
                    if (i === current_page) pageButton.classList.add('active');

                    pageButton.addEventListener('click', () => {
                        cargarDatos(i);
                    });

                    paginacionContainer.appendChild(pageButton);
                }

                // Botón de página siguiente
                const nextButton = document.createElement("button");
                nextButton.textContent = "Sig.";
                nextButton.classList.add("page-button", "sig");
                nextButton.disabled = current_page === last_page;
                nextButton.addEventListener('click', () => {
                    cargarDatos(current_page + 1);
                });
                paginacionContainer.appendChild(nextButton);
            })
            .catch(error => console.error('Error:', error));
    }

    cargarDatos(1);

    // Eventos
    consultarBtn.addEventListener("click", () => cargarDatos(1));
    registrosPorPaginaSelect.addEventListener("change", () => cargarDatos(1));
    busquedaInput.addEventListener("input", () => cargarDatos(1));

    // Modal functionality
    const modal = document.getElementById("createClientModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalActionBtn = document.getElementById("modalActionBtn");
    const clientForm = document.getElementById("clientForm");
    let editMode = false;
    let clientId = null;

    cargarPaises();
    cargarSectoresEconomicos();
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

            fetch(`${rutaRelativa}clientes?id=${clientId}`)
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
        const url = editMode ? `${rutaRelativa}clientes/actualizar/${clientId}` : `${rutaRelativa}clientes/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(clientForm);
        formData.append("estado", document.getElementById("estadoToggle").checked ? 1 : 0);

        if (editMode) {
            formData.append("_method", "PUT");
        }

        fetch(url, {
                method: "POST", // Always use POST for FormData
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                        
                        "ok.png", // Ruta del ícono de éxito
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
        fetch(`${rutaRelativa}ciudades?pais=${paisId}`)
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

    // Cargar paises
    function cargarPaises() {
        fetch(`${rutaRelativa}paises`)
            .then(response => response.json())
            .then(paises => {
                const paisSelect = document.getElementById("pais");
                paisSelect.innerHTML = '<option value="">Seleccione</option>';
                paises.forEach(pais => {
                    const option = document.createElement("option");
                    option.value = pais.id;
                    option.textContent = pais.nombre;
                    paisSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los países:", error));
    }

    // Cargar sectores económicos
    function cargarSectoresEconomicos() {
        fetch(`${rutaRelativa}sectores-economicos`)
            .then(response => response.json())
            .then(sectores => {
                const sectorEconomicoSelect = document.getElementById("sectorEconomico");
                sectorEconomicoSelect.innerHTML = '<option value="">Seleccione</option>';
                sectores.forEach(sector => {
                    const option = document.createElement("option");
                    option.value = sector.id;
                    option.textContent = sector.nombre;
                    sectorEconomicoSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los sectores económicos:", error));
    }

    // Llamar a las funciones para cargar los datos al abrir el modal
    /* openModalBtn.addEventListener("click", function() {
        
    }); */

    // Cargar ciudades
    const paisSelect = document.getElementById("pais");
    const ciudadSelect = document.getElementById("ciudad");

    // Escucha los cambios en el select de país
    paisSelect.addEventListener("change", function() {
        const paisId = paisSelect.value;

        // Limpia el select de ciudad al cambiar de país
        ciudadSelect.innerHTML = '<option value="">Seleccione</option>';

        if (paisId) {
            fetch(`${rutaRelativa}ciudades?pais=${paisId}`)
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

    // Actualizar estado del toggle
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
})();