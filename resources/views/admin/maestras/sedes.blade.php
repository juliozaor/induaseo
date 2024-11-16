<div class="btn-nuevo">
    <button class="btn-consultar" id="openModalBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
            <g id="Grupo_22674" data-name="Grupo 22674" transform="translate(-19 -11.164)">
                <line id="Línea_338" data-name="Línea 338" y1="15" transform="translate(27.5 12.164)"
                    fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2" />
                <line id="Línea_339" data-name="Línea 339" y1="15"
                    transform="translate(35 19.664) rotate(90)" fill="none" stroke="#fff" stroke-linecap="round"
                    stroke-width="2" />
            </g>
        </svg>
        Nuevo</button>
</div>

<!-- Listado de Sedes -->
<div class="listado-container">
    <h2 class="listado-titulo">Listado de sedes</h2>
    <!-- Línea divisoria -->
    <div class="divider"></div>

    <div class="busqueda-container">
        <div class="input-container">
            <input type="text" class="busqueda-input" id="busquedaInput" placeholder="Buscar...">
        </div>
        <select class="filtro form-control" id="filtroCliente" name="filtroCliente">
            <option value="">Todos los clientes</option>
            <!-- Opciones de clientes se llenarán dinámicamente -->
        </select>
        <select class="filtro form-control" id="filtroEstado">
            <option value="">Todos los estados</option>
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select>
        <span class="registros-encontrados">Total: 0</span>
        <select class="registros-por-pagina" id="registrosPorPagina">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>

    <!-- Tabla de datos -->
    <div class="tabla-container">
        <table class="tabla" id="tablaSedes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cliente</th>
                    <th>País</th>
                    <th>Ciudad</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Horario Inicio</th>
                    <th>Horario Fin</th>
                    <th>Estado</th>
                    <th>Creado por</th>
                    <th>Fecha de creación</th>
                    <th>Actualizado por</th>
                    <th>Fecha actualización</th>
                    <th>Regional</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="tabla-paginacion"></div>
</div>

<div id="createSedeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="contenedor-titulo">
                <span id="modalTitle" class="titulo-modal">Crear nueva sede</span>
                <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="sedeForm">
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                        <span class="error-message" id="errorNombre"></span>
                    </div>
                    <div class="col-3">
                        <label for="cliente">Cliente:</label>
                        <select id="cliente" name="cliente" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Agrega más opciones según sea necesario -->
                        </select>
                        <span class="error-message" id="errorCliente"></span>
                    </div>
                    <div class="col-3">
                        <label for="pais">País:</label>
                        <select id="pais" name="pais" class="form-control" required>
                            <option value="">Seleccione</option>
                        </select>
                        <span class="error-message" id="errorPais"></span>
                    </div>
                    <div class="col-3">
                        <label for="ciudad">Ciudad:</label>
                        <select id="ciudad" name="ciudad" class="form-control" required>
                            <option value="">Seleccione</option>
                        </select>
                        <span class="error-message" id="errorCiudad"></span>
                    </div>
                </div>

                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="direccion">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" class="form-control" required>
                        <span class="error-message" id="errorDireccion"></span>
                    </div>
                    <div class="col-3">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" required>
                        <span class="error-message" id="errorTelefono"></span>
                    </div>
                    <div class="col-3">
                        <label for="horarioInicio">Horario Inicio:</label>
                        <input type="time" id="horarioInicio" name="horarioInicio" class="form-control" required>
                        <span class="error-message" id="errorHorarioInicio"></span>
                    </div>
                    <div class="col-3">
                        <label for="horarioFin">Horario Fin:</label>
                        <input type="time" id="horarioFin" name="horarioFin" class="form-control" required>
                        <span class="error-message" id="errorHorarioFin"></span>
                    </div>
                    <div class="col-3">
                        <label for="regional">Regional:</label>
                        <select id="regional" name="regional" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Opciones de regionales se llenarán dinámicamente -->
                        </select>
                        <span class="error-message" id="errorRegional"></span>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <label class="switch">
                <input type="checkbox" id="estadoToggle" checked>
                <span class="slider round"></span>
            </label>
            <label for="estadoToggle">Activo</label>
            <button id="modalActionBtn" class="btn-consultar">Crear Sede</button>
        </div>
    </div>
</div>
