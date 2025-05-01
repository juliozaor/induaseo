(function () {

    const clienteSelect = document.getElementById('clienteSelect');
    const sedeSelect = document.getElementById('sedeSelect');
    const consultarBtn = document.getElementById('consultarBtn');
    const sedeInput = document.getElementById('sedeInput');
    const pillsTab = document.getElementById('pills-tab');
    const pillsTabContent = document.getElementById('pills-tabContent');
    const cerarActivoBtn = document.getElementById('cerarActivoBtn');
    const guardarActivoBtn = document.getElementById('guardarActivoBtn');
    const activoIdInput = document.getElementById('activoId');
    const crearActivoModalLabel = document.getElementById('crearActivoModalLabel');
    const clienteInput = document.getElementById('clienteInput');
    const codigoInput = document.getElementById('codigo');
    const cantidadInput = document.getElementById('cantidad');
    const activoSelect = document.getElementById('activo');
    const imagenesInput = document.getElementById('imagenes');
    const imagenesPreview = document.getElementById('imagenesPreview');
    const crearActivoModal = document.getElementById('crearActivoModal');
    const mantenimientosTableBody = document.getElementById('mantenimientosTableBody');
    const busquedaMantenimientoInput = document.getElementById('busquedaMantenimientoInput');
    const registrosMantenimientoPorPagina = document.getElementById('registrosMantenimientoPorPagina');
    const estadoMantenimientoSelect = document.getElementById('estadoMantenimientoSelect');
    const totalActivos = document.getElementById('totalActivos');
    const totalMantenimientos = document.getElementById('totalMantenimientos');
    const mantenimientoModal = document.getElementById('mantenimientoModal');
    const guardarMantenimientoBtn = document.getElementById('guardarMantenimientoBtn');

    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);

    const paginacionContainer2 = document.createElement('div');
    paginacionContainer2.classList.add('paginacion');
    document.querySelector(".tabla-paginacion-mant").appendChild(paginacionContainer2);

    const registrosTurnoPorPagina = document.getElementById('registrosTurnoPorPagina');

    let activoId = null;
    let assignedActivoId = null;
    let editMode = false;

    clienteSelect.addEventListener('change', function () {
        const clienteId = this.value;
        fetch(`sedes?cliente_id=${clienteId}`)
            .then(response => response.json())
            .then(data => {
                sedeSelect.innerHTML = '<option value="">Seleccione una sede</option>';
                data.forEach(sede => {
                    const option = document.createElement('option');
                    option.value = sede.id;
                    option.textContent = sede.nombre;
                    sedeSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching sedes:', error));
    });

    sedeSelect.addEventListener('change', function () {
        const selectedOption = sedeSelect.options[sedeSelect.selectedIndex];
        sedeInput.value = selectedOption.textContent;
    });

    consultarBtn.addEventListener('click', function () {
        consultarActivos(1);
    });

    function consultarActivos(page = 1) {
        const sedeId = sedeSelect.value;
        const estadoId = estadoActivoSelect.value;
        const registrosPorPagina = registrosTurnoPorPagina.value;
        const buscar = busquedaTurnoInput.value;

        fetch(`gestionar-activos/consultar?sede_id=${sedeId}&estado_id=${estadoId}&page=${page}&registros_por_pagina=${registrosPorPagina}&buscar=${buscar}`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data.data)) {
                    throw new Error('Invalid response format');
                }
                activosTableBody.innerHTML = '';
                data.data.forEach(activo => {
                    const row = document.createElement('tr');
                    const estadoClase = activo.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                            <td>${activo.id}</td>
                            <td>${activo.activo.nombre_elemento}</td>
                            <td>${activo.cantidad}</td>
                            <td>${activo.estados?.nombre}</td>
                            <td>${activo.sede.nombre}</td>
                            <td>${activo.sede.cliente.nombre}</td>
                            <td><div class="${estadoClase}">${activo.estado ? 'Activo' : 'Inactivo'}</div></td>
                            <td>${activo.creador?.nombres || 'N/A'}</td>
                            <td>${formatDate(activo.created_at)}</td>
                            <td>${activo.actualizador?.nombres || 'N/A'}</td>
                            <td>${formatDate(activo.updated_at)}</td>
                            <td>
                                <img src="assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${activo.id}">
                                <img src="assets/icons/eliminar.png" alt="Eliminar" class="icono-eliminar" data-id="${activo.id}" data-tipo="gestionar-activos">
                            </td>
                        `;
                    activosTableBody.appendChild(row);
                });

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
                    consultarActivos(current_page - 1);
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
                        consultarActivos(i);
                    });

                    paginacionContainer.appendChild(pageButton);
                }

                // Botón de página siguiente
                const nextButton = document.createElement("button");
                nextButton.textContent = "Sig.";
                nextButton.classList.add("page-button", "sig");
                nextButton.disabled = current_page === last_page;
                nextButton.addEventListener('click', () => {
                    consultarActivos(current_page + 1);
                });
                paginacionContainer.appendChild(nextButton);

                // Mostrar total de registros
                const registrosEncontrados = document.querySelector('.registros-encontrados');
                if (registrosEncontrados) {
                    registrosEncontrados.textContent = `Total: ${data.total}`;
                }
                pillsTab.style.display = 'flex';
                pillsTabContent.style.display = 'block';
            })
            .catch(error => console.error('Error fetching activos:', error));

        consultarMantenimientos(1);
    }

    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('icono-editar')) {
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            activoId = event.target.getAttribute('data-id');
            if (!activoId) {
                console.error("Error: No se encontró el ID del activo en el botón.");
                return;
            }

            editMode = true;
            crearActivoModalLabel.textContent = "Editar Activo";
            guardarActivoBtn.textContent = "Guardar Cambios";

            fetch(`gestionar-activo/consultar?id=${activoId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos del activo.");
                    }
                    return response.json();
                })
                .then((activo) => {

                    // Populate form fields with activo data
                    const activoSelect = document.getElementById('activo');
                    activoSelect.value = activo.activo_id;
                    clienteInput.value = activo.sede.cliente.nombre;
                    sedeInput.value = activo.sede.nombre;
                    const estadoSelect = document.getElementById('estado');
                    estadoSelect.value = activo.estado_id;
                    codigoInput.value = activo.activo.serie;
                    cantidadInput.value = activo.cantidad;

                    // ...populate other fields...

                    // Clear previous image previews
                    imagenesPreview.innerHTML = '';

                    // Populate image previews
                    activo.imagenes.forEach(imagen => {
                        const imgContainer = document.createElement('div');
                        imgContainer.classList.add('img-container');
                        const img = document.createElement('img');
                        img.src = `${imagen.imagen}`; // Ensure the full URL is used
                        img.classList.add('img-thumbnail', 'mr-2', 'mb-2');
                        img.style.width = '100px';
                        img.style.height = '100px';
                        const removeBtn = document.createElement('button');
                        removeBtn.textContent = 'X';
                        removeBtn.classList.add('remove-btn');
                        removeBtn.addEventListener('click', function () {
                            imgContainer.remove();
                            // Optionally, handle image removal from the server here
                        });
                        imgContainer.appendChild(img);
                        imgContainer.appendChild(removeBtn);
                        imagenesPreview.appendChild(imgContainer);
                    });

                    $(crearActivoModal).modal('show');// Ensure jQuery is used to show the modal
                })
                .catch((error) => console.error("Error al cargar los datos del activo:", error));
        } else if (event.target.classList.contains('icono-ver')) {
            const mantenimientoId = event.target.getAttribute('data-id');
            if (!mantenimientoId) {
                console.error("Error: No se encontró el ID del mantenimiento en el botón.");
                return;
            }

            fetch(`obtener-detalles-mantenimiento/${mantenimientoId}`)
                .then(response => response.json())
                .then(mantenimiento => {
                    document.getElementById('nombreActivo').value = mantenimiento.sedes_activos.activo.nombre_elemento;
                    document.getElementById('estadoElemento').value = mantenimiento.estado.nombre;
                    document.getElementById('sedeMantenimiento').value = mantenimiento.sedes_activos.sede.nombre;
                    document.getElementById('clienteMantenimiento').value = mantenimiento.sedes_activos.sede.cliente.nombre;
                    document.getElementById('observaciones').value = mantenimiento.observaciones;
                    document.getElementById('observacionesReportadas').value = mantenimiento.observaciones_reportadas;

                    $(mantenimientoModal).modal('show'); // Use jQuery to show the modal
                })
                .catch(error => console.error("Error al cargar los datos del mantenimiento:", error));
        }

        if (event.target.classList.contains('icono-eliminar')) {
            const activoId = event.target.getAttribute('data-id');
            const tipo = event.target.getAttribute("data-tipo");
            const sedeId = sedeSelect.value; // Obtener el ID de la sede seleccionada
            if (!activoId) {
                console.error("Error: No se encontró el ID del activo en el botón.");
                return;
            }
            if (tipo === "gestionar-activos") {
                if (confirm("¿Está seguro de que desea eliminar este activo?")) {
                    fetch(`gestionar-activos/eliminar/${activoId}?sede_id=${sedeId}`, {
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Error al eliminar el activo.");
                            }
                            return response.json();
                        })
                        .then(data => {
                            showAlertModal(
                                "ok.png", // Ruta del ícono de éxito
                                data.message // Mensaje de éxito
                            );
                            /* alert(data.message); */
                            consultarActivos(); // Reload the table
                        })
                        .catch(error => console.error("Error al eliminar el activo:", error));
                }
            }

        }
    });

    guardarActivoBtn.addEventListener('click', function () {
        const url = editMode ? `gestionar-activos/actualizar/${activoId}` : `gestionar-activos/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(document.getElementById('crearActivoForm'));
        formData.append("estadoActivo", document.getElementById("estadoActivoToggle").checked ? 1 : 0);
        formData.append('sede_id', sedeSelect.value);

        // Append only the first image to formData
        if (imagenesInput.files.length > 0) {
            console.log(imagenesInput.files[0]);
            formData.append('imagenes', imagenesInput.files[0]);
        }

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
                console.log(data);
                if (data.errors) {
                    showActivoErrors(data.errors);
                } else {
                    showAlertModal(
                        "ok.png", // Ruta del ícono de éxito
                        data.message // Mensaje de éxito
                    );
                    $(crearActivoModal).modal('hide'); // Use jQuery to hide the modal
                    resetActivoForm();
                    consultarActivos(); // Reload the table
                }
            })
            .catch((error) => {
                console.error("Error al guardar el activo:", error);
                if (error.errors) {
                    showActivoErrors(error.errors);
                } else {
                    console.error("Error al guardar el activo:", error);
                }
            });
    });

    guardarMantenimientoBtn.addEventListener('click', function () {
        const formData = new FormData(document.getElementById('mantenimientoForm'));
        const mantenimientoId = document.querySelector('.icono-ver[data-id]').getAttribute('data-id');

        fetch(`actualizar-mantenimiento/${mantenimientoId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    showMantenimientoErrors(data.errors);
                } else {
                    showAlertModal(
                        "ok.png", // Ruta del ícono de éxito
                        data.message // Mensaje de éxito
                    );
                    $(mantenimientoModal).modal('hide'); // Use jQuery to hide the modal
                    consultarMantenimientos(); // Reload the table
                }
            })
            .catch(error => console.error("Error al guardar el mantenimiento:", error));
    });

    function showActivoErrors(errors) {
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

    function showMantenimientoErrors(errors) {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        for (const [key, messages] of Object.entries(errors)) {
            const errorElement = document.getElementById(`error${capitalizeFirstLetter(key)}`);
            if (errorElement) {
                errorElement.textContent = messages.join(', ');
            }
        }
    }

    function resetActivoForm() {
        document.getElementById('crearActivoForm').reset();
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        crearActivoModalLabel.textContent = "Crear nuevo Activo";
        guardarActivoBtn.textContent = "Crear Activo";
        editMode = false;
        activoId = null;
        imagenesPreview.innerHTML = ''; // Clear image previews
        imagenesInput.value = ''; // Clear image input
    }

    cerarActivoBtn.addEventListener('click', function () {
        editMode = false;
        activoId = null;
        document.getElementById('crearActivoForm').reset();
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        sedeInput.value = sedeSelect.options[sedeSelect.selectedIndex].textContent;
        clienteInput.value = clienteSelect.options[clienteSelect.selectedIndex].textContent;
        imagenesPreview.innerHTML = ''; // Clear image previews
        imagenesInput.value = ''; // Clear image input
        $(crearActivoModal).modal('show'); // Use jQuery to show the modal
    });

    function formatDate(dateString) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    function cargarActivos() {
        fetch(`activos`)
            .then(response => response.json())
            .then(activos => {
                const activoSelect = document.getElementById('activo');
                activoSelect.innerHTML = '<option value="">Seleccione un activo</option>';
                activos.forEach(activo => {
                    const option = document.createElement('option');
                    option.value = activo.id;
                    option.textContent = activo.nombre_elemento;
                    activoSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar los activos:', error));
    }

    activoSelect.addEventListener('change', function () {

        const activoId = this.value;
        fetch(`activo?id=${activoId}`)
            .then(response => response.json())
            .then(data => {
                codigoInput.value = data.serie;
            })
            .catch(error => console.error('Error fetching sedes:', error));

    });

    function cargarEstados() {
        fetch(`estados`)
            .then(response => response.json())
            .then(estados => {
                const estadoSelect = document.getElementById("estado");
                estadoSelect.innerHTML = '<option value="">Seleccione</option>';
                estados.forEach(estado => {
                    const option = document.createElement("option");
                    option.value = estado.id;
                    option.textContent = estado.nombre;
                    estadoSelect.appendChild(option);
                });

                // Llenar el select de estados para el filtro de mantenimientos
                estadoMantenimientoSelect.innerHTML = '<option value="">Todos los estados</option>';
                estadoActivoSelect.innerHTML = '<option value="">Todos los estados</option>';
                estados.forEach(estado => {
                    const option = document.createElement("option");
                    option.value = estado.id;
                    option.textContent = estado.nombre;
                    estadoMantenimientoSelect.appendChild(option);
                    estadoActivoSelect.appendChild(option.cloneNode(true));
                });
            })
            .catch(error => console.error("Error al cargar los estados:", error));
    }

    cargarActivos();
    cargarEstados();

    document.getElementById('pills-mantenimiento-tab').addEventListener('click', consultarMantenimientos(1));
    busquedaMantenimientoInput.addEventListener("input", () => consultarMantenimientos(1));
    estadoMantenimientoSelect.addEventListener("change", () => consultarMantenimientos(1));
    registrosMantenimientoPorPagina.addEventListener('change', consultarMantenimientos(1));

    function consultarMantenimientos(page = 1) {

        const sedeId = sedeSelect.value;
        const estadoId = estadoMantenimientoSelect.value;
        const registrosPorPagina = registrosMantenimientoPorPagina.value;
        const buscar = busquedaMantenimientoInput.value;

        fetch(`gestionar-activos/obtener-mantenimientos?sede_id=${sedeId}&estado_id=${estadoId}&page=${page}&registros_por_pagina=${registrosPorPagina}&buscar=${buscar}`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data.data)) {
                    throw new Error('Invalid response format');
                }
                mantenimientosTableBody.innerHTML = '';
                data.data.forEach(mantenimiento => {
                    const row = document.createElement('tr');
                    const estadoClase = mantenimiento.estado ? 'estado-activo' : 'estado-inactivo';

                    row.innerHTML = `
                        <td>${mantenimiento.id}</td>
                        <td>${formatDate(mantenimiento.ultimo_mtto)}</td>
                        <td>${formatDate(mantenimiento.mtto_programado)}</td>
                        <td>${mantenimiento.sedes_activos?.activo?.nombre_elemento || 'N/A'}</td>
                        <td>${mantenimiento.sedes_activos?.cantidad || 'N/A'}</td>
                        <td>${mantenimiento.estado?.nombre || 'N/A'}</td>
                        <td><div class="${estadoClase}">${mantenimiento.estado ? 'Activo' : 'Inactivo'}</div></td>
                        <td>${mantenimiento.sedes_activos?.sede?.nombre || 'N/A'}</td>
                        <td>${mantenimiento.sedes_activos?.sede?.cliente?.nombre || 'N/A'}</td>
                        <td>${mantenimiento.creador?.nombres || 'N/A'}</td>
                        <td>${formatDate(mantenimiento.created_at)}</td>
                        <td>${mantenimiento.actualizador?.nombres || 'N/A'}</td>
                        <td>${formatDate(mantenimiento.updated_at)}</td>
                        <td><img src="assets/icons/editar.png" alt="Ver" class="icono-ver" data-id="${mantenimiento.id}"></td>
                    `;
                    mantenimientosTableBody.appendChild(row);
                });

                // Generar paginación
                const { current_page, last_page } = data;

                // Limpiar la paginación anterior
                paginacionContainer2.innerHTML = '';

                // Botón de página anterior
                const prevButton2 = document.createElement("button");
                prevButton2.textContent = "Ant.";
                prevButton2.classList.add("page-button", "ant");
                prevButton2.disabled = current_page === 1;
                prevButton2.addEventListener('click', () => {
                    consultarMantenimientos(current_page - 1);
                });
                paginacionContainer2.appendChild(prevButton2);

                // Crear botones de página (máximo 6 números)
                const startPage2 = Math.max(1, current_page - 2);
                const endPage2 = Math.min(last_page, current_page + 3);

                for (let i = startPage2; i <= endPage2; i++) {
                    const pageButton2 = document.createElement("button");
                    pageButton2.classList.add('page-button');
                    pageButton2.textContent = i;
                    pageButton2.style = i === current_page ? 'background: #000000;' : 'font: normal normal normal 12px/16px Neo Sans Std; color: #4B4B4B;';
                    if (i === current_page) pageButton2.classList.add('active');

                    pageButton2.addEventListener('click', () => {
                        consultarMantenimientos(i);
                    });

                    paginacionContainer2.appendChild(pageButton2);
                }

                // Botón de página siguiente
                const nextButton = document.createElement("button");
                nextButton.textContent = "Sig.";
                nextButton.classList.add("page-button", "sig");
                nextButton.disabled = current_page === last_page;
                nextButton.addEventListener('click', () => {
                    consultarMantenimientos(current_page + 1);
                });
                paginacionContainer2.appendChild(nextButton);

                // Mostrar total de registros
                const registrosEncontrados = document.querySelector('.registros-encontrados-mant');
                if (registrosEncontrados) {
                    registrosEncontrados.textContent = `Total: ${data.total}`;
                }
                //totalMantenimientos.textContent = `Total: ${data.length}`;
            })
            .catch(error => console.error('Error fetching mantenimientos:', error));
    }

    document.addEventListener('DOMContentLoaded', function () {
        const busquedaTurnoInput = document.getElementById('busquedaTurnoInput');
        const estadoActivoSelect = document.getElementById('estadoActivoSelect');
        const registrosTurnoPorPagina = document.getElementById('registrosTurnoPorPagina');

        busquedaTurnoInput.addEventListener('input', consultarActivos);
        estadoActivoSelect.addEventListener('change', consultarActivos);
        registrosTurnoPorPagina.addEventListener('change', consultarActivos);

        //consultarActivos()

        document.getElementById('pills-activos-tab').addEventListener('click', consultarActivos);

        const imagenesInput = document.getElementById('imagenes');
        const imagenesPreview = document.getElementById('imagenesPreview');

        imagenesInput.addEventListener('change', function () {
            imagenesPreview.innerHTML = ''; // Clear previous previews
            const dt = new DataTransfer();
            for (const file of imagenesInput.files) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.classList.add('img-container');
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('img-thumbnail', 'mr-2', 'mb-2');
                    img.style.width = '100px';
                    img.style.height = '100px';
                    const removeBtn = document.createElement('button');
                    removeBtn.textContent = 'X';
                    removeBtn.classList.add('remove-btn');
                    removeBtn.addEventListener('click', function () {
                        imgContainer.remove();
                        // Remove the file from the input
                        const files = Array.from(imagenesInput.files);
                        const index = files.indexOf(file);
                        if (index > -1) {
                            files.splice(index, 1);
                            dt.items.clear();
                            files.forEach(f => dt.items.add(f));
                            imagenesInput.files = dt.files;
                        }
                    });
                    imgContainer.appendChild(img);
                    imgContainer.appendChild(removeBtn);
                    imagenesPreview.appendChild(imgContainer);
                    dt.items.add(file); // Add file to DataTransfer
                };
                reader.readAsDataURL(file);
            }
            //imagenesInput.files = dt.files; // Update input files
        });
    });

})();

