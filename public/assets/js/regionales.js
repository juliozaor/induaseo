(function() {
    const consultarBtn = document.getElementById("consultarBtn");
    const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
    const tablaRegionalesBody = document.querySelector("#tablaRegionales tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);

    const busquedaInput = document.getElementById("busquedaInput");
    const registrosPorPaginaSelect = document.getElementById("registrosPorPagina");
    const openModalBtn = document.getElementById("openModalBtn");

    function cargarDatos(page = 1) {
        const buscar = busquedaInput.value;
        const registrosPorPagina = registrosPorPaginaSelect.value;

        fetch(`../regionales?page=${page}&buscar=${buscar}&registros_por_pagina=${registrosPorPagina}`, {
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
                tablaRegionalesBody.innerHTML = '';
                paginacionContainer.innerHTML = '';

                // Llenar la tabla con los datos
                data.forEach(regional => {
                    const row = document.createElement("tr");
                    const estadoClase = regional.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                    <td>${regional.id}</td>
                    <td>${regional.nombre}</td>
                    <td><div class="${estadoClase}">${regional.estado ? 'Activo' : 'Inactivo'}</div></td>
                    <td>${formatDate(regional.updated_at)}</td>
                    <td>${formatDate(regional.created_at)}</td>
                    <td><img src="../assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${regional.id}"></td>
                `;
                    tablaRegionalesBody.appendChild(row);
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

    // Eventos
    consultarBtn.addEventListener("click", () => cargarDatos(1));
    registrosPorPaginaSelect.addEventListener("change", () => cargarDatos(1));
    busquedaInput.addEventListener("input", () => cargarDatos(1));

    // Modal functionality
    const modal = document.getElementById("createRegionalModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalActionBtn = document.getElementById("modalActionBtn");
    const regionalForm = document.getElementById("regionalForm");
    let editMode = false;
    let regionalId = null;

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
            regionalId = event.target.getAttribute("data-id");
            if (!regionalId) {
                console.error("Error: No se encontró el ID de la regional en el botón.");
                return;
            }

            editMode = true;

            // Aquí continúa el código de apertura del modal y carga de datos
            modalTitle.textContent = "Editar regional";
            modalActionBtn.textContent = "Guardar Cambios";

            fetch(`../regional?id=${regionalId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos de la regional.");
                    }
                    return response.json();
                })
                .then((regional) => {                    
                    document.getElementById("nombre").value = regional.nombre;
                    document.getElementById("estadoToggle").checked = regional.estado === 1;
                    document.querySelector("label[for='estadoToggle']").textContent = regional.estado ? "Activo" : "Inactivo";

                    modal.style.display = "flex";

                })
                .catch((error) => console.error("Error al cargar los datos de la regional:", error));
        }
    });

    // Cerrar modal
    document.getElementById("close").addEventListener("click", function() {
        modal.style.display = "none";
        resetForm();
    });

    // Guardar cambios
    modalActionBtn.addEventListener("click", function() {
        const url = editMode ? `../regionales/actualizar/${regionalId}` : `../regionales/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(regionalForm);
        formData.append("estado", document.getElementById("estadoToggle").checked ? 1 : 0);

        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        

        fetch(url, {
                method: 'POST',
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
                    cargarDatos(1);
                }
            })
            .catch((error) => {
                if (error.errors) {
                    showErrors(error.errors);
                } else {
                    console.error("Error al guardar la regional:", error);
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

    function resetForm() {
        regionalForm.reset();
        modalTitle.textContent = "Crear nueva regional";
        modalActionBtn.textContent = "Crear Regional";
        editMode = false;
        regionalId = null;
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
