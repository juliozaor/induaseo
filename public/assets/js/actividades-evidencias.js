document.addEventListener('DOMContentLoaded', function () {
    const sedeSelect = document.getElementById('sedeSelect');
    const consultarBtn = document.getElementById('consultarBtn');
    const turnosTableBody = document.getElementById('turnosTableBody');
    const registrosEncontrados = document.querySelector('.registros-encontrados');
    const actividadesTableBody = document.getElementById('actividadesTableBody');
    const actividadesMensaje = document.getElementById('actividadesMensaje');
    const turnoMenu = document.getElementById('turnoMenu');
    /* const areaDetails = document.getElementById('areaDetails');
    const areaNombre = document.getElementById('areaNombre');
    const areaDescripcion = document.getElementById('areaDescripcion');
    const areaActividades = document.getElementById('areaActividades'); */

    function renderPagination(totalItems, currentPage, rowsPerPage, paginationId, renderFunction, data) {
        const totalPages = Math.ceil(totalItems / rowsPerPage);
        const paginationContainer = document.getElementById(paginationId);
        paginationContainer.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            pageButton.classList.add('page-button');
            if (i === currentPage) {
                pageButton.classList.add('active');
            }
            pageButton.addEventListener('click', () => renderFunction(data, i, rowsPerPage));
            paginationContainer.appendChild(pageButton);
        }
    }

    function renderTurnosTable(data, page = 1, rowsPerPage = 5) {
        turnosTableBody.innerHTML = '';
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedData = data.slice(start, end);

        paginatedData.forEach(turno => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${turno.fecha}</td>
                <td>${turno.nombre_turno}</td>
                <td>${turno.regional}</td>
                <td>${turno.actividades_completadas}</td>
                <td>${turno.supervisor}</td>
                <td>${turno.observaciones ?? 'Sin observaciones'}</td>
                <td><img src="assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${turno.id}"></td>
            `;
            turnosTableBody.appendChild(row);
        });

        renderPagination(data.length, page, rowsPerPage, 'turnosPaginacion', renderTurnosTable, data);
    }

    function fetchTurnos(sedeId) {
        fetch(`actividades-evidencias/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                renderTurnosTable(data, 1, 5);
                registrosEncontrados.textContent = `Total: ${data.length}`;
            })
            .catch(error => console.error('Error fetching turnos:', error));
    }

    function renderTurnoMenu(data) {
        turnoMenu.innerHTML = '';
        data.forEach(turno => {
            console.log('Turno:', turno.supervisor_turno_id); // Debug statement
            const li = document.createElement('li');
            li.classList.add('menu-item');
            li.dataset.id = turno.id;
            li.innerHTML = `
                Listado de áreas del turno: ${turno.nombre}
                <button class="btn-consultar encuesta-turno-btn" data-supervisor-turno-id="${turno.supervisor_turno_id}" style="float: right;">Encuesta</button>
            `;
            turnoMenu.appendChild(li);
        });
    }

    consultarBtn.addEventListener('click', function () {
        const sedeId = sedeSelect.value;
        fetchTurnos(sedeId);

        // Consultar actividades
        fetch(`reportes/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                actividadesData = data;
                if (actividadesData.length > 0) {
                    renderTable(actividadesData);
                } else {
                    actividadesMensaje.textContent = 'No hay registros para mostrar.';
                    actividadesTableBody.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error fetching actividades:', error);
                actividadesMensaje.textContent = 'Error al cargar los datos.';
            });

        // Consultar reportes
        fetch(`reportes/consultar-turnos?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                renderTurnoMenu(data);
            })
            .catch(error => console.error('Error fetching turnos:', error));
    });

    turnosTableBody.addEventListener('click', function (event) {
        if (event.target.classList.contains('icono-editar')) {
            const turnoId = event.target.getAttribute('data-id');
            fetch(`actividades-evidencias/detalle/${turnoId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('detalleSupervisor').value = data.supervisor;
                    document.getElementById('detalleSede').value = data.sede;
                    document.getElementById('detalleFecha').value = data.fecha;
                    const actividadesTableBody = document.getElementById('actividadesTableBody2');
                    actividadesTableBody.innerHTML = '';
                    console.log(data.actividades);
                    data.actividades.forEach(actividad => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${actividad.id}</td>
                            <td>${actividad.nombre_area}</td>
                            <td>${actividad.nombre}</td>
                            <td>${actividad.estado ? 'Pendiente' : 'Completado'}</td>
                            <td>${actividad.calificacion ?? 0}/5</td>
                        `;
                        actividadesTableBody.appendChild(row);
                    });
                    $('#detalleTurnoModal').modal('show');
                })
                .catch(error => console.error('Error fetching turno details:', error));
        }
    });

    const aplicarFiltroBtn = document.getElementById('aplicarFiltroBtn');
    const limpiarFiltroBtn = document.getElementById('limpiarFiltroBtn');
    const exportarBtn = document.getElementById('exportarBtn');
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');

    let actividadesData = [];

    function renderTable(data, page = 1, rowsPerPage = 5) {
        actividadesMensaje.textContent = '';
        if (data.length === 0) {
            actividadesMensaje.textContent = 'No hay registros para mostrar.';
            actividadesTableBody.innerHTML = '';
            return;
        }

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedData = data.slice(start, end);

        actividadesTableBody.innerHTML = '';
        paginatedData.forEach(turno => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${turno.fecha ?? '-'}</td>
                <td>${turno.area}</td>
                <td>${turno.actividad}</td>
                <td>${turno.estado}</td>
            `;
            actividadesTableBody.appendChild(row);
        });

        renderPagination(data.length, page, rowsPerPage, 'actividadesPaginacion', renderTable, actividadesData);
    }

    aplicarFiltroBtn.addEventListener('click', function () {
        const sedeId = sedeSelect.value;
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;

        actividadesMensaje.textContent = 'Cargando...';

        fetch(`reportes/consultar?sede_id=${sedeId}&fecha_inicio=${fechaInicioVal}&fecha_fin=${fechaFinVal}`)
            .then(response => response.json())
            .then(data => {
                actividadesData = data;
                if (actividadesData.length > 0) {
                    renderTable(actividadesData);
                    actividadesMensaje.textContent = '';
                } else {
                    actividadesMensaje.textContent = 'No hay registros para mostrar.';
                    actividadesTableBody.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error fetching actividades:', error);
                actividadesMensaje.textContent = 'Error al cargar los datos.';
            });
    });

    limpiarFiltroBtn.addEventListener('click', function () {
        fechaInicio.value = '';
        fechaFin.value = '';
        const sedeId = sedeSelect.value;

        actividadesMensaje.textContent = 'Cargando...';

        // Consultar actividades
        fetch(`reportes/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                actividadesData = data;
                if (actividadesData.length > 0) {
                    renderTable(actividadesData);
                } else {
                    actividadesMensaje.textContent = 'No hay registros para mostrar.';
                    actividadesTableBody.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error fetching actividades:', error);
                actividadesMensaje.textContent = 'Error al cargar los datos.';
            });
    });

    exportarBtn.addEventListener('click', function () {
        const sedeId = sedeSelect.value;
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;

        const url = `actividades-evidencias/exportar-actividades?sede_id=${sedeId}&fecha_inicio=${fechaInicioVal}&fecha_fin=${fechaFinVal}`;
        window.location.href = url;
    });

    turnoMenu.addEventListener('click', function (event) {
        const sedeId = sedeSelect.value; // Get the selected sede ID
        if (event.target.classList.contains('menu-item')) {
            const turnoId = event.target.dataset.id;
            const submenu = event.target.nextElementSibling;

            if (submenu && submenu.classList.contains('submenu')) {
                submenu.remove();
                event.target.classList.remove('expanded');
            } else {
                fetch(`actividades-evidencias/consolidado?turno_id=${turnoId}&sede_id=${sedeId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Turno data:', data); // Debug statement
                        const opciones = document.createElement('ul');
                        opciones.classList.add('submenu');
                        data.forEach(area => {
                            const li = document.createElement('li');
                            li.textContent = `Área: ${area.nombre}`;
                            li.classList.add('submenu-item');
                            li.dataset.id = area.id;
                            li.dataset.turnoId = turnoId; // Store turnoId in dataset
                            opciones.appendChild(li);
                        });
                        event.target.insertAdjacentElement('afterend', opciones);
                        event.target.classList.add('expanded');
                    })
                    .catch(error => console.error('Error fetching areas:', error));
            }
        } else if (event.target.classList.contains('submenu-item')) {
            const areaId = event.target.dataset.id;
            const turnoId = event.target.dataset.turnoId; // Retrieve turnoId from dataset
            const submenu = event.target.nextElementSibling;

            if (submenu && submenu.classList.contains('submenu')) {
                submenu.remove();
                event.target.classList.remove('expanded');
            } else {
                fetch(`actividades-evidencias/consolidado?area_id=${areaId}&turno_id=${turnoId}&sede_id=${sedeId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Area data:', data); // Debug statement
                        const opciones = document.createElement('ul');
                        opciones.classList.add('submenu');
                        const areaDetails = document.createElement('div');
                        areaDetails.classList.add('area-details');
                        areaDetails.style.marginBottom = '20px'; // Add margin to create space
                        areaDetails.innerHTML = `
                            <h3>Detalles del Área</h3>
                            <form>
                                <div class="form-group">
                                    <label for="detalleSupervisor">Supervisor:</label>
                                    <input type="text" id="detalleSupervisor" class="form-control" value="${data.supervisor}" readonly>
                                    <input type="hidden" id="turnoIdHidden" value="${turnoId}">
                                    <input type="hidden" id="areaIdHidden" value="${areaId}">
                                </div>
                                <div class="form-group">
                                    <label for="detalleFecha">Fecha de Asignación:</label>
                                    <input type="text" id="detalleFecha" class="form-control" value="${data.fecha}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="areaActividades">Actividades:</label>
                                    <div class="tabla-container">
                                        <table class="tabla">
                                            <thead>
                                                <tr>
                                                    <th>Actividad</th>
                                                    <th>Estado</th>
                                                    <th>Calificación</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.actividades.map(actividad => `
                                                    <tr>
                                                        <td>${actividad.nombre}</td>
                                                        <td>${actividad.estado ? 'Pendiente' : 'Completado'}</td>
                                                        <td>${actividad.calificacion ?? 0}/5</td>
                                                        <td>
                                                            <img src="assets/icons/editar.png" alt="Ver" class="icono-editar"
                                                            data-id="${actividad.id}" data-turno-id="${turnoId}" data-area-id="${areaId}">
                                                        </td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        `;
                        opciones.appendChild(areaDetails);
                        event.target.insertAdjacentElement('afterend', opciones);
                        event.target.classList.add('expanded');
                    })
                    .catch(error => console.error('Error fetching actividades:', error));
            }
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('icono-editar')) {
            const actividadId = event.target.getAttribute('data-id');
            const areaId = event.target.getAttribute('data-area-id'); /* document.getElementById('areaIdHidden').value; */
            const turnoId = event.target.getAttribute('data-turno-id'); /* document.getElementById('turnoIdHidden').value; */
            const sedeId = document.getElementById('sedeSelect').value;
            console.log('Actividad ID:', actividadId); // Debug statement
            fetch(`actividades-evidencias/detalle-actividad?sede_id=${sedeId}&turno_id=${turnoId}&area_id=${areaId}&actividad_id=${actividadId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('detalleActividadNombre').value = data.nombre;
                    document.getElementById('detalleActividadEstado').value = data.estado ? 'Pendiente' : 'Completado';
                    document.getElementById('detalleActividadCalificacion').value = data.calificacion ? data.calificacion + '/5' : '0/5';

                    const imagenesContainer = document.getElementById('detalleActividadImagenes');
                    imagenesContainer.innerHTML = '';
                    if (data.imagenes.length > 0) {
                        data.imagenes.forEach(imagen => {
                            const imgElement = document.createElement('img');
                            imgElement.src = imagen.url;
                            imgElement.alt = 'Evidencia';
                            imgElement.classList.add('img-thumbnail', 'm-2');
                            imgElement.style.width = '100px';
                            imagenesContainer.appendChild(imgElement);
                        });
                    }

                    $('#detalleActividadModal').modal('show');
                })
                .catch(error => console.error('Error fetching actividad details:', error));
        } else if (event.target.classList.contains('encuesta-turno-btn')) {
            const turnoId = event.target.dataset.turnoId;
            console.log(`Abrir encuesta para el turno ID: ${turnoId}`);
            $('#encuestaModal').modal('show');
        }
    });

    // Handle survey submission
    document.getElementById('enviarEncuesta').addEventListener('click', function (event) {
        event.preventDefault();

        // Get the supervisor_turno_id from the active survey button
        const supervisorTurnoId = document.querySelector('.encuesta-turno-btn[data-supervisor-turno-id]').dataset.supervisorTurnoId;

        // Calculate total points from the survey
        const totalPuntos = Array.from(document.querySelectorAll('input[name="calificacion"]:checked'))
            .map(input => parseInt(input.value))
            .reduce((acc, val) => acc + val, 0);

        if (!supervisorTurnoId || totalPuntos === 0) {
            alert('Por favor, complete la encuesta antes de enviarla.');
            return;
        }
        console.log(`Enviar encuesta para el supervisor_turno_id: ${supervisorTurnoId}, total_puntos: ${totalPuntos}`);
        // Send the survey data to the server
        fetch('satisfaccion-servicio/guardar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                supervisor_turnos_id: supervisorTurnoId,
                total_puntos: totalPuntos
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    $('#encuestaModal').modal('hide');
                    document.getElementById('encuestaForm').reset();
                } else {
                    alert('2. Error al guardar la encuesta.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('1. Error al guardar la encuesta.');
            });
    });
});
