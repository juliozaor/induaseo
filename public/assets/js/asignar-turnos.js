document.addEventListener('DOMContentLoaded', function () {
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
    const tareasTableBody = document.getElementById('tareasTableBody');
    const volverBtn = document.getElementById('volverBtn');
    const agregarTareaBtn = document.getElementById('agregarTareaBtn');
    const nuevaAreaSelect = document.getElementById('nuevaAreaSelect');
    const areaSection = document.getElementById("areaSection");
    const guardarFinalizarBtn = document.getElementById("guardarFinalizarBtn");
    const asignarTurnoModalLabel = document.getElementById("asignarTurnoModalLabel")
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);
    const registrosTurnoPorPagina = document.getElementById('registrosTurnoPorPagina');
    let editMode = false;
    let turnoId = null;
    let assignedTurnoId = null;

    const asignarTurnoModal = document.getElementById('asignarTurnoModal');

    // Evento para reiniciar el modal al cerrarlo
    $('#asignarTurnoModal').on('hidden.bs.modal', function () {
        document.getElementById('asignarTurnoForm').reset();
        areaSection.style.display = "none";
        tareasTableBody.innerHTML = '';
    });

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

        // Cargar áreas correspondientes a la sede seleccionada
        const sedeId = sedeSelect.value;
        fetch(`areas-por-sede/${sedeId}`)
            .then(response => {
                console.log(response); // Agregar este console.log para ver la respuesta completa
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log(data); // Agregar este console.log para ver los datos
                nuevaAreaSelect.innerHTML = '<option value="">Seleccione un área</option>';
                data.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.id;
                    option.textContent = area.nombre;
                    nuevaAreaSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching áreas:', error));
    });

    consultarBtn.addEventListener('click', function () {
        consultarTurnosAsignados(1);
    });

    function consultarTurnosAsignados(page = 1, $isEliminar = true) {
        if ($isEliminar) {
            cargarSupervisores();
            cargarTurnos();
        }

        const sedeId = sedeSelect.value;
        const registrosPorPagina = registrosTurnoPorPagina.value;

        fetch(`asignar-turnos/consultar?sede_id=${sedeId}&page=${page}&registros_por_pagina=${registrosPorPagina}`)
            .then(response => response.json())
            .then(data => {
                turnosTableBody.innerHTML = '';
                data.data.forEach(turno => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${turno.fecha_inicio}</td>
                        <td>${turno.fecha_fin}</td>
                        <td>${turno.supervisor.numero_documento}</td>
                        <td>${turno.supervisor.nombres} ${turno.supervisor.apellidos}</td>
                        <td>${turno.turno.nombre}</td>
                        <td>${turno.areas_count}</td>
                        <td>
                            <img src="assets/icons/editar.png" alt="Editar" class="icono-editar editarTurnoBtn" data-id="${turno.id}">
                            <img src="assets/icons/eliminar.png" alt="Eliminar" class="icono-eliminar" data-id="${turno.id}">
                        </td>
                    `;
                    turnosTableBody.appendChild(row);
                });

                // Mostrar total de registros
                const registrosEncontrados = document.querySelector('.registros-encontrados');
                if (registrosEncontrados) {
                    registrosEncontrados.textContent = `Total: ${data.total}`;
                }

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
                    consultarTurnosAsignados(current_page - 1);
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
                        consultarTurnosAsignados(i);
                    });

                    paginacionContainer.appendChild(pageButton);
                }

                // Botón de página siguiente
                const nextButton = document.createElement("button");
                nextButton.textContent = "Sig.";
                nextButton.classList.add("page-button", "sig");
                nextButton.disabled = current_page === last_page;
                nextButton.addEventListener('click', () => {
                    consultarTurnosAsignados(current_page + 1);
                });
                paginacionContainer.appendChild(nextButton);

                nuevaAsignacionBtn.style.display = 'inline-block';
            })
            .catch(error => console.error('Error fetching turnos:', error));
    }

    guardarTurnoBtn.addEventListener('click', function () {
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
                    console.log(data);
                    turnoId = data.turno_id; // Almacenar el ID del turno asignado
                    document.getElementById('asignarTurnoModal').setAttribute('data-turno-id', turnoId); // Almacenar el turnoId en el modal
                    areaSection.style.display = "block"; // Mostrar la sección de áreas
                    showAlertModal(
                        "ok.png", // Ruta del ícono de éxito
                        data.message // Mensaje de éxito
                    );
                })
                .catch(error => console.error('Error al guardar el turno:', error));
        };

        if (editMode) {
            validateAndSave();
        } else {
            // Validar si el supervisor ya tiene el turno asignado
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
                .catch(error => console.error('Error al validar el turno:', error));
        }
    });

    agregarTareaBtn.addEventListener('click', function () {
        const turnoId = document.getElementById('asignarTurnoModal').getAttribute('data-turno-id'); // Obtener el turnoId del modal
        if (!turnoId) {
            console.error('Error: turnoId es nulo.');
            return;
        }

        const formData = new FormData();
        formData.append('turno_id', turnoId);
        formData.append('area_id', nuevaAreaSelect.value);

        fetch(`turnos_areas/area`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                showAlertModal(
                    "ok.png", // Ruta del ícono de éxito
                    data.message // Mensaje de éxito
                );
                nuevaAreaSelect.value = '';
                cargarAreas(turnoId);
            })
            .catch(error => console.error('Error al guardar el área:', error));
    });

    guardarFinalizarBtn.addEventListener("click", function () {
        $('#asignarTurnoModal').modal('hide'); // Cerrar el modal
        resetModal(); // Reiniciar los valores del modal
        consultarTurnosAsignados(); // Consultar turnos asignados
        showAlertModal(
            "ok.png", // Ruta del ícono de éxito
            "Guardado con éxito" // Mensaje de éxito
        );
    });

    function resetModal() {
        document.getElementById('asignarTurnoForm').reset();
        areaSection.style.display = "none";
        tareasTableBody.innerHTML = '';
    }

    nuevaAsignacionBtn.addEventListener('click', function () {
        editMode = false;
        turnoId = null;
        asignarTurnoModalLabel.textContent = "Asignar Turno";
        document.getElementById('asignarTurnoForm').reset();
        sedeInput.value = sedeSelect.options[sedeSelect.selectedIndex].textContent; // Set the sede input value
        nuevaAreaSelect.value = '';
        $('#asignarTurnoModal').modal('show');
    });

    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('editarTurnoBtn')) {
            turnoId = event.target.getAttribute('data-id');
            document.getElementById('asignarTurnoModal').setAttribute('data-turno-id', turnoId); // Almacenar el turnoId en el modal
            asignarTurnoModalLabel.textContent = "Editar Turno Asignado #"+turnoId;
            console.log('Id: ', turnoId)
            editMode = true;

            fetch(`asignar-turnos/${turnoId}`)
                .then(response => response.json())
                .then(turno => {
                    document.getElementById('supervisorSelect').value = turno.supervisor_id;
                    document.getElementById('sedeInput').value = turno.sede.nombre;
                    document.getElementById('fechaInicioInput').value = turno.fecha_inicio;
                    document.getElementById('fechaFinInput').value = turno.fecha_fin;
                    document.getElementById('turnoSelect').value = turno.turno_id;

                    // Mostrar areaSection
                    areaSection.style.display = "block";

                    // Cargar áreas asignadas al turno
                    cargarAreas(turnoId);

                    $('#asignarTurnoModal').modal('show');
                })
                .catch(error => console.error('Error fetching turno:', error));
        }
        /* if (event.target.classList.contains('icono-eliminar')) {
            const turnoId = event.target.getAttribute('data-id');
            eliminarRelacionAreaTurno(turnoId);
        } */

        if (event.target.classList.contains('icono-eliminar')) {
            const turnoId = event.target.getAttribute('data-id');
            if (!turnoId) {
                console.error("Error: No se encontró el ID del turno en el botón.");
                return;
            }

            if (confirm("¿Está seguro de que desea eliminar este turno asignado?")) {
                fetch(`asignar-turnos/eliminar/${turnoId}`, {
                    method: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error al eliminar el turno asignado.");
                    }
                    return response.json();
                })
                .then(data => {
                    alert("Turno asignado eliminado con éxito.");
                    consultarTurnosAsignados(); // Reload the table
                })
                .catch(error => console.error("Error al eliminar el turno asignado:", error));
            }
        }
    });

    function eliminarRelacionAreaTurno(turnoId, areaId) {
        fetch(`turnos_areas/eliminar/${turnoId}/${areaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                console.log(response); // Agregar este console.log para ver la respuesta completa
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                showAlertModal(
                    "ok.png", // Ruta del ícono de éxito
                    data.message // Mensaje de éxito
                );
                cargarAreas(turnoId);
                //consultarTurnosAsignados(false);
            })
            .catch(error => console.error('Error deleting área:', error));
    }

    function cargarAreas(turnoId) {
        fetch(`asignar-turnos/areas/${turnoId}`)
            .then(response => response.json())
            .then(data => {
                tareasTableBody.innerHTML = '';
                data.forEach(area => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${area.id}</td>
                        <td>${area.nombre}</td>
                        <td><button class="btn-eliminar" data-id="${area.id}">Eliminar</button></td>
                    `;
                    tareasTableBody.appendChild(row);
                });

                // Agregar evento de escucha para los botones de eliminar
                document.querySelectorAll('.btn-eliminar').forEach(button => {
                    button.addEventListener('click', function () {
                        const areaId = this.getAttribute('data-id');
                        eliminarRelacionAreaTurno(turnoId, areaId);
                    });
                });
            })
            .catch(error => console.error('Error al cargar las áreas:', error));
    }

    volverBtn.addEventListener('click', function () {
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
