document.addEventListener('DOMContentLoaded', function () {
    const clienteSelect = document.getElementById('clienteSelect');
    const sedeSelect = document.getElementById('sedeSelect');
    const consultarBtn = document.getElementById('consultarBtn');
    const actividadesTableBody = document.getElementById('actividadesTableBody');
    const activosTableBody = document.getElementById('activosTableBody');
    const aplicarFiltroBtn = document.getElementById('aplicarFiltroBtn');
    const limpiarFiltroBtn = document.getElementById('limpiarFiltroBtn');
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');
    const turnoMenu = document.getElementById('turnoMenu');
    const exportarBtn = document.getElementById('exportarBtn');
    const exportarActivosBtn = document.getElementById('exportarActivosBtn');
    const actividadesMensaje = document.getElementById('actividadesMensaje');
    const activosMensaje = document.getElementById('activosMensaje');
    const insumosTableBody = document.getElementById('insumosTableBody');
    const insumosMensaje = document.getElementById('insumosMensaje');
    const exportarInsumosBtn = document.getElementById('exportarInsumosBtn');

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

    let actividadesData = [];
    let activosData = [];
    let insumosData = [];

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

    function renderActivosTable(data, page = 1, rowsPerPage = 3) {
        activosMensaje.textContent = '';
        if (data.length === 0) {
            activosMensaje.textContent = 'No hay registros para mostrar.';
            activosTableBody.innerHTML = '';
            return;
        }

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedData = data.slice(start, end);

        activosTableBody.innerHTML = '';
        paginatedData.forEach(activo => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${activo.activo ?? '-'}</td>
                <td>${activo.cantidad ?? '-'}</td>
                <td>${activo.estado ?? '-'}</td>
                <td>${activo.observacion ?? '-'}</td>
            `;
            activosTableBody.appendChild(row);
        });

        renderPagination(data.length, page, rowsPerPage, 'activosPaginacion', renderActivosTable, activosData);
    }

    function renderInsumosTable(data, page = 1, rowsPerPage = 3) {
        console.log('Insumos Data:', data); // Debug statement
        insumosMensaje.textContent = '';
        if (data.length === 0) {
            insumosMensaje.textContent = 'No hay registros para mostrar.';
            insumosTableBody.innerHTML = '';
            return;
        }

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedData = data.slice(start, end);

        insumosTableBody.innerHTML = '';
        paginatedData.forEach(insumo => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>${insumo.insumo ?? '-'}</td>
            <td>${insumo.cantidad ?? '-'}</td>
            `;
            insumosTableBody.appendChild(row);
        });

        renderPagination(data.length, page, rowsPerPage, 'activosPaginacion', renderInsumosTable, data);
    }

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

    consultarBtn.addEventListener('click', function () {
        const sedeId = sedeSelect.value;

        actividadesMensaje.textContent = 'Cargando...';
        activosMensaje.textContent = 'Cargando...';

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

        fetch(`reportes/consultar-turnos?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                turnoMenu.innerHTML = '';
                data.forEach(turno => {
                    const li = document.createElement('li');
                    /* li.textContent = `Turno: ${turno.nombre}`; */
                    li.innerHTML = `
                        Turno: ${turno.nombre}
                        <div class="datos-satisfaccion">
                            <span>Satisfacción del cliente: ${turno.satisfaccion ?? 0}</span>
                            <span>Encuestas enviadas: ${turno.total_encuestas ?? 0}</span>
                        </div>
                    `;
                    li.classList.add('menu-item');
                    li.dataset.id = turno.id;
                    turnoMenu.appendChild(li);
                });
            })
            .catch(error => console.error('Error fetching turnos:', error));

        fetch(`reportes/activos?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                activosData = data;
                renderActivosTable(activosData);
            })
            .catch(error => {
                console.error('Error fetching activos:', error);
                activosMensaje.textContent = 'Error al cargar los datos.';
            });

        fetch(`reportes/insumos?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                insumosData = data;
                renderInsumosTable(insumosData);
            })
            .catch(error => {
                console.error('Error fetching insumos:', error);
                insumosMensaje.textContent = 'Error al cargar los datos.';
            });
    });

    turnoMenu.addEventListener('click', function (event) {
        const sedeId = sedeSelect.value;
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
                        const opciones = document.createElement('ul');
                        opciones.classList.add('submenu');
                        data.forEach(area => {
                            const li = document.createElement('li');
                            li.textContent = `Área: ${area.nombre}`;
                            li.classList.add('submenu-item');
                            li.dataset.id = area.id;
                            li.dataset.turnoId = turnoId;
                            opciones.appendChild(li);
                        });
                        event.target.insertAdjacentElement('afterend', opciones);
                        event.target.classList.add('expanded');
                    })
                    .catch(error => console.error('Error fetching areas:', error));
            }
        } else if (event.target.classList.contains('submenu-item')) {
            const areaId = event.target.dataset.id;
            const turnoId = event.target.dataset.turnoId;
            const submenu = event.target.nextElementSibling;

            if (submenu && submenu.classList.contains('submenu')) {
                submenu.remove();
                event.target.classList.remove('expanded');
            } else {
                fetch(`actividades-evidencias/consolidado?area_id=${areaId}&turno_id=${turnoId}&sede_id=${sedeId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Data:', data); // Debug statement
                        const opciones = document.createElement('ul');
                        opciones.classList.add('submenu');
                        const areaDetails = document.createElement('div');
                        areaDetails.classList.add('area-details');
                        areaDetails.style.marginBottom = '20px';
                        areaDetails.innerHTML = `
                            <h3>Detalles del Área</h3>
                            <form>
                                <div class="form-group">
                                    <label for="detalleSupervisor">Supervisor:</label>${turnoId}
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

    aplicarFiltroBtn.addEventListener('click', function () {
        const sedeId = sedeSelect.value;
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;

        actividadesMensaje.textContent = 'Cargando...';
        /* activosMensaje.textContent = 'Cargando...'; */

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

        /* fetch(`reportes/activos?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                activosData = data;
                renderActivosTable(activosData);
            })
            .catch(error => {
                console.error('Error fetching activos:', error);
                activosMensaje.textContent = 'Error al cargar los datos.';
            }); */
    });

    limpiarFiltroBtn.addEventListener('click', function () {
        fechaInicio.value = '';
        fechaFin.value = '';
        const sedeId = sedeSelect.value;

        actividadesMensaje.textContent = 'Cargando...';
        activosMensaje.textContent = 'Cargando...';

        fetch(`reportes/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                actividadesData = data;
                renderTable(actividadesData);
            })
            .catch(error => {
                console.error('Error fetching actividades:', error);
                actividadesMensaje.textContent = 'Error al cargar los datos.';
            });

        fetch(`reportes/activos?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                activosData = data;
                renderActivosTable(activosData);
            })
            .catch(error => {
                console.error('Error fetching activos:', error);
                activosMensaje.textContent = 'Error al cargar los datos.';
            });
    });

    exportarBtn.addEventListener('click', function () {
        const sedeId = sedeSelect.value;
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;

        const url = `reportes/exportar-actividades?sede_id=${sedeId}&fecha_inicio=${fechaInicioVal}&fecha_fin=${fechaFinVal}`;
        window.location.href = url;
    });

    exportarActivosBtn.addEventListener('click', function () {
        const sedeId = sedeSelect.value;

        const url = `reportes/exportar-activos?sede_id=${sedeId}`;
        window.location.href = url;
    });
    exportarInsumosBtn.addEventListener('click', function () {
        const sedeId = sedeSelect.value;

        const url = `reportes/exportar-insumos?sede_id=${sedeId}`;
        window.location.href = url;
    });
});

document.addEventListener('click', function (event) {
    if (event.target.classList.contains('icono-editar')) {
        const actividadId = event.target.getAttribute('data-id');
        const areaId = event.target.getAttribute('data-area-id'); /* document.getElementById('areaIdHidden').value; */
        const turnoId = event.target.getAttribute('data-turno-id'); /* document.getElementById('turnoIdHidden').value; */
        const sedeId = document.getElementById('sedeSelect').value;
        console.log(areaId, turnoId, sedeId); // Debug statement
        fetch(`actividades-evidencias/detalle-actividad?sede_id=${sedeId}&turno_id=${turnoId}&area_id=${areaId}&actividad_id=${actividadId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('detalleActividadNombre').value = data.nombre;
                document.getElementById('detalleActividadEstado').value = data.estado ? 'Pendiente' : 'Completado';
                document.getElementById('detalleActividadCalificacion').value = data.calificacion ? data.calificacion + '/5' : '0/5';

                const imagenesContainer = document.getElementById('detalleActividadImagenes');
                imagenesContainer.innerHTML = '';
                console.log('Imagenes:', data.imagenes); // Debug statement
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
    }
});
