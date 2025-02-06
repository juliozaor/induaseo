document.addEventListener('DOMContentLoaded', function() {
    const turnosTableBody = document.getElementById('turnosTableBody');   
    const registrosEncontrados = document.querySelector('.registros-encontrados');
    const paginacionContainer = document.createElement('div');
    const clienteSelect = document.getElementById('clienteSelect');
    const sedeSelect = document.getElementById('sedeSelect');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);

        
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

    function cargarAlertas(page = 1) {
        console.log('cargarAlertas');
        
        const buscar = document.getElementById("busquedaTurnoInput").value;
        const registrosPorPagina = document.getElementById("registrosTurnoPorPagina").value;
        const clienteId = clienteSelect.value;
        const sedeId = sedeSelect.value;

        const formData = new FormData();
        formData.append('buscar', buscar);
        formData.append('registros_por_pagina', registrosPorPagina);
        formData.append('cliente_id', clienteId);
        formData.append('sede_id', sedeId);

        fetch(`alertas/consultar?page=${page}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error(`Error en la solicitud: ${response.statusText}`);
                return response.json();
            })
            .then(data => {
                // Limpiar la tabla y la paginación
                turnosTableBody.innerHTML = '';
                paginacionContainer.innerHTML = '';


                // Convertir el objeto en un array
                const turnosArray = Object.values(data.data);

                // Llenar la tabla con los datos
                turnosArray.forEach(turno => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${turno.id}</td>
                        <td>${turno.alerta ? 'Tareas completadas' : 'Tareas sin completar'}</td>
                        <td>${turno.fecha}</td>
                        <td>${turno.actividades_completadas}</td>
                        <td>${turno.turno}</td>
                        <td>${turno.cliente}</td>
                        <td>${turno.sede}</td>
                        <td>${turno.supervisor}</td>
                        <td>${turno.celular}</td>
                        <td><img src="assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${turno.id}" data-turno='${JSON.stringify(turno)}'></td>
                    `;
                    turnosTableBody.appendChild(row);
                });

                // Mostrar total de registros
                registrosEncontrados.textContent = `Total: ${data.total}`;

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
                    cargarAlertas(current_page - 1);
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
                        cargarAlertas(i);
                    });

                    paginacionContainer.appendChild(pageButton);
                }

                // Botón de página siguiente
                const nextButton = document.createElement("button");
                nextButton.textContent = "Sig.";
                nextButton.classList.add("page-button", "sig");
                nextButton.disabled = current_page === last_page;
                nextButton.addEventListener('click', () => {
                    cargarAlertas(current_page + 1);
                });
                paginacionContainer.appendChild(nextButton);
            })
            .catch(error => console.error('Error:', error));
    }

    turnosTableBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('icono-editar')) {
            const turnoData = JSON.parse(event.target.getAttribute('data-turno'));
            document.getElementById('detalleAlerta').value = turnoData.alerta ? 'Tareas completadas' : 'Tareas sin completar';
            document.getElementById('detalleFecha').value = turnoData.fecha;
            document.getElementById('detalleActividades').value = turnoData.actividades_completadas;
            document.getElementById('detalleTurno').value = turnoData.turno;
            document.getElementById('detalleSede').value = turnoData.sede;
            document.getElementById('detalleSupervisor').value = turnoData.supervisor;
            document.getElementById('detalleCelular').value = turnoData.celular;
            document.getElementById('detalleCliente').value = turnoData.cliente;
            document.getElementById('detalleObservaciones').value = turnoData.observaciones;
          
            $('#detalleTurnoModal').modal('show');
        }
    });

    function cargarClientes() {
        fetch(`clientes-select`)
            .then(response => response.json())
            .then(clientes => {               
                    const clienteSelect1 = document.getElementById("clienteSelect");
                    clienteSelect1.innerHTML = '<option value="">Seleccione un cliente</option>';
                    clientes.forEach(cliente => {
                        const option = document.createElement("option");
                        option.value = cliente.id;
                        option.textContent = cliente.nombre;
                        clienteSelect1.appendChild(option);
                    });
               
            })

            .catch(error => console.error("Error al cargar los clientes:", error));
    }

    cargarAlertas(1);
    cargarClientes();

    // Add event listeners for filters
    clienteSelect.addEventListener('change', () => cargarAlertas(1));
    sedeSelect.addEventListener('change', () => cargarAlertas(1));
    document.getElementById("busquedaTurnoInput").addEventListener("input", () => cargarAlertas(1));
    document.getElementById("registrosTurnoPorPagina").addEventListener("change", () => cargarAlertas(1));
});