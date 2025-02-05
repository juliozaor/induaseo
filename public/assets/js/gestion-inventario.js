document.addEventListener('DOMContentLoaded', function () {

    // Elementos del DOM
    const clienteSelect = document.getElementById('clienteSelect');
    const sedeSelect = document.getElementById('sedeSelect');
    //const consultarBtn = document.getElementById('consultarBtn');
    const sedeInput = document.getElementById('sedeInput');
    const pillsTab = document.getElementById('pills-tab');
    const pillsTabContent = document.getElementById('pills-tabContent');
    const crearInventarioBtn = document.getElementById('crearInventarioBtn');
    const guardarInventarioBtn = document.getElementById('guardarInventarioBtn');
    const crearInventarioModalLabel = document.getElementById('crearInventarioModalLabel');
    const clienteInput = document.getElementById('clienteInput');
    const codigoInput = document.getElementById('codigoInput');
    const cantidadInput = document.getElementById('cantidadInput');
    const itemSelect = document.getElementById('itemSelect');
    const imagenesInput = document.getElementById('imagenesInput');
    const imagenesPreview = document.getElementById('imagenesPreview');
    const crearInventarioModal = document.getElementById('crearInventarioModal');
    const selectedOptionsContainer = document.getElementById('selectedOptions');
    const cantidadDisponibleSpan = document.getElementById('cantidadDisponible');

    const btnQuitarSede = document.getElementById('btnQuitarSede')

    let inventarioId = null;
    let editMode = false;
    let selectedOptions = [];

    // Función para consultar inventarios
    function consultarInventarios() {
        fetch(`gestionar-inventario/consultar`)
            .then(response => response.json())
            .then(data => {
                inventariosTableBody.innerHTML = '';
                data.data.forEach(inventario => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${inventario.id}</td>
                        <td>${inventario.nombre}</td>
                        <td>${inventario.cantidad}</td>
                        <td>${inventario.sede}</td>
                        <td>${inventario.cliente}</td>
                        <td>${inventario.creado_por}</td>
                        <td>${inventario.ultima_actualizacion}</td>
                        <td>${inventario.editado_por}</td>
                        <td><img src="assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${inventario.id}"></td>
                    `;
                    inventariosTableBody.appendChild(row);
                });
                pillsTab.style.display = 'flex';
                pillsTabContent.style.display = 'block';
            })
            .catch(error => console.error('Error fetching inventarios:', error));
    }

    // Consultar inventarios al cargar la página
    consultarInventarios();

    // Evento para cargar las sedes al seleccionar un cliente
    clienteSelect.addEventListener('change', function () {
        const clienteId = this.value;

        // Reset selectedOptions and clear the selectedOptionsContainer
        selectedOptions = [];
        selectedOptionsContainer.innerHTML = '';

        fetch(`sedes?cliente_id=${clienteId}`)
            .then(response => response.json())
            .then(data => {
                sedeSelect.innerHTML = '<option value="">Seleccione una o varias sedes</option>';
                data.forEach(sede => {
                    const option = document.createElement('option');
                    option.value = sede.id;
                    option.textContent = sede.nombre;
                    sedeSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching sedes:', error));
    });

    // Evento para actualizar el input de sede al seleccionar una sede
    sedeSelect.addEventListener('change', function () {
        const selectedValue = sedeSelect.value;
        const selectedText = sedeSelect.options[sedeSelect.selectedIndex].text;
        console.log(selectedValue, selectedText)

        if (selectedValue && !selectedOptions.find(opt => opt.value === selectedValue)) {
            selectedOptions.push({ value: selectedValue, text: selectedText });
            renderSelectedOptions();
        }

        // Eliminar la opción seleccionada del select
        sedeSelect.options[sedeSelect.selectedIndex].style.display = 'none';
        sedeSelect.value = '';
    });

    function renderSelectedOptions() {
        selectedOptionsContainer.innerHTML = '';
        selectedOptions.forEach((option, index) => {
            const badge = document.createElement('span');
            badge.className = 'badge';
            badge.innerHTML = `
                ${option.text}
                <button onclick="removeOption(${index})" type="button" class="btn-close ms-2" aria-label="Remove" >X</button>
            `;
            selectedOptionsContainer.appendChild(badge);
        });
    }

    /* btnQuitarSede.addEventListener('click', function () {
        console.log('remover')
    }); */

    // Definir la función removeOption en el ámbito global
    window.removeOption = function(index) {
        // console.log('remover')
        const removedOption = selectedOptions.splice(index, 1)[0];
        renderSelectedOptions();

        // Volver a mostrar la opción eliminada en el select
        const optionToShow = Array.from(sedeSelect.options).find(opt => opt.value === removedOption.value);
        if (optionToShow) {
            optionToShow.style.display = 'block';
        }
    }

    // Evento para consultar inventarios al hacer clic en el botón de consultar
    /* consultarBtn.addEventListener('click', function () {
        consultarInventarios();
    }); */

    // Evento para editar un inventario al hacer clic en el icono de editar
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('icono-editar')) {
            inventarioId = event.target.getAttribute('data-id');
            if (!inventarioId) {
                console.error("Error: No se encontró el ID del inventario en el botón.");
                return;
            }

            editMode = true;
            crearInventarioModalLabel.textContent = "Editar Inventario";
            guardarInventarioBtn.textContent = "Guardar Cambios";

            fetch(`gestionar-inventario/obtener?id=${inventarioId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos del inventario.");
                    }
                    return response.json();
                })
                .then((inventario) => {
                    console.log("Datos del inventario:", inventario); // Log the fetched data

                    // Populate form fields with inventario data
                    itemSelect.value = inventario.item_id;
                    clienteSelect.value = inventario.sede.cliente.id;
                    sedeSelect.value = inventario.sede.id;
                    const estadoSelect = document.getElementById("estadoInventario");
                    estadoSelect.value = inventario.estado_id;
                    codigoInput.value = inventario.numero_serie;
                    cantidadInput.value = inventario.cantidad;

                    // Clear previous image previews
                    imagenesPreview.innerHTML = '';

                    // Populate image previews
                    inventario.imagenes.forEach(imagen => {
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
                        removeBtn.addEventListener('click', function () {
                            imgContainer.remove();
                            // Optionally, handle image removal from the server here
                        });
                        imgContainer.appendChild(img);
                        imgContainer.appendChild(removeBtn);
                        imagenesPreview.appendChild(imgContainer);
                    });

                    $(crearInventarioModal).modal('show');// Ensure jQuery is used to show the modal
                })
                .catch((error) => console.error("Error al cargar los datos del inventario:", error));
        }
    });

    // Evento para guardar un inventario al hacer clic en el botón de guardar
    guardarInventarioBtn.addEventListener('click', function () {
        const url = editMode ? `gestionar-inventario/actualizar/${inventarioId}` : `gestionar-inventario/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(document.getElementById('crearInventarioForm'));
        formData.append('sede_id', sedeSelect.value);

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
                    showInventarioErrors(data.errors);
                } else {
                    showAlertModal(
                        "ok.png", // Ruta del ícono de éxito
                        data.message // Mensaje de éxito
                    );
                    $(crearInventarioModal).modal('hide'); // Use jQuery to hide the modal
                    resetInventarioForm();
                    consultarInventarios(); // Reload the table
                }
            })
            .catch((error) => {
                if (error.errors) {
                    showInventarioErrors(error.errors);
                } else {
                    console.error("Error al guardar el inventario:", error);
                }
            });
    });

    // Función para mostrar errores en el formulario de inventario
    function showInventarioErrors(errors) {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        for (const [key, messages] of Object.entries(errors)) {
            const errorElement = document.getElementById(`error${capitalizeFirstLetter(key)}`);
            if (errorElement) {
                errorElement.textContent = messages.join(', ');
            }
        }
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Función para resetear el formulario de inventario
    function resetInventarioForm() {
        document.getElementById('crearInventarioForm').reset();
        crearInventarioModalLabel.textContent = "Crear nuevo Inventario";
        guardarInventarioBtn.textContent = "Crear Inventario";
        editMode = false;
        inventarioId = null;
        imagenesPreview.innerHTML = ''; // Clear image previews
        imagenesInput.value = ''; // Clear image input
    }

    // Evento para abrir el modal de crear inventario
    crearInventarioBtn.addEventListener('click', function () {
        editMode = false;
        inventarioId = null;
        document.getElementById('crearInventarioForm').reset();
        clienteSelect.options[clienteSelect.selectedIndex].textContent;
        sedeSelect.options[sedeSelect.selectedIndex].textContent;
        //imagenesPreview.innerHTML = ''; // Clear image previews
        //imagenesInput.value = ''; // Clear image input
        cargarItems(); // Cargar los artículos al abrir el modal
        $(crearInventarioModal).modal('show'); // Use jQuery to show the modal
    });

    // Función para formatear fechas
    function formatDate(dateString) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    // Función para cargar los items
    function cargarItems() {
        fetch(`items`)
            .then(response => response.json())
            .then(items => {
                itemSelect.innerHTML = '<option value="">Seleccione un item</option>';
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nombre_elemento;
                    itemSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar los items:', error));
    }

    // Evento para cargar el código del item seleccionado y la cantidad disponible
    itemSelect.addEventListener('change', function () {
        const itemId = this.value;
        fetch(`obtener-item/${itemId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('La respuesta de la red no fue satisfactoria');
                }
                return response.json();
            })
            .then(data => {
                codigoInput.value = data.codigo;
                cantidadDisponibleSpan.textContent = `Disponible: ${data.cantidad}`;
            })
            .catch(error => console.error('Error fetching items:', error));
    });

    document.addEventListener('DOMContentLoaded', function () {
        const itemSelect = document.getElementById('itemSelect');
        const codigoInput = document.getElementById('codigoInput');

        itemSelect.addEventListener('change', function () {
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            if (selectedOption) {
                codigoInput.value = selectedOption.value;
            }
        });
    });
});
