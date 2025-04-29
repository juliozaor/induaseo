(function() {
    const openUserModalBtn = document.getElementById("openUserModalBtn");
    const userModal = document.getElementById("createUserModal");
    const userModalTitle = document.getElementById("userModalTitle");
    const userModalActionBtn = document.getElementById("userModalActionBtn");
    const userForm = document.getElementById("userForm");
    const tablaUsuariosBody = document.querySelector("#tablaUsuarios tbody");
    const paginacionContainer = document.createElement('div');
    paginacionContainer.classList.add('paginacion');
    document.querySelector(".tabla-paginacion").appendChild(paginacionContainer);
    const estadoToggle = document.getElementById("estadoToggle");
    const estadoToggleLabel = document.querySelector("label[for='estadoToggle']");
    const estadoToggleContainer = document.querySelector(".switch"); // Contenedor del switch
    let editMode = false;
    let userId = null;

    // Abrir el modal
    openUserModalBtn.addEventListener("click", function() {
        userModal.style.display = "flex";
        estadoToggle.checked = true; // Estado por defecto true
        setMaxDate(); // Set the maximum date for fechaNacimiento
    });

    // Cerrar el modal al hacer clic fuera de él
    window.addEventListener("click", function(e) {
        if (e.target === userModal) {
            userModal.style.display = "none";
            resetUserForm();
            resetErrorMessages(); // Reset error messages
        }
    });

    // Función para actualizar el label del estado
    function actualizarEstadoLabel() {
        if (estadoToggle.checked) {
            estadoToggleLabel.textContent = "Activo";
            estadoToggleLabel.classList.remove("estado-inactivo");
            estadoToggleLabel.classList.add("estado-activo");
            estadoToggleLabel.style.background = "#D2F3D4";
            estadoToggleLabel.style.borderRadius = "20px";
            estadoToggleLabel.style.padding = "5px";
            estadoToggleLabel.style.font = "normal normal normal 12px/15px 'Neo Sans Std', Arial, sans-serif";
        } else {
            estadoToggleLabel.textContent = "Inactivo";
            estadoToggleLabel.classList.remove("estado-activo");
            estadoToggleLabel.classList.add("estado-inactivo");
            estadoToggleLabel.style.background = "#FFD59A";
            estadoToggleLabel.style.borderRadius = "20px";
            estadoToggleLabel.style.padding = "5px";
            estadoToggleLabel.style.font = "normal normal normal 12px/15px 'Neo Sans Std', Arial, sans-serif";
        }
    }

    // Añadir evento para cambiar el label cuando se cambia el estado del checkbox
    estadoToggle.addEventListener("change", actualizarEstadoLabel);

    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("icono-editar")) {
            userId = event.target.getAttribute("data-id");
            if (!userId) {
                console.error("Error: No se encontró el ID del usuario en el botón.");
                return;
            }

            editMode = true;

            // Mostrar el checkbox de estado en modo edición
            estadoToggleContainer.style.display = "inline-block";
            estadoToggleLabel.style.display = "inline-block";

            // Aquí continúa el código de apertura del modal y carga de datos
            userModalTitle.textContent = "Editar usuario";
            userModalActionBtn.textContent = "Guardar Cambios";

            fetch(`../usuarios?id=${userId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Error al cargar los datos del usuario.");
                    }
                    return response.json();
                })
                .then((usuario) => {
                    const fechaFormatoInput = usuario.fecha_nacimiento.split('T')[0];
                    console.log(usuario);

                    cargarRoles().then(() => {
                        document.getElementById("perfil").value = usuario.rol;
                        if (usuario.rol == '3') {
                            document.getElementById("clienteSelectContainer").style.display = "block";
                            cargarClientes().then(() => {
                                document.getElementById("cliente_id").value = usuario.clientes[0]?.id || '';
                            });
                        } else {
                            document.getElementById("clienteSelectContainer").style.display = "none";
                        }
                    });
                    document.getElementById("tipoIdentificacion").value = usuario.tipo_documento_id;
                    document.getElementById("numeroIdentificacion").value = usuario.numero_documento;
                    document.getElementById("nombres").value = usuario.nombres;
                    document.getElementById("apellidos").value = usuario.apellidos;
                    document.getElementById("fechaNacimiento").value = fechaFormatoInput;
                    document.getElementById("telefono").value = usuario.telefono;
                    document.getElementById("correo").value = usuario.email;
                    document.getElementById("cargo").value = usuario.cargo;
                    estadoToggle.checked = usuario.estado; // Set the checkbox value based on user status
                    actualizarEstadoLabel(); // Actualizar el label del estado
                    setMaxDate(); // Set the maximum date for fechaNacimiento
                    userModal.style.display = "flex"; // Muestra el modal
                })
                .catch((error) => console.error("Error al cargar los datos del usuario:", error));
        }
        // Eliminar usuario
        if (event.target.classList.contains("icono-eliminar")) {
            const userId = event.target.getAttribute("data-id");
            if (!userId) {
            console.error("Error: No se encontró el ID del usuario en el botón.");
            return;
            }

            if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
            fetch(`../usuarios/${userId}`, {
                method: "DELETE",
                headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                throw new Error("Error al eliminar el usuario.");
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                cargarUsuarios(1); // Reload the table after deletion
            })
            .catch(error => console.error("Error al eliminar el usuario:", error));
            }
        }
    });

    // Cerrar modal
    document.getElementById("closeUserModal").addEventListener("click", function() {
        userModal.style.display = "none";
        resetUserForm();
        resetErrorMessages(); // Reset error messages
    });

    // Guardar cambios
    userModalActionBtn.addEventListener("click", function() {
        const url = editMode ? `../usuarios/actualizar/${userId}` : `../usuarios/guardar`;
        const method = editMode ? "PUT" : "POST";

        const formData = new FormData(userForm);

        if (editMode) {
            formData.append("_method", "PUT");
        }

        formData.append('estado', estadoToggle.checked ? 1 : 0); // Añadir estado al formData

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
                    showUserErrors(data.errors);
                } else {
                    showAlertModal(
                        "ok.png", // Ruta del ícono de éxito
                        data.message // Mensaje de éxito
                    );
                    userModal.style.display = "none";
                    resetUserForm();
                    cargarUsuarios(1); // Add this line to reload the table
                }
            })
            .catch((error) => {
                if (error.errors) {
                    showUserErrors(error.errors);
                } else {
                    console.error("Error al guardar el usuario:", error);
                }
            });
    });

    function showUserErrors(errors) {
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

    function resetUserForm() {
        userForm.reset();
        userModalTitle.textContent = "Crear nuevo usuario";
        userModalActionBtn.textContent = "Crear Usuario";
        editMode = false;
        userId = null;

        // Ocultar el checkbox de estado en modo creación
        estadoToggleContainer.style.display = "none";
        estadoToggleLabel.style.display = "none";
    }

    function resetErrorMessages() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // Cargar roles
    function cargarRoles() {
        return fetch(`../roles`)
            .then(response => response.json())
            .then(roles => {
                const perfilSelect = document.getElementById("perfil");
                perfilSelect.innerHTML = '<option value="">Seleccione</option>';
                roles.forEach(rol => {
                    const option = document.createElement("option");
                    option.value = rol.id;
                    option.textContent = rol.name;
                    perfilSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los roles:", error));
    }

    // Cargar roles en el select de filtro
    function cargarRolesFiltro() {
        return fetch(`../roles`)
            .then(response => response.json())
            .then(roles => {
                const filtroPerfilSelect = document.getElementById("filtroPerfil");
                filtroPerfilSelect.innerHTML = '<option value="">Todos los Perfiles</option>';
                roles.forEach(rol => {
                    const option = document.createElement("option");
                    option.value = rol.id;
                    option.textContent = rol.name;
                    filtroPerfilSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los roles:", error));
    }

    // Cargar tipos de documentos
    function cargarTiposDocumentos() {
        fetch(`../tipos-documentos`)
            .then(response => response.json())
            .then(tiposDocumentos => {
                const tipoIdentificacionSelect = document.getElementById("tipoIdentificacion");
                tipoIdentificacionSelect.innerHTML = '<option value="">Seleccione</option>';
                tiposDocumentos.forEach(tipo => {
                    const option = document.createElement("option");
                    option.value = tipo.id;
                    option.textContent = tipo.nombre;
                    tipoIdentificacionSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los tipos de documentos:", error));
    }

    function cargarUsuarios(page = 1) {
        const buscar = document.getElementById("busquedaUsuarioInput").value;
        const registrosPorPagina = document.getElementById("registrosUsuarioPorPagina").value;
        const filtroPerfil = document.getElementById("filtroPerfil").value;

        const formData = new FormData();
        formData.append('buscar', buscar);
        formData.append('registros_por_pagina', registrosPorPagina);
        formData.append('filtro_perfil', filtroPerfil); // Añadir el filtro por perfil

        fetch(`../admin/usuarios?page=${page}`, {
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
                tablaUsuariosBody.innerHTML = '';
                paginacionContainer.innerHTML = '';

                // Llenar la tabla con los datos
                data.data.forEach(usuario => {
                    const row = document.createElement("tr");
                    const estadoClase = usuario.estado ? 'estado-activo' : 'estado-inactivo';
                    row.innerHTML = `
                    <td>${usuario.numero_documento}</td>
                    <td>${usuario.nombres} ${usuario.apellidos}</td>
                    <td>${usuario.rol}</td>
                    <td>${formatDate(usuario.fecha_nacimiento)}</td>
                    <td>${usuario.email}</td>
                    <td>${usuario.telefono}</td>
                    <td><div class="${estadoClase}">${usuario.estado ? 'Activo' : 'Inactivo'}</div></td>
                    <td>
                        <img src="../assets/icons/editar.png" alt="Editar" class="icono-editar" data-id="${usuario.id}">
                        <img src="../assets/icons/eliminar.png" alt="Eliminar" class="icono-eliminar" data-id="${usuario.id}">
                    </td>
                `;
                    tablaUsuariosBody.appendChild(row);
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
                    cargarUsuarios(current_page - 1);
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
                        cargarUsuarios(i);
                    });

                    paginacionContainer.appendChild(pageButton);
                }

                // Botón de página siguiente
                const nextButton = document.createElement("button");
                nextButton.textContent = "Sig.";
                nextButton.classList.add("page-button", "sig");
                nextButton.disabled = current_page === last_page;
                nextButton.addEventListener('click', () => {
                    cargarUsuarios(current_page + 1);
                });
                paginacionContainer.appendChild(nextButton);
            })
            .catch(error => console.error('Error:', error));
    }

    function formatDate(dateString) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    function setMaxDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById("fechaNacimiento").setAttribute('max', today);
    }

    cargarRoles();
    cargarRolesFiltro();
    cargarTiposDocumentos();
    cargarUsuarios(1);

    // Eventos
    document.getElementById("busquedaUsuarioInput").addEventListener("input", () => cargarUsuarios(1));
    document.getElementById("registrosUsuarioPorPagina").addEventListener("change", () => cargarUsuarios(1));
    document.getElementById("filtroPerfil").addEventListener("change", () => cargarUsuarios(1));

    const perfilSelect = document.getElementById("perfil");

    perfilSelect.addEventListener("change", function() {
        const clienteSelectContainer = document.getElementById("clienteSelectContainer");

        if (this.value === '3') {
            clienteSelectContainer.style.display = "block";
            cargarClientes();
        } else {
            clienteSelectContainer.style.display = "none";
        }
    });

    function cargarClientes() {
        return fetch(`../clientes-select`)
            .then(response => response.json())
            .then(clientes => {
                const clienteSelect = document.getElementById("cliente_id");
                clienteSelect.innerHTML = '<option value="">Seleccione</option>';
                clientes.forEach(cliente => {
                    const option = document.createElement("option");
                    option.value = cliente.id;
                    option.textContent = cliente.nombre;
                    clienteSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los clientes:", error));
    }

    // Add input validation for numeroIdentificacion and telefono
    document.getElementById("numeroIdentificacion").addEventListener("input", function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    document.getElementById("telefono").addEventListener("input", function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });
})();
