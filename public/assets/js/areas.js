(function () {
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
    const clienteFiltro = document.getElementById("clienteFiltro");
    const sedeFiltro = document.getElementById("sedeFiltro");
    const estadoFiltro = document.getElementById("estadoFiltro");
    const actividadSection = document.getElementById("actividadSection");
    const nuevaActividadInput = document.getElementById("nuevaActividad");
    const agregarActividadBtn = document.getElementById("agregarActividadBtn");
    const tablaActividadesBody = document.querySelector("#tablaActividades tbody");
    const descripcionActividadInput = document.getElementById("descripcionActividad");

    const actividadSelect = document.getElementById("actividadSelect");

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

    function cargarClientesFiltro() {
        fetch(`../clientes-select`)
            .then(response => response.json())
            .then(clientes => {
                clienteFiltro.innerHTML = '<option value="">Todos los clientes</option>';
                clientes.forEach(cliente => {
                    const option = document.createElement("option");
                    option.value = cliente.id;
                    option.textContent = cliente.nombre;
                    clienteFiltro.appendChild(option);
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

    function cargarSedesFiltro(clienteId) {
        fetch(`../sedes?cliente_id=${clienteId}`)
            .then(response => response.json())
            .then(sedes => {
                sedeFiltro.innerHTML = '<option value="">Todas las sedes</option>';
                sedes.forEach(sede => {
                    const option = document.createElement("option");
                    option.value = sede.id;
                    option.textContent = sede.nombre;
                    sedeFiltro.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar sedes:', error));
    }

    function cargarActividades(areaId) {
        fetch(`../actividades/${areaId}`)
            .then(response => response.json())
            .then(data => {
                tablaActividadesBody.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(actividad => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${actividad.id}</td>
                            <td>${actividad.nombre}</td>
                            <td><button class="btn-eliminar" data-id="${actividad.id}">Eliminar</button></td>
                        `;
                        tablaActividadesBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td colspan="3">No se encontraron actividades.</td>
                    `;
                    tablaActividadesBody.appendChild(row);
                }
            })
            .catch(error => {
                console.error('Error al cargar actividades:', error);
                tablaActividadesBody.innerHTML = `
                    <tr>
                        <td colspan="3">No se encontraron actividades.</td>
                    </tr>
                `;
            });
    }

    function cargarActividadesSelect() {
        fetch(`../actividades`)
            .then(response => response.json())
            .then(data => {
                console.log(data.data);
                actividadSelect.innerHTML = '<option value="">Seleccione una actividad</option>';
                data.data.forEach(actividad => {
                    const option = document.createElement("option");
                    option.value = actividad.id;
                    option.textContent = actividad.nombre;
                    actividadSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar actividades:', error));
    }

    /* function agregarActividad(areaId, nombreActividad, descripcionActividad) {
        fetch(`../guardar-actividad`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ area_id: areaId, nombre: nombreActividad, descripcion: descripcionActividad })
        })
        .then(response => response.json())
        .then(data => {
            cargarActividades(areaId);
            nuevaActividadInput.value = '';
            descripcionActividadInput.value = '';
        })
        .catch(error => console.error('Error al agregar actividad:', error));
    } */

    function agregarActividad(areaId, actividadId) {
        fetch(`../areas_actividades`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ area_id: areaId, actividad_id: actividadId })
        })
            .then(response => response.json())
            .then(data => {
                cargarActividades(areaId);
                actividadSelect.value = '';
            })
            .catch(error => console.error('Error al agregar actividad:', error));
    }

    function eliminarActividad(id) {
        fetch(`../actividades/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                cargarActividades(areaId);
            })
            .catch(error => console.error('Error al eliminar actividad:', error));
    }

    function cargarDatos(page = 1) {
        const buscar = busquedaInput.value;
        const registrosPorPagina = registrosPorPaginaSelect.value;
        const clienteId = clienteFiltro.value;
        const sedeId = sedeFiltro.value;
        const estado = estadoFiltro.value;

        fetch(`../areas?page=${page}&buscar=${buscar}&registros_por_pagina=${registrosPorPagina}&cliente_id=${clienteId}&sede_id=${sedeId}&estado=${estado}`, {
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
                    <td>${area.actividades.length}</td>
                    <td><div class="${estadoClase}">${area.estado ? 'Activo' : 'Inactivo'}</div></td>
                    <td>${formatDate(area.updated_at)}</td>
                    <td>${area.actualizador?.nombres || 'N/A'}</td>
                    <td>${area.creador?.nombres || 'N/A'}</td>
                    <td>${formatDate(area.created_at)}</td>
                    <td>
                        <img src="../assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${area.id}">
                        <img src="../assets/icons/eliminar.png" alt="Eliminar" class="icono-eliminar" data-id="${area.id}" data-tipo="area">
                    </td>
                `;
                    tablaAreasBody.appendChild(row);
                });
                console.log(data);


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
    cargarClientesFiltro();
    cargarSedesFiltro();

    // Eventos
    consultarBtn.addEventListener("click", () => cargarDatos(1));
    registrosPorPaginaSelect.addEventListener("change", () => cargarDatos(1));
    busquedaInput.addEventListener("input", () => cargarDatos(1));
    clienteFiltro.addEventListener("change", () => {
        const clienteId = clienteFiltro.value;
        sedeFiltro.innerHTML = '<option value="">Todas las sedes</option>'; // Reiniciar el valor del select sedeFiltro
        cargarSedesFiltro(clienteId);
        cargarDatos(1);
    });
    sedeFiltro.addEventListener("change", () => cargarDatos(1));
    estadoFiltro.addEventListener("change", () => cargarDatos(1));

    // Modal functionality
    const modal = document.getElementById("createAreaModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalActionBtn = document.getElementById("modalActionBtn");
    const areaForm = document.getElementById("areaForm");
    let editMode = false;
    let areaId = null;

    // Abrir el modal
    openModalBtn.addEventListener("click", function () {
        modal.style.display = "flex";
        actividadSection.style.display = "none";
        resetForm();
        cargarActividadesSelect();
    });

    // Cerrar el modal al hacer clic fuera de él
    window.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
            resetForm();
        }
    });

    document.addEventListener("click", function (event) {
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
                    document.getElementById("nombre").value = area.nombre;
                    document.getElementById("cliente").value = area.sede.cliente.id;
                    cargarSedes(area.sede.cliente.id).then(() => {
                        document.getElementById("sede").value = area.sede_id;
                    });
                    document.getElementById("estadoToggle").checked = area.estado === 1;
                    document.querySelector("label[for='estadoToggle']").textContent = area.estado ? "Activo" : "Inactivo";

                    document.getElementById("nombre").disabled = false; // Enable the input for the area name
                    modal.style.display = "flex";

                    // Store the original name to exclude it from the verification
                    document.getElementById("nombre").setAttribute("data-original-name", area.nombre);

                    // Make actividadSection visible and load activities
                    actividadSection.style.display = "block";
                    cargarActividades(area.id);
                    cargarActividadesSelect(); // Cargar actividades en el select
                })
                .catch((error) => console.error("Error al cargar los datos del área:", error));
        }
    });

    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("icono-eliminar")) {
            const areaId = event.target.getAttribute("data-id");
            const tipo = event.target.getAttribute("data-tipo");
            if (areaId) {
                if (tipo === "area") {
                    Swal.fire({
                        title: '¿Estás seguro de que deseas eliminar esta área?',
                        /* text: "No podrás revertir esto.", */
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            eliminarArea(areaId);
                        }
                    });
                }

            }
        }
    });

    // Cerrar modal
    document.getElementById("close").addEventListener("click", function () {
        modal.style.display = "none";
        resetForm();
    });

    // Guardar cambios
    modalActionBtn.addEventListener("click", function () {
        const url = editMode ? `../areas/actualizar/${areaId}` : `../areas/guardar`;
        const method = editMode ? "PUT" : "POST";
        const nombreExistente = document.getElementById("nombreExistente");

        if (nombreExistente.style.display === "block") {
            showAlertModal(
                "error.png", // Ruta del ícono de error
                "El nombre del área ya existe" // Mensaje de error
            );
            return; // Do not proceed if the area name already exists
        }

        const formData = new FormData(areaForm);
        formData.append("estado", document.getElementById("estadoToggle").checked ? 1 : 0);

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

                    if (!editMode) {
                        areaId = data.area.id; // Establecer el areaId para el área recién creada
                    }
                    //actividadSection.style.display = "block";  Enable actividadSection
                    modal.style.display = "none";
                    cargarDatos(1);
                    resetForm();
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

    clienteSelect.addEventListener("change", function () {
        const clienteId = parseInt(clienteSelect.value);
        cargarSedes(clienteId);
    });

    // Deshabilita el input nombre si no se ha seleccionado una sede
    document.getElementById("sede").addEventListener("change", function () {
        const sedeId = this.value;
        const nombreInput = document.getElementById("nombre");

        if (sedeId) {
            nombreInput.disabled = false;
        } else {
            nombreInput.disabled = true;
            nombreInput.value = '';
            document.getElementById("nombreExistente").style.display = "none";
        }
    });

    // Agregar actividad
    agregarActividadBtn.addEventListener("click", function () {
        const actividadId = actividadSelect.value;
        if (actividadId && areaId) {
            fetch(`../actividades/${areaId}`)
                .then(response => response.json())
                .then(data => {
                    const actividadExistente = data.find(actividad => actividad.id == actividadId);
                    if (actividadExistente) {
                        showAlertModal(
                            "error.png", // Ruta del ícono de error
                            "La actividad ya está asociada a esta área" // Mensaje de error
                        );
                    } else {
                        agregarActividad(areaId, actividadId);
                    }
                })
                .catch(error => console.error('Error al verificar la actividad:', error));
        } else {
            showAlertModal(
                "error.png", // Ruta del ícono de error
                "Seleccione una actividad" // Mensaje de error
            );
        }
    });

    // Eliminar actividad
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("btn-eliminar")) {
            const actividadId = event.target.getAttribute("data-id");
            if (actividadId) {
                eliminarActividad(actividadId);
            }
        }
    });

    // Verificar si el nombre del área ya existe en la sede seleccionada
    document.getElementById("nombre").addEventListener("input", function () {
        const nombre = this.value;
        const sedeId = document.getElementById("sede").value;
        const nombreExistente = document.getElementById("nombreExistente");
        const modalActionBtn = document.getElementById("modalActionBtn");
        const originalName = this.getAttribute("data-original-name");

        if (nombre && sedeId) {
            fetch(`../areas/verificar-nombre?nombre=${nombre}&sede_id=${sedeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists && nombre !== originalName) {
                        nombreExistente.style.display = "block";
                        /* modalActionBtn.disabled = true; */
                    } else {
                        nombreExistente.style.display = "none";
                        /* modalActionBtn.disabled = false; */
                    }
                })
                .catch(error => console.error('Error al verificar el nombre del área:', error));
        } else {
            nombreExistente.style.display = "none";
            /* modalActionBtn.disabled = true; */
        }
    });

    // Mostrar mensaje de éxito o error
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

    // resetea el formulario y cierra el modal
    function resetForm() {
        areaForm.reset();
        modalTitle.textContent = "Crear nueva área";
        modalActionBtn.textContent = "Crear Área";
        document.getElementById("nombre").disabled = true;
        document.getElementById("nombreExistente").style.display = "none";
        /* modalActionBtn.disabled = true; */
        editMode = false;
        areaId = null;
        tablaActividadesBody.innerHTML = ''; // Clear the activities table
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
    // Guardar y finalizar (cerrar modal)
    guardarFinalizarBtn.addEventListener("click", function () {
        modal.style.display = "none";
        cargarDatos(1);
        resetForm();
        showAlertModal(
            "ok.png", // Ruta del ícono de éxito
            "Guardado con éxito" // Mensaje de éxito
        );
    });

    // Mostrar mensaje de éxito o error
    function eliminarArea(id) {
        fetch(`../areas/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al eliminar el área.');
                }
                return response.text(); // Use text() instead of json() for empty responses
            })
            .then(() => {
                cargarDatos(1);
                showAlertModal(
                    "ok.png", // Ruta del ícono de éxito
                    "Área eliminada con éxito" // Mensaje de éxito
                );
            })
            .catch(error => console.error('Error al eliminar el área:', error));
    }
})();
