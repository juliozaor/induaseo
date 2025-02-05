document.addEventListener('DOMContentLoaded', function() {
    const clienteSelect = document.getElementById('clienteSelect');
    const sedeSelect = document.getElementById('sedeSelect');
    const consultarBtn = document.getElementById('consultarBtn');
    const actividadesTableBody = document.getElementById('actividadesTableBody');

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
    });


});