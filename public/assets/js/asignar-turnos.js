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
    const tareasModal = document.getElementById('tareasModal');
    const supervisorNombre = document.getElementById('supervisorNombre');
    const sedeNombre = document.getElementById('sedeNombre');
    const tareasTableBody = document.getElementById('tareasTableBody');
    const nuevaTareaNombre = document.getElementById('nuevaTareaNombre');
    const nuevaTareaDescripcion = document.getElementById('nuevaTareaDescripcion');
    const guardarTareaBtn = document.getElementById('guardarTareaBtn');
    const volverBtn = document.getElementById('volverBtn');
    const agregarTareaBtn = document.getElementById('agregarTareaBtn');
    let editMode = false;
    let turnoId = null;
    let assignedTurnoId = null;

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

    sedeSelect.addEventListener('change', function() {
        const selectedOption = sedeSelect.options[sedeSelect.selectedIndex];
        sedeInput.value = selectedOption.textContent;
    });

    consultarBtn.addEventListener('click', function() {
        const sedeId = sedeSelect.value;
        fetch(`asignar-turnos/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {
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
                        <td><img src="assets/icons/editar.png" alt="Editar" class="icono-editar editarTurnoBtn" data-id="${turno.id}"></td>
                    `;
                    turnosTableBody.appendChild(row);
                });
                nuevaAsignacionBtn.style.display = 'inline-block';
            })
            .catch(error => console.error('Error fetching turnos:', error));
    });

    function cargarActividades(turnoId) {
        fetch(`asignar-turnos/tareas/${turnoId}`)
            .then(response => response.json())
            .then(data => {
                tareasTableBody.innerHTML = '';
                if (data.tareas.length > 0) {
                    data.tareas.forEach(actividad => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${actividad.id}</td>
                            <td>${actividad.nombre}</td>
                            <td>${actividad.descripcion}</td>
                            <td><button class="btn-eliminar" data-id="${actividad.id}">Eliminar</button></td>
                        `;
                        tareasTableBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="4">No se encontraron actividades.</td>
                    `;
                    tareasTableBody.appendChild(row);
                }
            })
            .catch(error => {
                console.error('Error al cargar actividades:', error);
                tareasTableBody.innerHTML = `
                    <tr>
                        <td colspan="4">No se encontraron actividades.</td>
                    </tr>
                `;
            });
    }

    guardarTurnoBtn.addEventListener('click', function() {
        const formData = new FormData();
        formData.append('supervisor_id', supervisorSelect.value);
        formData.append('sede_id', sedeSelect.value);
        formData.append('turno_id', turnoSelect.value);
        formData.append('fecha_inicio', fechaInicioInput.value);
        formData.append('fecha_fin', fechaFinInput.value);

        const url = editMode ? `asignar-turnos/actualizar/${turnoId}` : `asignar-turnos/guardar`;
        const method = editMode ? 'PUT' : 'POST';

        if (editMode) {
            formData.append('_method', 'PUT');
        }

        const validateAndSave = () => {
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showAlertModal(
                    "ok.png", // Ruta del ícono de éxito
                    data.message // Mensaje de éxito
                );
                $('#asignarTurnoModal').modal('hide');
                consultarBtn.click();

                // Store the assigned turno ID
                assignedTurnoId = turnoSelect.value;

                // Open the modal for assigned tasks
                fetch(`asignar-turnos/tareas/${assignedTurnoId}`)
                    .then(response => response.json())
                    .then(data => {
                        supervisorNombre.value = data.supervisor.nombres;
                        sedeNombre.value = data.sede.nombre;
                        cargarActividades(assignedTurnoId);
                        $('#tareasModal').modal('show');
                    })
                    .catch(error => console.error('Error fetching tareas:', error));
            })
            .catch(error => console.error('Error saving turno:', error));
        };

        if (editMode) {
            validateAndSave();
        } else {
            // Validate if the supervisor already has the shift assigned
            fetch(`asignar-turnos/validar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    supervisor_id: supervisorSelect.value,
                    sede_id: sedeSelect.value,
                    turno_id: turnoSelect.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    showAlertModal(
                        "error.png", // Ruta del ícono de error
                        "El supervisor ya tiene asignado este turno en la sede seleccionada." // Mensaje de error
                    );
                } else {
                    validateAndSave();
                }
            })
            .catch(error => console.error('Error validating turno:', error));
        }
    });

    agregarTareaBtn.addEventListener('click', function() {
        const formData = new FormData();
        formData.append('turno_id', assignedTurnoId);
        formData.append('nombre', nuevaTareaNombre.value);
        formData.append('descripcion', nuevaTareaDescripcion.value);

        fetch(`actividades`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showAlertModal(
                "ok.png", // Ruta del ícono de éxito
                data.message // Mensaje de éxito
            );
            nuevaTareaNombre.value = '';
            nuevaTareaDescripcion.value = '';
            cargarActividades(assignedTurnoId);
        })
        .catch(error => console.error('Error saving tarea:', error));
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

            fetch(`asignar-turnos/${turnoId}`)
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
        } else if (event.target.classList.contains('verTareasBtn')) {
            turnoId = event.target.getAttribute('data-id');

            fetch(`asignar-turnos/tareas/${turnoId}`)
                .then(response => response.json())
                .then(data => {
                    supervisorNombre.value = data.supervisor.nombre;
                    sedeNombre.value = data.sede.nombre;
                    tareasTableBody.innerHTML = '';
                    data.tareas.forEach(tarea => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${tarea.id}</td>
                            <td>${tarea.nombre}</td>
                            <td>${tarea.descripcion}</td>
                            <td><button class="btn-eliminar" data-id="${tarea.id}">Eliminar</button></td>
                        `;
                        tareasTableBody.appendChild(row);
                    });
                    $('#tareasModal').modal('show');
                })
                .catch(error => console.error('Error fetching tareas:', error));
        }
    });

    volverBtn.addEventListener('click', function() {
        $('#tareasModal').modal('hide');
        $('#asignarTurnoModal').modal('show');
    });

    function cargarSupervisores() {
        fetch(`supervisores`)
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
        fetch(`turnos`)
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