document.addEventListener('DOMContentLoaded', function() {
    const clienteSelect = document.getElementById('clienteSelect');
    const sedeSelect = document.getElementById('sedeSelect');
    const consultarBtn = document.getElementById('consultarBtn');
    const turnosTableBody = document.getElementById('turnosTableBody');
    const supervisorSelect = document.getElementById('supervisorSelect');
    const sedeInput = document.getElementById('sedeInput');
    const fechaInicioInput = document.getElementById('fechaInicioInput');
    const fechaFinInput = document.getElementById('fechaFinInput');
    const turnoSelect = document.getElementById('turnoSelect');
    const guardarTurnoBtn = document.getElementById('guardarTurnoBtn');
    const nuevaAsignacionBtn = document.getElementById('nuevaAsignacionBtn');
    const asignarTurnoModal = document.getElementById('asignarTurnoModal');
    let editMode = false;
    let turnoId = null;

    clienteSelect.addEventListener('change', function() {
        const clienteId = this.value;
        fetch(`${rutaRelativa}sedes?cliente_id=${clienteId}`)
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

    sedeSelect.addEventListener('change', function() {
        const selectedOption = sedeSelect.options[sedeSelect.selectedIndex];
        sedeInput.value = selectedOption.textContent;
    });

    consultarBtn.addEventListener('click', function() {
        const sedeId = sedeSelect.value;
        fetch(`${rutaRelativa}asignar-turnos/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Turnos data:', data); // Add this line to inspect the response data
                turnosTableBody.innerHTML = '';
                data.forEach(turno => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${turno.fecha_inicio}</td>
                        <td>${turno.fecha_fin}</td>
                        <td>${turno.supervisor.numero_documento}</td>
                        <td>${turno.supervisor.nombres} ${turno.supervisor.apellidos}</td>
                        <td>${turno.turno.nombre}</td>
                        <td>${turno.turno.actividades_count}</td>
                        <td><img src="${rutaRelativa}assets/icons/editar.png" alt="Editar" class="icono-editar editarTurnoBtn" data-id="${turno.id}"></td>
                    `;
                    turnosTableBody.appendChild(row);
                });
                nuevaAsignacionBtn.style.display = 'inline-block';
            })
            .catch(error => console.error('Error fetching turnos:', error));
    });

    guardarTurnoBtn.addEventListener('click', function() {
        const formData = new FormData();
        formData.append('supervisor_id', supervisorSelect.value);
        formData.append('sede_id', sedeSelect.value);
        formData.append('turno_id', turnoSelect.value);
        formData.append('fecha_inicio', fechaInicioInput.value);
        formData.append('fecha_fin', fechaFinInput.value);

        const url = editMode ? `${rutaRelativa}asignar-turnos/actualizar/${turnoId}` : `${rutaRelativa}asignar-turnos/guardar`;
        const method = editMode ? 'PUT' : 'POST';

        if (editMode) {
            formData.append('_method', 'PUT');
        }

        console.log('FormData:', ...formData); // Add this line to inspect the form data

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            $('#asignarTurnoModal').modal('hide');
            consultarBtn.click();
        })
        .catch(error => console.error('Error saving turno:', error));
    });

    nuevaAsignacionBtn.addEventListener('click', function() {
        editMode = false;
        turnoId = null;
        document.getElementById('asignarTurnoForm').reset();
        sedeInput.value = sedeSelect.options[sedeSelect.selectedIndex].textContent; // Set the sede input value
        $('#asignarTurnoModal').modal('show');
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('editarTurnoBtn')) {
            turnoId = event.target.getAttribute('data-id');
            editMode = true;

            fetch(`${rutaRelativa}asignar-turnos/${turnoId}`)
                .then(response => response.json())
                .then(turno => {
                    document.getElementById('supervisorSelect').value = turno.supervisor_id;
                    document.getElementById('sedeInput').value = turno.sede.nombre;
                    document.getElementById('fechaInicioInput').value = turno.fecha_inicio;
                    document.getElementById('fechaFinInput').value = turno.fecha_fin;
                    document.getElementById('turnoSelect').value = turno.turno_id;

                    $('#asignarTurnoModal').modal('show');
                })
                .catch(error => console.error('Error fetching turno:', error));
        }
    });

    function cargarSupervisores() {
        fetch(`${rutaRelativa}supervisores`)
            .then(response => response.json())
            .then(data => {
                supervisorSelect.innerHTML = '<option value="">Seleccione un supervisor</option>';
                data.forEach(supervisor => {
                    const option = document.createElement('option');
                    option.value = supervisor.id;
                    option.textContent = `${supervisor.nombres} ${supervisor.apellidos}`;
                    supervisorSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching supervisores:', error));
    }

    function cargarTurnos() {
        fetch(`${rutaRelativa}turnos`)
            .then(response => response.json())
            .then(data => {
                turnoSelect.innerHTML = '<option value="">Seleccione un turno</option>';
                data.data.forEach(turno => { //no modificar
                    const option = document.createElement('option');
                    option.value = turno.id;
                    option.textContent = turno.nombre;
                    turnoSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching turnos:', error));
    }

    // Load supervisors and shifts
    cargarSupervisores();
    cargarTurnos();
});