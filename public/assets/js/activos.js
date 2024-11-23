(function() {
    const consultarBtn = document.getElementById("consultarBtn");
    const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
    const tablaActivosBody = document.querySelector("#tablaActivos tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);

    const busquedaInput = document.getElementById("busquedaActivoInput");
    const registrosPorPaginaSelect = document.getElementById("registrosPorPaginaActivo");
    const openModalBtn = document.getElementById("openModalActivoBtn");
    const filtroClasificacion = document.getElementById("filtroClasificacion");
    const filtroEstado = document.getElementById("filtroEstadoActivo");

    function cargarDatos(page = 1) {
        const tablaSeleccionada = tablaMaestraSelect.value;
        const buscar = busquedaInput.value;
        const registrosPorPagina = registrosPorPaginaSelect.value;
        const clasificacion = filtroClasificacion.value;
        const estado = filtroEstado.value;

        if (!tablaSeleccionada) {
            return;
        }

        const formData = new FormData();
        formData.append('tabla', tablaSeleccionada);
        formData.append('buscar', buscar);
        formData.append('registros_por_pagina', registrosPorPagina);
        formData.append('clasificacion', clasificacion);
        formData.append('estado', estado);

        fetch(`../admin/maestras/consultar?page=${page}`, {
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
                tablaActivosBody.innerHTML = '';
                paginacionContainer.innerHTML = '';

                // Llenar la tabla con los datos
                data.data.forEach(activo => {                    
                    const row = document.createElement("tr");
                    const estadoClase = activo.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                    <td>${activo.id}</td>
                    <td>${activo.nombre_elemento}</td> 
                    <td>${activo.estado.nombre}</td>
                    <td>${activo.clasificacion.nombre}</td>
                    <td>${activo.cantidad}</td>
                    <td>${activo.serie}</td>
                    <td>${activo.marca}</td>
                    <td><div class="${estadoClase}">${activo.estado ? 'Activo' : 'Inactivo'}</div></td>
                    <td>${activo.creador?.nombres || 'N/A'}</td>
                    <td>${formatDate(activo.created_at)}</td>
                    <td>${activo.actualizador?.nombres || 'N/A'}</td>
                    <td>${formatDate(activo.updated_at)}</td>
                    <td><img src="../assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${activo.id}"></td>
                `;
                    tablaActivosBody.appendChild(row);
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

    function formatDate(dateString) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    cargarDatos(1);
    cargarClasificaciones();
    cargarEstados();

    // Eventos
    consultarBtn.addEventListener("click", () => cargarDatos(1));
    registrosPorPaginaSelect.addEventListener("change", () => cargarDatos(1));
    busquedaInput.addEventListener("input", () => cargarDatos(1));
    filtroClasificacion.addEventListener("change", () => cargarDatos(1));
    filtroEstado.addEventListener("change", () => cargarDatos(1));

    // Modal functionality
    const modal = document.getElementById("createActivoModal");
    const modalTitle = document.getElementById("modalActivoTitle");
    const modalActionBtn = document.getElementById("modalActivoActionBtn");
    const activoForm = document.getElementById("activoForm");
    let editMode = false;
    let activoId = null;

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
            activoId = event.target.getAttribute("data-id");
            if (!activoId) {
                console.error("Error: No se encontró el ID del activo en el botón.");
                return;
            }

            editMode = true;

            // Aquí continúa el código de apertura del modal y carga de datos
            modalTitle.textContent = "Editar activo";
            modalActionBtn.textContent = "Guardar Cambios";

            fetch(`../activos?id=${activoId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos del activo.");
                    }
                    return response.json();
                })
                .then((activo) => {   
                    document.getElementById("nombreElemento").value = activo.nombre_elemento;
                    document.getElementById("marca").value = activo.marca;
                    document.getElementById("serie").value = activo.serie;
                    document.getElementById("clasificacion").value = activo.clasificacion_id;
                    document.getElementById("cantidad").value = activo.cantidad;
                    document.getElementById("estadoActivo").value = activo.estado_id;
                    document.getElementById("estadoActivoToggle").checked = activo.estado === 1;
                    document.querySelector("label[for='estadoActivoToggle']").textContent = activo.estado ? "Activo" : "Inactivo";

                    modal.style.display = "flex"; // Muestra el modal
                })
                .catch((error) => console.error("Error al cargar los datos del activo:", error));
        }
    });

    // Cerrar modal
    document.getElementById("closeActivo").addEventListener("click", function() {
        modal.style.display = "none";
        resetForm();
    });

    // Guardar cambios
    modalActionBtn.addEventListener("click", function() {
        const url = editMode ? `../activos/actualizar/${activoId}` : `../activos/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(activoForm);
        formData.append("estado", document.getElementById("estadoActivoToggle").checked ? 1 : 0);

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
                    console.error("Error al guardar el activo:", error);
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

    function cargarClasificaciones() {
        fetch(`../clasificaciones`)
            .then(response => response.json())
            .then(clasificaciones => {                
                const clasificacionSelect = document.getElementById("clasificacion");
                clasificacionSelect.innerHTML = '<option value="">Seleccione</option>';
                clasificaciones.forEach(clasificacion => {
                    const option = document.createElement("option");
                    option.value = clasificacion.id;
                    option.textContent = clasificacion.nombre;
                    clasificacionSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar las clasificaciones:", error));
    }

    function cargarEstados() {
        fetch(`../estados`)
            .then(response => response.json())
            .then(estados => {                
                const estadoSelect = document.getElementById("estadoActivo");
                estadoSelect.innerHTML = '<option value="">Seleccione</option>';
                estados.forEach(estado => {
                    const option = document.createElement("option");
                    option.value = estado.id;
                    option.textContent = estado.nombre;
                    estadoSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los estados:", error));
    }

    function resetForm() {
        activoForm.reset();
        modalTitle.textContent = "Crear nuevo activo";
        modalActionBtn.textContent = "Crear Activo";
        editMode = false;
        activoId = null;
    }

    // Actualizar estado del toggle
    const estadoToggle = document.getElementById("estadoActivoToggle");
    const estadoLabel = document.querySelector("label[for='estadoActivoToggle']");

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