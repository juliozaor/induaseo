document.addEventListener('DOMContentLoaded', function() {
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

    clienteSelect.addEventListener('change', function() {
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

    consultarBtn.addEventListener('click', function() {
        const sedeId = sedeSelect.value;

        fetch(`reportes/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                actividadesTableBody.innerHTML = '';
                data.forEach(turno => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${turno.fecha}</td>
                        <td>${turno.actividades_completadas}</td>
                        <td>${turno.actividades_incompletadas}</td>
                    `;
                    actividadesTableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Error fetching actividades:', error));

        fetch(`reportes/consultar-turnos?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                turnoMenu.innerHTML = '';
                data.forEach(turno => {
                    const li = document.createElement('li');
                    li.textContent = `Turno: ${turno.nombre}`;
                    li.classList.add('menu-item');
                    li.dataset.id = turno.id;
                    turnoMenu.appendChild(li);
                });
            })
            .catch(error => console.error('Error fetching turnos:', error));

        fetch(`reportes/activos?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                activosTableBody.innerHTML = '';
                data.forEach(activo => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${activo.activo}</td>
                        <td>${activo.cantidad}</td>
                        <td>${activo.estado}</td>
                        <td>${activo.observacion}</td>
                    `;
                    activosTableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Error fetching activos:', error));
    });

    turnoMenu.addEventListener('click', function(event) {
        if (event.target.classList.contains('menu-item')) {
            const turnoId = event.target.dataset.id;
            const submenu = event.target.nextElementSibling;

            if (submenu && submenu.classList.contains('submenu')) {
                submenu.remove();
                event.target.classList.remove('expanded');
            } else {
                fetch(`reportes/consultar-areas?turno_id=${turnoId}`)
                    .then(response => response.json())
                    .then(data => {
                        const opciones = document.createElement('ul');
                        opciones.classList.add('submenu');
                        data.forEach(area => {
                            const li = document.createElement('li');
                            li.textContent = `Área: ${area.nombre}`;
                            li.classList.add('submenu-item');
                            li.dataset.id = area.id;
                            opciones.appendChild(li);
                        });
                        event.target.insertAdjacentElement('afterend', opciones);
                        event.target.classList.add('expanded');
                    })
                    .catch(error => console.error('Error fetching areas:', error));
            }
        } else if (event.target.classList.contains('submenu-item')) {
            const areaId = event.target.dataset.id;
            const submenu = event.target.nextElementSibling;

            if (submenu && submenu.classList.contains('submenu')) {
                submenu.remove();
                event.target.classList.remove('expanded');
            } else {
                fetch(`reportes/consultar-actividades?area_id=${areaId}`)
                    .then(response => response.json())
                    .then(data => {
                        const opciones = document.createElement('ul');
                        opciones.classList.add('submenu');
                        data.forEach(actividad => {
                            const li = document.createElement('li');
                            li.textContent = `Actividad: ${actividad.nombre}`;
                            li.classList.add('submenu-item-act');
                            opciones.appendChild(li);
                        });
                        event.target.insertAdjacentElement('afterend', opciones);
                        event.target.classList.add('expanded');
                    })
                    .catch(error => console.error('Error fetching actividades:', error));
            }
        }
    });

    aplicarFiltroBtn.addEventListener('click', function() {
        const sedeId = sedeSelect.value;
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;

        fetch(`reportes/consultar?sede_id=${sedeId}&fecha_inicio=${fechaInicioVal}&fecha_fin=${fechaFinVal}`)
            .then(response => response.json())
            .then(data => {
                actividadesTableBody.innerHTML = '';
                data.forEach(turno => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${turno.fecha}</td>
                        <td>${turno.actividades_completadas}</td>
                        <td>${turno.actividades_incompletadas}</td>
                    `;
                    actividadesTableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Error fetching actividades:', error));
    });

    limpiarFiltroBtn.addEventListener('click', function() {
        fechaInicio.value = '';
        fechaFin.value = '';
        const sedeId = sedeSelect.value;

        fetch(`reportes/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                actividadesTableBody.innerHTML = '';
                data.forEach(turno => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${turno.fecha}</td>
                        <td>${turno.actividades_completadas}</td>
                        <td>${turno.actividades_incompletadas}</td>
                    `;
                    actividadesTableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Error fetching actividades:', error));
    });
});
