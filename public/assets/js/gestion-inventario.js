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

    const totalInventarios = document.getElementById('totalInventarios');
    //const paginacionContainer = document.getElementById('inventariosPaginacion');
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);
    const registrosInventarioPorPagina = document.getElementById('registrosInventarioPorPagina');
    const busquedaInventarioInput = document.getElementById('busquedaInventarioInput');

    let inventarioId = null;
    let editMode = false;
    let selectedOptions = [];

    // Función para consultar inventarios
    function consultarInventarios(page = 1) {
        const registrosPorPagina = registrosInventarioPorPagina.value;
        const buscar = busquedaInventarioInput.value;

        fetch(`gestionar-inventario/consultar?page=${page}&registros_por_pagina=${registrosPorPagina}&buscar=${buscar}`)
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
                        <td>${inventario.creado_por ?? ''}</td>
                        <td>${inventario.ultima_actualizacion}</td>
                        <td>${inventario.editado_por ?? ''}</td>
                        <td>
                            <img src="assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${inventario.id}">
                            <img src="assets/icons/eliminar.png" alt="Eliminar" class="icono-eliminar" data-id="${inventario.id}">
                        </td>
                    `;
                    inventariosTableBody.appendChild(row);
                });

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
                    consultarInventarios(current_page - 1);
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
                        consultarInventarios(i);
                    });

                    paginacionContainer.appendChild(pageButton);
                }

                // Botón de página siguiente
                const nextButton = document.createElement("button");
                nextButton.textContent = "Sig.";
                nextButton.classList.add("page-button", "sig");
                nextButton.disabled = current_page === last_page;
                nextButton.addEventListener('click', () => {
                    consultarInventarios(current_page + 1);
                });
                paginacionContainer.appendChild(nextButton);

                // Mostrar total de registros
                const registrosEncontrados = document.querySelector('.registros-encontrados');
                if (registrosEncontrados) {
                    registrosEncontrados.textContent = `Total: ${data.total}`;
                }

                //renderPagination(data);
                pillsTab.style.display = 'flex';
                pillsTabContent.style.display = 'block';
            })
            .catch(error => console.error('Error fetching inventarios:', error));
    }

    function renderPagination(data) {
        inventariosPaginacion.innerHTML = '';
        for (let i = 1; i <= data.last_page; i++) {
            const pageItem = document.createElement('button');
            pageItem.textContent = i;
            pageItem.classList.add('page-item');
            if (i === data.current_page) {
                pageItem.classList.add('active');
            }
            pageItem.addEventListener('click', () => consultarInventarios(i));
            inventariosPaginacion.appendChild(pageItem);
        }
    }

    // Consultar inventarios al cargar la página
    consultarInventarios();

    // Evento para cargar las sedes al seleccionar un cliente
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

    function cargarSedes(clienteId) {
        return fetch(`sedes?cliente_id=${clienteId}`)
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
    }

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
                    itemSelect.value = inventario.insumo_id;
                    clienteSelect.value = inventario.sede.cliente.id;
                    cargarSedes(inventario.sede.cliente.id).then(() => {
                        sedeSelect.value = inventario.sede_id;
                    });
                    codigoInput.value = inventario.item.codigo;
                    cantidadInput.value = inventario.cantidad;

                    // Clear previous image previews
                    imagenesPreview.innerHTML = '';

                    // Populate image previews if images exist
                    if (inventario.imagenes && inventario.imagenes.length > 0) {
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
                    }

                    $(crearInventarioModal).modal('show');// Ensure jQuery is used to show the modal
                })
                .catch((error) => console.error("Error al cargar los datos del inventario:", error));
        }

        if (event.target.classList.contains('icono-eliminar')) {
            const inventarioId = event.target.getAttribute('data-id');
            if (!inventarioId) {
                console.error("Error: No se encontró el ID del inventario en el botón.");
                return;
            }

            if (confirm("¿Está seguro de que desea eliminar este inventario?")) {
                fetch(`gestionar-inventario/eliminar/${inventarioId}`, {
                    method: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error al eliminar el inventario.");
                    }
                    return response.json();
                })
                .then(data => {
                    alert("Inventario eliminado con éxito.");
                    consultarInventarios(); // Reload the table
                })
                .catch(error => console.error("Error al eliminar el inventario:", error));
            }
        }
    });

    // Evento para guardar un inventario al hacer clic en el botón de guardar
    guardarInventarioBtn.addEventListener('click', function () {
        const url = editMode ? `gestionar-inventario/actualizar/${inventarioId}` : `gestionar-inventario/guardar`;

        const formData = new FormData(document.getElementById('crearInventarioForm'));

        // Log form data for debugging
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        fetch(url, {
            method: "POST", // Always use POST for FormData
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData,
        })
            .then((response) => {
                console.log("Respuesta del servidor:", response);
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
        cantidadDisponibleSpan.textContent = ''; // Clear available quantity
        imagenesPreview.innerHTML = ''; // Clear image previews
        imagenesInput.value = ''; // Clear image input
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
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
        resetInventarioForm();
        $(crearInventarioModal).modal('show'); // Use jQuery to show the modal
    });

    // Evento para limpiar los mensajes de error al cerrar el modal
    $('#crearInventarioModal').on('hidden.bs.modal', function () {
        clearErrorMessages(); // Clear error messages
        resetInventarioForm(); // Reset form fields
    });

    // Función para limpiar los mensajes de error
    function clearErrorMessages() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

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
                /* cantidadDisponibleSpan.textContent = `Disponible: ${data.cantidad}`; */
            })
            .catch(error => console.error('Error fetching items:', error));
    });

    // Evento para mostrar la vista previa de las imágenes seleccionadas
    imagenesInput.addEventListener('change', function () {
        imagenesPreview.innerHTML = ''; // Clear previous previews
        const dt = new DataTransfer();
        for (const file of imagenesInput.files) {
            const reader = new FileReader();
            reader.onload = function (e) {
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
                removeBtn.addEventListener('click', function () {
                    imgContainer.remove();
                    // Remove the file from the input
                    const files = Array.from(imagenesInput.files);
                    const index = files.indexOf(file);
                    if (index > -1) {
                        files.splice(index, 1);
                        dt.items.clear();
                        files.forEach(f => dt.items.add(f));
                        imagenesInput.files = dt.files;
                    }
                });
                imgContainer.appendChild(img);
                imgContainer.appendChild(removeBtn);
                imagenesPreview.appendChild(imgContainer);
                dt.items.add(file); // Add file to DataTransfer
            };
            reader.readAsDataURL(file);
        }
        //imagenesInput.files = dt.files; // Update input files
        console.log(dt.files);
        // Display the selected file name if a file is selected
        const fileLabel = document.getElementById('fileLabel');
        if (imagenesInput.files.length > 0) {
            fileLabel.textContent = `Archivo seleccionado: ${imagenesInput.files[0].name}`;
        } else {
            fileLabel.textContent = ''; // Clear the file label if no file is selected
        }
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

    registrosInventarioPorPagina.addEventListener('change', () => consultarInventarios());
    busquedaInventarioInput.addEventListener('input', () => consultarInventarios());

    cargarItems(); // Cargar los artículos al abrir el modal
});
