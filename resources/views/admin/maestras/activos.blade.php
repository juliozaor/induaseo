
<div class="btn-nuevo">
    <button class="btn-consultar" id="openModalActivoBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
            <g id="Grupo_22674" data-name="Grupo 22674" transform="translate(-19 -11.164)">
                <line id="Línea_338" data-name="Línea 338" y1="15" transform="translate(27.5 12.164)"
                    fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2" />
                <line id="Línea_339" data-name="Línea 339" y1="15"
                    transform="translate(35 19.664) rotate(90)" fill="none" stroke="#fff" stroke-linecap="round"
                    stroke-width="2" />
            </g>
        </svg>
        Nuevo Activo
    </button>
</div>

<!-- Listado de Activos -->
<div class="listado-container">
    <h2 class="listado-titulo">Listado de activos</h2>
    <!-- Línea divisoria -->
    <div class="divider"></div>

    <div class="busqueda-container">
        <div class="input-container">
            <input type="text" class="busqueda-input" id="busquedaActivoInput" placeholder="Buscar...">
        </div>
        <select class="filtro form-control" id="filtroClasificacion" name="filtroClasificacion">
            <option value="">Todas las clasificaciones</option>
            <!-- Opciones de clasificaciones se llenarán dinámicamente -->
        </select>
        <select class="filtro form-control" id="filtroEstadoActivo">
            <option value="">Todos los estados</option>
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select>
        <span class="registros-encontrados">Total: 0</span>
        <select class="registros-por-pagina" id="registrosPorPaginaActivo">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>

    <!-- Tabla de datos -->
    <div class="tabla-container">
        <table class="tabla" id="tablaActivos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Elemento</th>
                    <th>Estado del Elemento</th>
                    <th>Clasificación</th>
                    <th>Cantidad</th>
                    <th>Serie</th>
                    <th>Marca</th>
                    <th>Estado</th>
                    <th>Creado por</th>
                    <th>Fecha de creación</th>
                    <th>Actualizado por</th>
                    <th>Fecha actualización</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="tabla-paginacion"></div>
</div>

<div id="createActivoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="contenedor-titulo">
                <span id="modalActivoTitle" class="titulo-modal">Crear nuevo activo</span>
                <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeActivo">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="activoForm">
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="nombreElemento">Nombre del Elemento:</label>
                        <input type="text" id="nombreElemento" name="nombreElemento" class="form-control" required>
                        <span class="error-message" id="errorNombreElemento"></span>
                    </div>
                    <div class="col-3">
                        <label for="marca">Marca:</label>
                        <input type="text" id="marca" name="marca" class="form-control" required>
                        <span class="error-message" id="errorMarca"></span>
                    </div>
                    <div class="col-3">
                        <label for="serie">Serie:</label>
                        <input type="text" id="serie" name="serie" class="form-control" required>
                        <span class="error-message" id="errorSerie"></span>
                    </div>
                    <div class="col-3">
                        <label for="clasificacion">Clasificación:</label>
                        <select id="clasificacion" name="clasificacion" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Opciones de clasificación se llenarán dinámicamente -->
                        </select>
                        <span class="error-message" id="errorClasificacion"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" id="cantidad" name="cantidad" class="form-control" required>
                        <span class="error-message" id="errorCantidad"></span>
                    </div>
                    <div class="col-3">
                        <label for="estadoActivo">Estado:</label>
                        <select id="estadoActivo" name="estadoActivo" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Opciones de estado se llenarán dinámicamente -->
                        </select>
                        <span class="error-message" id="errorEstadoActivo"></span>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <label class="switch">
                <input type="checkbox" id="estadoActivoToggle" checked>
                <span class="slider round"></span>
            </label>
            <label for="estadoActivoToggle">Activo</label>
            <button id="modalActivoActionBtn" class="btn-consultar">Crear Activo</button>
        </div>
    </div>
</div>