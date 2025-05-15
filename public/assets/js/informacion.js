(function () {
    const openInformacionModalBtn = document.getElementById("openInformacionModalBtn");
    const informacionModal = document.getElementById("createInformacionModal");
    const informacionModalTitle = document.getElementById("informacionModalTitle");
    const informacionModalActionBtn = document.getElementById("informacionModalActionBtn");
    const informacionForm = document.getElementById("informacionForm");
    const tablaInformacionBody = document.querySelector("#tablaInformacion tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);
    let editMode = false;
    let informacionId = null;

    const clienteSelect = document.getElementById("cliente_id");
    const sedeSelect = document.getElementById("sede_id");

    // Abrir el modal
    openInformacionModalBtn.addEventListener("click", function () {
        informacionModal.style.display = "flex";

        cargarTiposMultimedia();
        cargarCategorias();
        cargarClientes();
    });

    // Cerrar el modal al hacer clic fuera de él
    window.addEventListener("click", function (e) {
        if (e.target === informacionModal) {
            informacionModal.style.display = "none";
            resetInformacionForm();
        }
    });

    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("icono-editar")) {
            informacionId = event.target.getAttribute("data-id");
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            if (!informacionId) {
                console.error("Error: No se encontró el ID de la información en el botón.");
                return;
            }

            editMode = true;

            // Aquí continúa el código de apertura del modal y carga de datos
            informacionModalTitle.textContent = "Editar información";
            informacionModalActionBtn.textContent = "Guardar Cambios";

            fetch(`../informacion/obtener?id=${informacionId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos de la información.");
                    }
                    return response.json();
                })
                .then((informacion) => {
                    cargarTiposMultimedia().then(() => {
                        document.getElementById("tipo_multimedia_id").value = informacion.tipo_multimedia_id;
                    });
                    cargarCategorias().then(() => {
                        document.getElementById("categoria_id").value = informacion.categoria_id;
                    });
                    cargarClientes();
                    cargarSedes(informacion.sede.cliente.id).then(() => {
                        document.getElementById("sede_id").value = informacion.sede_id;
                        document.getElementById("cliente_id").value = informacion.sede.cliente.id;
                    });

                    document.getElementById("titulo").value = informacion.titulo;
                    document.getElementById("descripcion").value = informacion.descripcion;
                    // Display the existing file name as a label or placeholder
                    const fileLabel = document.getElementById("fileLabel");
                    fileLabel.textContent = `Archivo actual: ${informacion.url}`;

                    informacionModal.style.display = "flex"; // Muestra el modal
                })
                .catch((error) => console.error("Error al cargar los datos de la información:", error));
        }

        if (event.target.classList.contains("icono-eliminar")) {
            const informacionId = event.target.getAttribute("data-id");
            const tipo = event.target.getAttribute("data-tipo");
            if (!informacionId) {
                console.error("Error: No se encontró el ID de la información en el botón.");
                return;
            }
            if (tipo === "informacion") {
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar esta información?',
                    /* text: "No podrás revertir esto.", */
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`../informacion/eliminar/${informacionId}`, {
                            method: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error("Error al eliminar la información.");
                                }
                                return response.json();
                            })
                            .then(data => {
                                showAlertModal(
                                    "ok.png", // Ruta del ícono de éxito
                                    "Información eliminada con éxito." // Mensaje de éxito
                                );
                                /* alert(); */
                                cargarInformacion(1); // Reload the table
                            })
                            .catch(error => console.error("Error al eliminar la información:", error));
                    }
                });
            }

        }
    });

    // Cerrar modal
    document.getElementById("closeInformacionModal").addEventListener("click", function () {
        informacionModal.style.display = "none";
        resetInformacionForm();
        document.getElementById("fileLabel").textContent = ''; // Clear the file label
    });

    // Guardar cambios
    informacionModalActionBtn.addEventListener("click", function () {
        const url = editMode ? `../informacion/actualizar/${informacionId}` : `../informacion/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(informacionForm);
        /* console.log('formData:', ...formData); */
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
                console.log('data:', data);
                if (data.errors) {
                    showInformacionErrors(data.errors);
                } else {
                    showAlertModal(
                        "ok.png", // Ruta del ícono de éxito
                        data.message // Mensaje de éxito
                    );
                    informacionModal.style.display = "none";
                    resetInformacionForm();
                    cargarInformacion(1); // Add this line to reload the table
                }
            })
            .catch((error) => {
                console.error("Error al guardar la información:", error);
                if (error.errors) {
                    showInformacionErrors(error.errors);
                } else {
                    console.error("Error al guardar la información:", error);
                }
            });
    });

    function showInformacionErrors(errors) {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        for (const [key, messages] of Object.entries(errors)) {
            const errorElement = document.getElementById(`error${capitalizeFirstLetter(key)}`);
            console.log(capitalizeFirstLetter(key));
            if (errorElement) {
                errorElement.textContent = messages.join(', ');
            }
        }
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function resetInformacionForm() {
        informacionForm.reset();
        informacionModalTitle.textContent = "Crear nueva información";
        informacionModalActionBtn.textContent = "Crear Información";
        editMode = false;
        informacionId = null;
        document.getElementById("fileLabel").textContent = ''; // Clear the file label

        // Resetea aquí los mensajes de error
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // Cargar tipos de multimedia
    function cargarTiposMultimedia() {
        return fetch(`../tipos-multimedia`)
            .then(response => response.json())
            .then(tiposMultimedia => {
                const tipoMultimediaSelect = document.getElementById("tipo_multimedia_id");
                tipoMultimediaSelect.innerHTML = '<option value="">Seleccione</option>';
                tiposMultimedia.forEach(tipo => {
                    const option = document.createElement("option");
                    option.value = tipo.id;
                    option.textContent = tipo.nombre;
                    tipoMultimediaSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los tipos de multimedia:", error));
    }

    // Cargar categorías
    function cargarCategorias() {
        return fetch(`../categorias`)
            .then(response => response.json())
            .then(categorias => {
                const categoriaSelect = document.getElementById("categoria_id");
                categoriaSelect.innerHTML = '<option value="">Seleccione</option>';
                categorias.forEach(categoria => {
                    const option = document.createElement("option");
                    option.value = categoria.id;
                    option.textContent = categoria.nombre;
                    categoriaSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar las categorías:", error));
    }

    function cargarClientes() {
        fetch(`../clientes-select`)
            .then(response => response.json())
            .then(clientes => {
                clienteSelect.innerHTML = '<option value="">Seleccione</option>';
                clientes.forEach(cliente => {
                    const option = document.createElement("option");
                    option.value = cliente.id;
                    option.textContent = cliente.nombre;
                    clienteSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar clientes:', error));
    }

    function cargarSedes(clienteId) {
        return fetch(`../sedes?cliente_id=${clienteId}`)
            .then(response => response.json())
            .then(sedes => {
                sedeArr = sedes;

                sedeSelect.innerHTML = '<option value="">Seleccione</option>';
                sedes.forEach(sede => {
                    const option = document.createElement("option");
                    option.value = sede.id;
                    option.textContent = sede.nombre;
                    sedeSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar sedes:', error));
    }

    function cargarInformacion(page = 1) {
        const buscar = document.getElementById("busquedaInformacionInput").value;
        const registrosPorPagina = document.getElementById("registrosInformacionPorPagina").value;

        const formData = new FormData();
        formData.append('buscar', buscar);
        formData.append('registros_por_pagina', registrosPorPagina);

        fetch(`../admin/informacion?page=${page}`, {
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
                tablaInformacionBody.innerHTML = '';
                paginacionContainer.innerHTML = '';

                // Llenar la tabla con los datos
                data.data.forEach(informacion => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                    <td>${informacion.titulo}</td>
                    <td>${informacion.descripcion}</td>
                    <td>${informacion.sede.cliente.nombre}</td>
                    <td>${informacion.sede.nombre}</td>
                    <td>${informacion.tipo_multimedia.nombre}</td>
                    <td>${informacion.categoria.nombre}</td>
                    <td>${formatDate(informacion.fecha)}</td>
                    <td>
                        <img src="../assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${informacion.id}">
                        <img src="../assets/icons/eliminar.png" alt="Eliminar" class="icono-eliminar" data-id="${informacion.id}" data-tipo="informacion">
                    </td>
                `;
                    tablaInformacionBody.appendChild(row);
                });

                // Mostrar total de registros
                document.querySelector('.registros-encontrados').textContent = `Total: ${data.total}`;

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
                    cargarInformacion(current_page - 1);
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
                        cargarInformacion(i);
                    });

                    paginacionContainer.appendChild(pageButton);
                }

                // Botón de página siguiente
                const nextButton = document.createElement("button");
                nextButton.textContent = "Sig.";
                nextButton.classList.add("page-button", "sig");
                nextButton.disabled = current_page === last_page;
                nextButton.addEventListener('click', () => {
                    cargarInformacion(current_page + 1);
                });
                paginacionContainer.appendChild(nextButton);
            })
            .catch(error => console.error('Error:', error));
    }

    function formatDate(dateString) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    cargarTiposMultimedia();
    cargarCategorias();
    cargarSedes();
    cargarInformacion(1);

    // Eventos
    document.getElementById("busquedaInformacionInput").addEventListener("input", () => cargarInformacion(1));
    document.getElementById("registrosInformacionPorPagina").addEventListener("change", () => cargarInformacion(1));

    clienteSelect.addEventListener("change", function () {
        const clienteId = clienteSelect.value;
        cargarSedes(clienteId);
    });
})();
