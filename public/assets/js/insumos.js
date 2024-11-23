(function() {
    const consultarBtn = document.getElementById("consultarBtn");
    const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
    const tablaInsumosBody = document.querySelector("#tablaInsumos tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);

    const busquedaInput = document.getElementById("busquedaInsumoInput");
    const registrosPorPaginaSelect = document.getElementById("registrosPorPaginaInsumo");
    const openModalBtn = document.getElementById("openModalInsumoBtn");
    const filtroClasificacion = document.getElementById("filtroClasificacionInsumo");
    const filtroEstado = document.getElementById("filtroEstadoInsumo");

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
                tablaInsumosBody.innerHTML = '';
                paginacionContainer.innerHTML = '';

                // Llenar la tabla con los datos
                data.data.forEach(insumo => {
                    const row = document.createElement("tr");
                    const estadoClase = insumo.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                    <td>${insumo.id}</td>
                    <td>${insumo.nombre_elemento}</td>
                    <td>${insumo.estado.nombre}</td>
                    <td>${insumo.marca}</td>
                    <td>${insumo.codigo}</td>
                    <td>${insumo.clasificacion.nombre}</td>
                    <td>${insumo.cantidad}</td>
                    <td><div class="${estadoClase}">${insumo.estado ? 'Activo' : 'Inactivo'}</div></td>
                    <td>${insumo.proveedor}</td>
                    <td>${insumo.telefono_proveedor}</td>
                    <td>${insumo.creador?.nombres || 'N/A'}</td>
                    <td>${formatDate(insumo.created_at)}</td>
                    <td>${insumo.actualizador?.nombres || 'N/A'}</td>
                    <td>${formatDate(insumo.updated_at)}</td>
                    <td><img src="../assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${insumo.id}"></td>
                `;
                    tablaInsumosBody.appendChild(row);
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
    const modal = document.getElementById("createInsumoModal");
    const modalTitle = document.getElementById("modalInsumoTitle");
    const modalActionBtn = document.getElementById("modalInsumoActionBtn");
    const insumoForm = document.getElementById("insumoForm");
    let editMode = false;
    let insumoId = null;

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
            insumoId = event.target.getAttribute("data-id");
            if (!insumoId) {
                console.error("Error: No se encontró el ID del insumo en el botón.");
                return;
            }

            editMode = true;

            // Aquí continúa el código de apertura del modal y carga de datos
            modalTitle.textContent = "Editar insumo";
            modalActionBtn.textContent = "Guardar Cambios";

            fetch(`../insumos?id=${insumoId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos del insumo.");
                    }
                    return response.json();
                })
                .then((insumo) => {   
                    document.getElementById("nombreInsumo").value = insumo.nombre_elemento;
                    document.getElementById("marcaInsumo").value = insumo.marca;
                    document.getElementById("codigoInsumo").value = insumo.codigo;
                    document.getElementById("clasificacionInsumo").value = insumo.clasificacion_id;
                    document.getElementById("cantidadInsumo").value = insumo.cantidad;
                    document.getElementById("estadoInsumo").value = insumo.estado_id;
                    document.getElementById("proveedorInsumo").value = insumo.proveedor;
                    document.getElementById("telefonoProveedorInsumo").value = insumo.telefono_proveedor;
                    document.getElementById("estadoInsumoToggle").checked = insumo.estado === 1;
                    document.querySelector("label[for='estadoInsumoToggle']").textContent = insumo.estado ? "Activo" : "Inactivo";

                    modal.style.display = "flex"; // Muestra el modal
                })
                .catch((error) => console.error("Error al cargar los datos del insumo:", error));
        }
    });

    // Cerrar modal
    document.getElementById("closeInsumo").addEventListener("click", function() {
        modal.style.display = "none";
        resetForm();
    });

    // Guardar cambios
    modalActionBtn.addEventListener("click", function() {
        const url = editMode ? `../insumos/actualizar/${insumoId}` : `../insumos/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(insumoForm);
        formData.append("estado", document.getElementById("estadoInsumoToggle").checked ? 1 : 0);

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
                    console.error("Error al guardar el insumo:", error);
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
                const clasificacionSelect = document.getElementById("clasificacionInsumo");
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
                const estadoSelect = document.getElementById("estadoInsumo");
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
        insumoForm.reset();
        modalTitle.textContent = "Crear nuevo insumo";
        modalActionBtn.textContent = "Crear Insumo";
        editMode = false;
        insumoId = null;
    }

    // Actualizar estado del toggle
    const estadoToggle = document.getElementById("estadoInsumoToggle");
    const estadoLabel = document.querySelector("label[for='estadoInsumoToggle']");

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