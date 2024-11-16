(function() {
    const rutaRelativa = '/induaseo/public/';
    const consultarBtn = document.getElementById("consultarBtn");
    const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
    const tablaTurnosBody = document.querySelector("#tablaTurnos tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);

    const busquedaInput = document.getElementById("busquedaInput");
    const registrosPorPaginaSelect = document.getElementById("registrosPorPagina");
    const openModalBtn = document.getElementById("openModalBtn");

    const frecuenciaSelect = document.getElementById("frecuencia");
    const detalleFrecuenciaInput = document.getElementById("detalleFrecuencia");
    const actividadSection = document.getElementById("actividadSection");
    const nuevaActividadInput = document.getElementById("nuevaActividad");
    const agregarActividadBtn = document.getElementById("agregarActividadBtn");
    const tablaActividadesBody = document.querySelector("#tablaActividades tbody");
    const descripcionActividadInput = document.getElementById("descripcionActividad");

    let frecuenciaArr = [];

    function cargarFrecuencias() {
        fetch(`${rutaRelativa}frecuencias`)
            .then(response => response.json())
            .then(frecuencias => {
                frecuenciaArr = frecuencias;
                
                frecuenciaSelect.innerHTML = '<option value="">Seleccione</option>';
                frecuencias.forEach(frecuencia => {
                    const option = document.createElement("option");
                    option.value = frecuencia.id;
                    option.textContent = frecuencia.nombre;
                    frecuenciaSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar frecuencias:', error));
    }

    function cargarActividades(turnoId) {
        
        fetch(`${rutaRelativa}actividades/${turnoId}`)
            .then(response => response.json())
            .then(data => {                
                tablaActividadesBody.innerHTML = '';
                if (data.length > 0) {                    
                    data.forEach(actividad => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${actividad.id}</td>
                            <td>${actividad.nombre}</td>
                            <td>${actividad.descripcion}</td>
                            <td><button class="btn-eliminar" data-id="${actividad.id}">Eliminar</button></td>
                        `;                        
                        tablaActividadesBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td colspan="4">No se encontraron actividades.</td>
                    `;
                    tablaActividadesBody.appendChild(row);
                }
            })
            .catch(error => {
                console.error('Error al cargar actividades:', error);
                tablaActividadesBody.innerHTML = `
                    <tr>
                        <td colspan="4">No se encontraron actividades.</td>
                    </tr>
                `;
            });
    }

    function agregarActividad(turnoId, nombreActividad, descripcionActividad) {
        fetch(`${rutaRelativa}actividades`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ turno_id: turnoId, nombre: nombreActividad, descripcion: descripcionActividad })
        })
        .then(response => response.json())
        .then(data => {
            cargarActividades(turnoId);
            nuevaActividadInput.value = '';
            descripcionActividadInput.value = '';
        })
        .catch(error => console.error('Error al agregar actividad:', error));
    }

    function eliminarActividad(id) {
        fetch(`${rutaRelativa}actividades/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            cargarActividades(turnoId);
        })
        .catch(error => console.error('Error al eliminar actividad:', error));
    }

    function cargarDatos(page = 1) {
        const buscar = busquedaInput.value;
        const registrosPorPagina = registrosPorPaginaSelect.value;

        fetch(`${rutaRelativa}turnos?page=${page}&buscar=${buscar}&registros_por_pagina=${registrosPorPagina}`, {
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
                tablaTurnosBody.innerHTML = '';
                paginacionContainer.innerHTML = '';

                // Llenar la tabla con los datos
                data.data.forEach(turno => {
                    const row = document.createElement("tr");
                    const estadoClase = turno.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                    <td>${turno.id}</td>
                    <td>${turno.nombre}</td>
                    <td>${turno.frecuencia.nombre}</td>
                    <td>${turno.actividades.length}</td>
                    <td><div class="${estadoClase}">${turno.estado ? 'Activo' : 'Inactivo'}</div></td>
                    <td>${formatDate(turno.updated_at)}</td>
                    <td>${turno.actualizador?.nombres || 'N/A'}</td>
                    <td>${turno.creador?.nombres || 'N/A'}</td>
                    <td>${formatDate(turno.created_at)}</td>
                    <td><img src="${rutaRelativa}assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${turno.id}"></td>
                `;
                    tablaTurnosBody.appendChild(row);
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
    cargarFrecuencias();

    // Eventos
    consultarBtn.addEventListener("click", () => cargarDatos(1));
    registrosPorPaginaSelect.addEventListener("change", () => cargarDatos(1));
    busquedaInput.addEventListener("input", () => cargarDatos(1));

    // Modal functionality
    const modal = document.getElementById("createTurnoModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalActionBtn = document.getElementById("modalActionBtn");
    const turnoForm = document.getElementById("turnoForm");
    let editMode = false;
    let turnoId = null;

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
            turnoId = event.target.getAttribute("data-id");
            if (!turnoId) {
                console.error("Error: No se encontró el ID del turno en el botón.");
                return;
            }

            editMode = true;

            // Aquí continúa el código de apertura del modal y carga de datos
            modalTitle.textContent = "Editar turno";
            modalActionBtn.textContent = "Guardar Cambios";

            fetch(`${rutaRelativa}turno?id=${turnoId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos del turno.");
                    }
                    return response.json();
                })
                .then((turno) => {
                    
                    document.getElementById("nombre").value = turno.nombre;
                    document.getElementById("frecuencia").value = turno.frecuencia_id;
                    document.getElementById("detalleFrecuencia").value = turno.frecuencia_cantidad;
                    document.getElementById("estadoToggle").checked = turno.estado === 1;
                    document.querySelector("label[for='estadoToggle']").textContent = turno.estado ? "Activo" : "Inactivo";

                    actividadSection.style.display = "block";
                    cargarActividades(turno.id);

                    modal.style.display = "flex"; // Muestra el modal
                })
                .catch((error) => console.error("Error al cargar los datos del turno:", error));
        }
    });

    // Cerrar modal
    document.getElementById("close").addEventListener("click", function() {
        modal.style.display = "none";
        resetForm();
    });

    // Guardar cambios
    modalActionBtn.addEventListener("click", function() {
        const url = editMode ? `${rutaRelativa}turnos/actualizar/${turnoId}` : `${rutaRelativa}turnos/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(turnoForm);
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
                    actividadSection.style.display = "block";
                    turnoId = data.turno.id; // Set the turnoId to the newly created or updated turno ID
                    cargarActividades(turnoId);
                }
            })
            .catch((error) => {
                if (error.errors) {
                    showErrors(error.errors);
                } else {
                    console.error("Error al guardar el turno:", error);
                }
            });
    });

    
        // Escucha los cambios en el select de frecuencia
        frecuenciaSelect.addEventListener("change", function() {
            const frecuenciaId = parseInt(frecuenciaSelect.value);
            const frecuencia = frecuenciaArr.find(f => f.id === frecuenciaId);

            if (frecuencia) {
                detalleFrecuenciaInput.value = frecuencia.detalle;
            }
        });

    agregarActividadBtn.addEventListener("click", function() {
        const nombreActividad = nuevaActividadInput.value;
        const descripcionActividad = descripcionActividadInput.value;
        if (nombreActividad && descripcionActividad && turnoId) {
            agregarActividad(turnoId, nombreActividad, descripcionActividad);
        }
    });

    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("btn-eliminar")) {
            const actividadId = event.target.getAttribute("data-id");
            if (actividadId) {
                eliminarActividad(actividadId);
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
        turnoForm.reset();
        modalTitle.textContent = "Crear nuevo turno";
        modalActionBtn.textContent = "Crear Turno";
        editMode = false;
        turnoId = null;
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
