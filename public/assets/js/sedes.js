(function() {
    const rutaRelativa = '/induaseo/public/';
    const consultarBtn = document.getElementById("consultarBtn");
    const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
    const tablaSedesBody = document.querySelector("#tablaSedes tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-container").appendChild(paginacionContainer);

    const busquedaInput = document.getElementById("busquedaInput");
    const registrosPorPaginaSelect = document.getElementById("registrosPorPagina");
    const openModalBtn = document.getElementById("openModalBtn");

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
                tablaSedesBody.innerHTML = '';
                paginacionContainer.innerHTML = '';

                // Llenar la tabla con los datos
                data.data.forEach(sede => {
                    const row = document.createElement("tr");
                    const estadoClase = sede.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                    <td>${sede.id}</td>
                    <td>${sede.cliente.nombre}</td>
                    <td>${sede.ciudad.nombre}</td>
                    <td>${sede.direccion}</td>
                    <td>${sede.telefono}</td>
                    <td>${sede.horario_inicio}</td>
                    <td>${sede.horario_fin}</td>
                    <td><div class="${estadoClase}">${sede.estado ? 'Activo' : 'Inactivo'}</div></td>
                    <td>${sede.creador?.nombres || 'N/A'}</td>
                    <td>${sede.created_at}</td>
                    <td>${sede.actualizador?.nombres || 'N/A'}</td>
                    <td>${sede.updated_at}</td>
                    <td><img src="${rutaRelativa}assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${sede.id}"></td>
                `;
                    tablaSedesBody.appendChild(row);
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
    const modal = document.getElementById("createSedeModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalActionBtn = document.getElementById("modalActionBtn");
    const sedeForm = document.getElementById("sedeForm");
    let editMode = false;
    let sedeId = null;

    cargarClientes();
    cargarCiudades();
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
            sedeId = event.target.getAttribute("data-id");
            if (!sedeId) {
                console.error("Error: No se encontró el ID de la sede en el botón.");
                return;
            }

            editMode = true;

            // Aquí continúa el código de apertura del modal y carga de datos
            modalTitle.textContent = "Editar sede";
            modalActionBtn.textContent = "Guardar Cambios";

            fetch(`${rutaRelativa}sedes?id=${sedeId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos de la sede.");
                    }
                    return response.json();
                })
                .then((sede) => {
                    console.log(sede);
                    
                    document.getElementById("cliente").value = sede.cliente_id;
                    document.getElementById("ciudad").value = sede.ciudad_id;
                    document.getElementById("direccion").value = sede.direccion;
                    document.getElementById("telefono").value = sede.telefono;
                    document.getElementById("horarioInicio").value = sede.horario_inicio;
                    document.getElementById("horarioFin").value = sede.horario_fin;
                    document.getElementById("estadoToggle").checked = sede.estado === 1;
                    document.querySelector("label[for='estadoToggle']").textContent = sede.estado ? "Activo" : "Inactivo";

                    modal.style.display = "flex"; // Muestra el modal
                })
                .catch((error) => console.error("Error al cargar los datos de la sede:", error));
        }
    });

    // Cerrar modal
    document.getElementById("close").addEventListener("click", function() {
        modal.style.display = "none";
        resetForm();
    });

    // Guardar cambios
    modalActionBtn.addEventListener("click", function() {
        const url = editMode ? `${rutaRelativa}sedes/actualizar/${sedeId}` : `${rutaRelativa}sedes/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(sedeForm);
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
                    console.error("Error al guardar la sede:", error);
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

    function cargarClientes() {
        fetch(`${rutaRelativa}clientes-select`)
            .then(response => response.json())
            .then(clientes => {
                const clienteSelect = document.getElementById("cliente");
                clienteSelect.innerHTML = '<option value="">Seleccione</option>';
                clientes.forEach(cliente => {
                    const option = document.createElement("option");
                    option.value = cliente.id;
                    option.textContent = cliente.nombre;
                    clienteSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los clientes:", error));
    }

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

    function cargarCiudades(paisId, ciudadId = null) {
        fetch(`${rutaRelativa}ciudades?pais=${paisId}`)
            .then(response => response.json())
            .then(ciudades => {
                const ciudadSelect = document.getElementById("ciudad");
                ciudadSelect.innerHTML = '<option value="">Seleccione</option>';
                ciudades.forEach(ciudad => {
                    const option = document.createElement("option");
                    option.value = ciudad.id;
                    option.textContent = ciudad.nombre;
                    if (ciudad.id == ciudadId) option.selected = true;
                    ciudadSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar las ciudades:", error));
    }

    // Escucha los cambios en el select de país
    document.getElementById("pais").addEventListener("change", function() {
        const paisId = this.value;
        cargarCiudades(paisId);
    });

    // Llamar a las funciones para cargar los datos al abrir el modal
    openModalBtn.addEventListener("click", function() {
        cargarClientes();
        cargarPaises()
        
    });

    function resetForm() {
        sedeForm.reset();
        modalTitle.textContent = "Crear nueva sede";
        modalActionBtn.textContent = "Crear Sede";
        editMode = false;
        sedeId = null;
    }

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
