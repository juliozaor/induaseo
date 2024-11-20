(function() {
    const consultarBtn = document.getElementById("consultarBtn");
    const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
    const tablaAreasBody = document.querySelector("#tablaAreas tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);

    const busquedaInput = document.getElementById("busquedaInput");
    const registrosPorPaginaSelect = document.getElementById("registrosPorPagina");
    const openModalBtn = document.getElementById("openModalBtn");

    const clienteSelect = document.getElementById("cliente");
    const sedeSelect = document.getElementById("sede");
    const tareaSection = document.getElementById("tareaSection");
    const nuevaTareaInput = document.getElementById("nuevaTarea");
    const agregarTareaBtn = document.getElementById("agregarTareaBtn");
    const tablaTareasBody = document.querySelector("#tablaTareas tbody");
    const descripcionTareaInput = document.getElementById("descripcionTarea");

    let clienteArr = [];
    let sedeArr = [];

    function cargarClientes() {
        fetch(`../clientes-select`)
            .then(response => response.json())
            .then(clientes => {
                clienteArr = clientes;
                
                clienteSelect.innerHTML = '<option value="">Seleccione</option>';
                clientes.forEach(cliente => {
                    const option = document.createElement("option");
                    option.value = cliente.id;
                    option.textContent = cliente.nombre;
                    clienteSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar clientes:', error));
    }

    function cargarSedes(clienteId) {
        return fetch(`../sedes?cliente_id=${clienteId}`)
            .then(response => response.json())
            .then(sedes => {
                sedeArr = sedes;
                
                sedeSelect.innerHTML = '<option value="">Seleccione</option>';
                sedes.forEach(sede => {
                    const option = document.createElement("option");
                    option.value = sede.id;
                    option.textContent = sede.nombre;
                    sedeSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar sedes:', error));
    }

    function cargarTareas(areaId) {
        fetch(`../tareas/${areaId}`)
            .then(response => response.json())
            .then(data => {                
                tablaTareasBody.innerHTML = '';
                if (data.length > 0) {                    
                    data.forEach(tarea => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${tarea.id}</td>
                            <td>${tarea.nombre}</td>
                            <td>${tarea.descripcion}</td>
                            <td><button class="btn-eliminar" data-id="${tarea.id}">Eliminar</button></td>
                        `;                        
                        tablaTareasBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td colspan="4">No se encontraron tareas.</td>
                    `;
                    tablaTareasBody.appendChild(row);
                }
            })
            .catch(error => {
                console.error('Error al cargar tareas:', error);
                tablaTareasBody.innerHTML = `
                    <tr>
                        <td colspan="4">No se encontraron tareas.</td>
                    </tr>
                `;
            });
    }

    function agregarTarea(areaId, nombreTarea, descripcionTarea) {
        fetch(`../tareas`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ area_id: areaId, nombre: nombreTarea, descripcion: descripcionTarea })
        })
        .then(response => response.json())
        .then(data => {
            cargarTareas(areaId);
            nuevaTareaInput.value = '';
            descripcionTareaInput.value = '';
        })
        .catch(error => console.error('Error al agregar tarea:', error));
    }

    function eliminarTarea(id) {
        fetch(`../tareas/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            cargarTareas(areaId);
        })
        .catch(error => console.error('Error al eliminar tarea:', error));
    }

    function cargarDatos(page = 1) {
        const buscar = busquedaInput.value;
        const registrosPorPagina = registrosPorPaginaSelect.value;

        fetch(`../areas?page=${page}&buscar=${buscar}&registros_por_pagina=${registrosPorPagina}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`Error en la solicitud: ${response.statusText}`);
                return response.json();
            })
            .then(data => {
                // Limpiar la tabla y la paginación
                tablaAreasBody.innerHTML = '';
                paginacionContainer.innerHTML = '';

                // Llenar la tabla con los datos
                data.data.forEach(area => {
                    const row = document.createElement("tr");
                    const estadoClase = area.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                    <td>${area.id}</td>
                    <td>${area.nombre}</td>
                    <td>${area.sede.cliente.nombre}</td>
                    <td>${area.sede.nombre}</td>
                    <td><div class="${estadoClase}">${area.estado ? 'Activo' : 'Inactivo'}</div></td>
                    <td>${formatDate(area.updated_at)}</td>
                    <td>${area.actualizador?.nombres || 'N/A'}</td>
                    <td>${area.creador?.nombres || 'N/A'}</td>
                    <td>${formatDate(area.created_at)}</td>
                    <td><img src="../assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${area.id}"></td>
                `;
                    tablaAreasBody.appendChild(row);
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
    cargarClientes();

    // Eventos
    consultarBtn.addEventListener("click", () => cargarDatos(1));
    registrosPorPaginaSelect.addEventListener("change", () => cargarDatos(1));
    busquedaInput.addEventListener("input", () => cargarDatos(1));

    // Modal functionality
    const modal = document.getElementById("createAreaModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalActionBtn = document.getElementById("modalActionBtn");
    const areaForm = document.getElementById("areaForm");
    let editMode = false;
    let areaId = null;

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
            areaId = event.target.getAttribute("data-id");
            if (!areaId) {
                console.error("Error: No se encontró el ID del área en el botón.");
                return;
            }

            editMode = true;

            // Aquí continúa el código de apertura del modal y carga de datos
            modalTitle.textContent = "Editar área";
            modalActionBtn.textContent = "Guardar Cambios";

            fetch(`../area?id=${areaId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos del área.");
                    }
                    return response.json();
                })
                .then((area) => {
                    console.log({area});
                    
                    document.getElementById("nombre").value = area.nombre;
                    document.getElementById("cliente").value = area.sede.cliente.id;
                    cargarSedes(area.sede.cliente.id).then(() => {
                        document.getElementById("sede").value = area.sede_id;
                    });
                    document.getElementById("estadoToggle").checked = area.estado === 1;
                    document.querySelector("label[for='estadoToggle']").textContent = area.estado ? "Activo" : "Inactivo";

                    tareaSection.style.display = "block";
                    cargarTareas(area.id);

                    modal.style.display = "flex"; // Muestra el modal
                })
                .catch((error) => console.error("Error al cargar los datos del área:", error));
        }
    });

    // Cerrar modal
    document.getElementById("close").addEventListener("click", function() {
        modal.style.display = "none";
        resetForm();
    });

    // Guardar cambios
    modalActionBtn.addEventListener("click", function() {
        const url = editMode ? `../areas/actualizar/${areaId}` : `../areas/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(areaForm);
        formData.append("estado", document.getElementById("estadoToggle").checked ? 1 : 0);

        fetch(url, {
                method: method,
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
                    tareaSection.style.display = "block";
                    areaId = data.area.id; // Set the areaId to the newly created or updated area ID
                    cargarTareas(areaId);
                }
            })
            .catch((error) => {
                if (error.errors) {
                    showErrors(error.errors);
                } else {
                    console.error("Error al guardar el área:", error);
                }
            });
    });

    clienteSelect.addEventListener("change", function() {
        const clienteId = parseInt(clienteSelect.value);
        cargarSedes(clienteId);
    });

    agregarTareaBtn.addEventListener("click", function() {
        const nombreTarea = nuevaTareaInput.value;
        const descripcionTarea = descripcionTareaInput.value;
        if (nombreTarea && descripcionTarea && areaId) {
            agregarTarea(areaId, nombreTarea, descripcionTarea);
        }
    });

    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("btn-eliminar")) {
            const tareaId = event.target.getAttribute("data-id");
            if (tareaId) {
                eliminarTarea(tareaId);
            }
        }
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

    function resetForm() {
        areaForm.reset();
        modalTitle.textContent = "Crear nueva área";
        modalActionBtn.textContent = "Crear Área";
        editMode = false;
        areaId = null;
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

    const guardarFinalizarBtn = document.getElementById("guardarFinalizarBtn");

    guardarFinalizarBtn.addEventListener("click", function() {
        modal.style.display = "none";
        cargarDatos(1);
        resetForm();
        showAlertModal(
            "ok.png", // Ruta del ícono de éxito
            "Guardado con éxito" // Mensaje de éxito
        );
    });
})();
