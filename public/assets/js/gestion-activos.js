document.addEventListener('DOMContentLoaded', function() {

    const clienteSelect = document.getElementById('clienteSelect');
    const sedeSelect = document.getElementById('sedeSelect');
    const consultarBtn = document.getElementById('consultarBtn');
    const sedeInput = document.getElementById('sedeInput');
    const pillsTab = document.getElementById('pills-tab');
    const pillsTabContent = document.getElementById('pills-tabContent');
    const cerarActivoBtn = document.getElementById('cerarActivoBtn');
    const guardarActivoBtn = document.getElementById('guardarActivoBtn');
    const activoIdInput = document.getElementById('activoId');
    const crearActivoModalLabel = document.getElementById('crearActivoModalLabel');
    const clienteInput = document.getElementById('clienteInput');
    const codigoInput = document.getElementById('codigoInput');
    const cantidadInput = document.getElementById('cantidadInput');
    const activoSelect = document.getElementById('activoSelect');
    const imagenesInput = document.getElementById('imagenesInput'); 
    const imagenesPreview = document.getElementById('imagenesPreview');
    const crearActivoModal = document.getElementById('crearActivoModal'); 

    let activoId = null;
    let assignedActivoId = null;
    let editMode = false;

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
        
        fetch(`gestionar-activos/consultar?sede_id=${sedeId}`)
            .then(response => response.json())
            .then(data => {                
               activosTableBody.innerHTML = '';
               data.data.forEach(activo => {
                   const row = document.createElement('tr');                    
                   const estadoClase = activo.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                        <td>${activo.id}</td>
                        <td>${activo.activo.nombre_elemento}</td>
                        <td>${activo.cantidad}</td>
                        <td>${activo.estados?.nombre}</td>
                        <td>${activo.sede.nombre}</td>
                        <td>${activo.sede.cliente.nombre}</td>
                        <td><div class="${estadoClase}">${activo.estado ? 'Activo' : 'Inactivo'}</div></td>
                        <td>${activo.creador?.nombres || 'N/A'}</td>
                    <td>${formatDate(activo.created_at)}</td>
                    <td>${activo.actualizador?.nombres || 'N/A'}</td>
                    <td>${formatDate(activo.updated_at)}</td>
                    <td><img src="assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${activo.id}"></td>
                    `;
                    activosTableBody.appendChild(row);
                });
                pillsTab.style.display = 'flex';
                pillsTabContent.style.display = 'block';
            })
            .catch(error => console.error('Error fetching activos:', error));
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('icono-editar')) {
            activoId = event.target.getAttribute('data-id');
            if (!activoId) {
                console.error("Error: No se encontró el ID del activo en el botón.");
                return;
            }

            editMode = true;
            crearActivoModalLabel.textContent = "Editar Activo";
            guardarActivoBtn.textContent = "Guardar Cambios";

            fetch(`gestionar-activo/consultar?id=${activoId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos del activo.");
                    }
                    return response.json();
                })
                .then((activo) => {
                    console.log("Datos del activo:", activo); // Log the fetched data

                    // Populate form fields with activo data
                    const activoSelect = document.getElementById('activoSelect');
                    activoSelect.value = activo.activo_id;
                    clienteInput.value = activo.sede.cliente.nombre;
                    sedeInput.value = activo.sede.nombre;
                    const estadoSelect = document.getElementById("estadoActivo");
                    estadoSelect.value = activo.estado_id;
                    codigoInput.value = activo.activo.serie;
                    cantidadInput.value = activo.cantidad;
                    
                    // ...populate other fields...

                    // Clear previous image previews
                    imagenesPreview.innerHTML = '';

                    // Populate image previews
                    activo.imagenes.forEach(imagen => {
                        const imgContainer = document.createElement('div');
                        imgContainer.classList.add('img-container');
                        const img = document.createElement('img');                      
                        img.src = `${imagen.imagen}`; // Ensure the full URL is used
                        img.classList.add('img-thumbnail', 'mr-2', 'mb-2');
                        img.style.width = '100px';
                        img.style.height = '100px';
                        const removeBtn = document.createElement('button');
                        removeBtn.textContent = 'X';
                        removeBtn.classList.add('remove-btn');
                        removeBtn.addEventListener('click', function() {
                            imgContainer.remove();
                            // Optionally, handle image removal from the server here
                        });
                        imgContainer.appendChild(img);
                        imgContainer.appendChild(removeBtn);
                        imagenesPreview.appendChild(imgContainer);
                    });

                    $(crearActivoModal).modal('show');// Ensure jQuery is used to show the modal
                })
                .catch((error) => console.error("Error al cargar los datos del activo:", error));
        }
    });

    guardarActivoBtn.addEventListener('click', function() {
        const url = editMode ? `gestionar-activos/actualizar/${activoId}` : `gestionar-activos/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(document.getElementById('crearActivoForm'));
        formData.append("estado", document.getElementById("estadoActivoToggle").checked ? 1 : 0);
        formData.append('sede_id', sedeSelect.value);

        // Append only the first image to formData
        if (imagenesInput.files.length > 0) {
            formData.append('imagenesInput', imagenesInput.files[0]);
        }

        if (editMode) {
            formData.append("_method", "PUT");
        }

        fetch(url, {
                method: "POST", // Always use POST for FormData
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData,
            })
            .then((response) => {
                if (!response.ok) {
                    return response.json().then((data) => {
                        throw data;
                    });
                }
                return response.json();
            })
            .then((data) => {
                if (data.errors) {
                    showActivoErrors(data.errors);
                } else {
                    showAlertModal(
                        "ok.png", // Ruta del ícono de éxito
                        data.message // Mensaje de éxito
                    );
                    $(crearActivoModal).modal('hide'); // Use jQuery to hide the modal
                    resetActivoForm();
                    consultarActivos(); // Reload the table
                }
            })
            .catch((error) => {
                if (error.errors) {
                    showActivoErrors(error.errors);
                } else {
                    console.error("Error al guardar el activo:", error);
                }
            });
    });

    function showActivoErrors(errors) {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        for (const [key, messages] of Object.entries(errors)) {
            const errorElement = document.getElementById(`error${capitalizeFirstLetter(key)}`);
            if (errorElement) {
                errorElement.textContent = messages.join(', ');
            }
        }
    }

    function resetActivoForm() {
        document.getElementById('crearActivoForm').reset();
        crearActivoModalLabel.textContent = "Crear nuevo Activo";
        guardarActivoBtn.textContent = "Crear Activo";
        editMode = false;
        activoId = null;
        imagenesPreview.innerHTML = ''; // Clear image previews
        imagenesInput.value = ''; // Clear image input
    }

  

    cerarActivoBtn.addEventListener('click', function() {
        editMode = false;
        activoId = null;
        document.getElementById('crearActivoForm').reset();
        sedeInput.value = sedeSelect.options[sedeSelect.selectedIndex].textContent; 
        clienteInput.value = clienteSelect.options[clienteSelect.selectedIndex].textContent; 
        imagenesPreview.innerHTML = ''; // Clear image previews
        imagenesInput.value = ''; // Clear image input
        $(crearActivoModal).modal('show'); // Use jQuery to show the modal
    });

    function formatDate(dateString) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    function cargarActivos() {
        fetch(`activos`)
            .then(response => response.json())
            .then(activos => {
                const activoSelect = document.getElementById('activoSelect');
                activoSelect.innerHTML = '<option value="">Seleccione un activo</option>';
                activos.forEach(activo => {
                    const option = document.createElement('option');
                    option.value = activo.id;
                    option.textContent = activo.nombre_elemento;
                    activoSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar los activos:', error));
    }

    activoSelect.addEventListener('change', function() {
        
        const activoId = this.value;
        fetch(`activo?id=${activoId}`)
        .then(response => response.json())
        .then(data => {
               codigoInput.value = data.serie;                
            })
            .catch(error => console.error('Error fetching sedes:', error));
            
    });

    function cargarEstados() {
        fetch(`estados`)
            .then(response => response.json())
            .then(estados => {                
                const estadoSelect = document.getElementById("estadoActivo");
                estadoSelect.innerHTML = '<option value="">Seleccione</option>';
                estados.forEach(estado => {
                    const option = document.createElement("option");
                    option.value = estado.id;
                    option.textContent = estado.nombre;
                    estadoSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los estados:", error));
    }

    cargarActivos();
    cargarEstados()

    imagenesInput.addEventListener('change', function() {
        
        imagenesPreview.innerHTML = ''; // Clear previous previews
        const dt = new DataTransfer();
        for (const file of imagenesInput.files) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgContainer = document.createElement('div');
                imgContainer.classList.add('img-container');
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('img-thumbnail', 'mr-2', 'mb-2');
                img.style.width = '100px';
                img.style.height = '100px';
                const removeBtn = document.createElement('button');
                removeBtn.textContent = 'X';
                removeBtn.classList.add('remove-btn');
                removeBtn.addEventListener('click', function() {
                    imgContainer.remove();
                    // Remove the file from the input
                    for (const fileItem of imagenesInput.files) {
                        if (fileItem !== file) {
                            dt.items.add(fileItem);
                        }
                    }
                    imagenesInput.files = dt.files;
                });
                imgContainer.appendChild(img);
                imgContainer.appendChild(removeBtn);
                imagenesPreview.appendChild(imgContainer);
                dt.items.add(file); // Add file to DataTransfer
            };
            reader.readAsDataURL(file);
        }
        imagenesInput.files = dt.files; // Update input files
    });

});