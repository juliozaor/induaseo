document.addEventListener("DOMContentLoaded", function () {
    const consultarBtn = document.getElementById("consultarBtn");
    const tablaMaestraSelect = document.getElementById("tablaMaestraSelect");
    const tablaClientesBody = document.querySelector("#tablaClientes tbody");
   

    consultarBtn.addEventListener("click", function () {
        const tablaSeleccionada = tablaMaestraSelect.value;

        if (!tablaSeleccionada) {
            alert("Por favor, seleccione una tabla maestra.");
            return;
        }

        const formData = new FormData();
        formData.append('tabla', tablaSeleccionada);
        
        fetch('{{ route('maestras.consultar') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })

        .then(response => {
            console.log(response);
            
            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Limpiar la tabla
            tablaClientesBody.innerHTML = '';

            // Llenar la tabla con datos
            data.forEach(cliente => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${cliente.tipo_documento?.nombre || 'N/A'}</td>
                    <td>${cliente.numero_documento}</td>
                    <td>${cliente.ciudad?.pais?.nombre || 'N/A'}</td>
                    <td>${cliente.ciudad?.nombre || 'N/A'}</td>
                    <td>${cliente.sector_economico?.nombre || 'N/A'}</td>
                    <td>${cliente.nombre}</td>
                    <td>${cliente.direccion}</td>
                    <td>${cliente.correo}</td>
                    <td>${cliente.celular}</td>
                    <td>${cliente.estado ? 'Activo' : 'Inactivo'}</td>
                    <td><button class="btn-editar">Editar</button></td>
                `;
                tablaClientesBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));
    });
});
