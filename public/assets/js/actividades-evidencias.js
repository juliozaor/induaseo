document.addEventListener('DOMContentLoaded', function() {
    const sedeSelect = document.getElementById('sedeSelect');
    const consultarBtn = document.getElementById('consultarBtn');
    const turnosTableBody = document.getElementById('turnosTableBody');   
    const nuevaAsignacionBtn = document.getElementById('nuevaAsignacionBtn');
    const registrosEncontrados = document.querySelector('.registros-encontrados');

    consultarBtn.addEventListener('click', function() {
        const sedeId = sedeSelect.value;
        
        fetch(`actividades-evidencias/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                turnosTableBody.innerHTML = '';
                let totalRegistros = 0;
                data.forEach(turno => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${turno.fecha}</td>
                        <td>${turno.nombre_turno}</td>
                        <td>${turno.regional}</td>
                        <td>${turno.actividades_completadas}</td>
                        <td>${turno.supervisor}</td>
                        <td>${turno.observaciones}</td>
                        <td><img src="assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${turno.id}"></td>
                    `;
                    turnosTableBody.appendChild(row);
                    totalRegistros++;
                });
                registrosEncontrados.textContent = `Total: ${totalRegistros}`;
                nuevaAsignacionBtn.style.display = 'inline-block';
            })
            .catch(error => console.error('Error fetching turnos:', error));
    });

    turnosTableBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('icono-editar')) {
            const turnoId = event.target.getAttribute('data-id');
            fetch(`actividades-evidencias/detalle/${turnoId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('detalleSupervisor').value = data.supervisor;
                    document.getElementById('detalleSede').value = data.sede;
                    document.getElementById('detalleFecha').value = data.fecha;
                    const actividadesTableBody = document.getElementById('actividadesTableBody');
                    actividadesTableBody.innerHTML = '';
                    data.actividades.forEach(actividad => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${actividad.id}</td>
                            <td>${actividad.nombre}</td>
                            <td>${actividad.descripcion}</td>
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
});