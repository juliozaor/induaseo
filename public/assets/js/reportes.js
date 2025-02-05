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
                nuevaAsignacionBtn.style.display = 'inline-block';
            })
            .catch(error => console.error('Error fetching actividades:', error));


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