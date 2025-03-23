(function() {
    const consultarBtn = document.getElementById("consultarBtn");
    const openModalBtn = document.getElementById("openModalBtn");
    const modal = document.getElementById("createActividadModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalActionBtn = document.getElementById("modalActionBtn");
    const actividadForm = document.getElementById("actividadForm");
    const tablaActividadesBody = document.querySelector("#tablaActividades tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);

    const busquedaInput = document.getElementById("busquedaInput");
    const registrosPorPaginaSelect = document.getElementById("registrosPorPagina");
    let editMode = false;
    let actividadId = null;

    const frecuenciaSelect = document.getElementById("frecuencia");

    let frecuenciaArr = [];

    function cargarFrecuencias() {
        fetch(`../frecuencias`)
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

    function cargarDatos(page = 1) {
        resetForm();
        const buscar = busquedaInput.value;
        const registrosPorPagina = registrosPorPaginaSelect.value;

        fetch(`../actividades?page=${page}&buscar=${buscar}&registros_por_pagina=${registrosPorPagina}`, {
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
                console.log(data);
                // Limpiar la tabla y la paginación
                tablaActividadesBody.innerHTML = '';
                paginacionContainer.innerHTML = '';

                // Llenar la tabla con los datos
                data.data.forEach(actividad => {
                    const row = document.createElement("tr");
                    const estadoClase = actividad.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                    <td>${actividad.id}</td>
                    <td>${actividad.nombre}</td>
                    <td>${actividad.frecuencia.nombre}</td>
                    <td><div class="${estadoClase}">${actividad.estado ? 'Activo' : 'Inactivo'}</div></td>
                    <td>
                        <img src="../assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${actividad.id}">
                        <img src="../assets/icons/eliminar.png" alt="Eliminar" class="icono-eliminar" data-id="${actividad.id}">
                    </td>
                `;
                    tablaActividadesBody.appendChild(row);
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
            actividadId = event.target.getAttribute("data-id");
            if (!actividadId) {
                console.error("Error: No se encontró el ID de la actividad en el botón.");
                return;
            }

            editMode = true;

            // Aquí continúa el código de apertura del modal y carga de datos
            modalTitle.textContent = "Editar actividad";
            modalActionBtn.textContent = "Guardar Cambios";

            fetch(`../actividad?id=${actividadId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos de la actividad.");
                    }
                    return response.json();
                })
                .then((actividad) => {
                    document.getElementById("nombre").value = actividad.nombre;
                    document.getElementById("frecuencia").value = actividad.frecuencia_id; // Corregido
                    document.getElementById("estadoToggle").checked = actividad.estado === 1;
                    document.querySelector("label[for='estadoToggle']").textContent = actividad.estado ? "Activo" : "Inactivo";

                    modal.style.display = "flex";

                })
                .catch((error) => console.error("Error al cargar los datos de la actividad:", error));
        }

        if (event.target.classList.contains("icono-eliminar")) {
            const actividadId = event.target.getAttribute("data-id");
            if (!actividadId) {
                console.error("Error: No se encontró el ID de la actividad en el botón.");
                return;
            }

            if (confirm("¿Estás seguro de que deseas eliminar esta actividad?")) {
                fetch(`../actividades/${actividadId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error al eliminar la actividad.");
                    }
                    return response.json();
                })
                .then(data => {
                    showAlertModal(
                        "ok.png", // Ruta del ícono de éxito
                        data.message // Mensaje de éxito
                    );
                    cargarDatos(1)
                })
                .catch(error => console.error("Error al eliminar la actividad:", error));
            }
        }
    });

    // Cerrar modal
    document.getElementById("close").addEventListener("click", function() {
        modal.style.display = "none";
        resetForm();
    });

    // Guardar cambios
    modalActionBtn.addEventListener("click", function() {
        const nombre = document.getElementById("nombre").value.trim();
        const urlVerificar = `../actividades/verificar-nombre?nombre=${encodeURIComponent(nombre)}&id=${editMode ? actividadId : ''}`;

        fetch(urlVerificar, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.exists, actividadId)
            if (data.exists) {
                document.getElementById("errorNombreDuplicado").textContent = "La actividad que intenta ingresar ya existe.";
            } else {
                guardarActividad();
            }
        })
        .catch(error => console.error('Error:', error));
    });

    function guardarActividad(){
        const url = editMode ? `../actividades/actualizar/${actividadId}` : `../actividades/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(actividadForm);
        formData.append("estado", document.getElementById("estadoToggle").checked ? 1 : 0);

        fetch(url, {
                method: "POST",
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
                    cargarDatos(1)

                    /* location.reload(); */
                }
            })
            .catch((error) => {
                if (error.errors) {
                    showErrors(error.errors);
                } else {
                    console.error("Error al guardar la actividad:", error);
                }
            });
    }

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
        actividadForm.reset();
        modalTitle.textContent = "Crear nueva actividad";
        modalActionBtn.textContent = "Crear Actividad";
        editMode = false;
        actividadId = null;
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
