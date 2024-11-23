<div class="btn-nuevo">
    <button class="btn-consultar" id="openModalInsumoBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
            <g id="Grupo_22674" data-name="Grupo 22674" transform="translate(-19 -11.164)">
                <line id="Línea_338" data-name="Línea 338" y1="15" transform="translate(27.5 12.164)"
                    fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2" />
                <line id="Línea_339" data-name="Línea 339" y1="15"
                    transform="translate(35 19.664) rotate(90)" fill="none" stroke="#fff" stroke-linecap="round"
                    stroke-width="2" />
            </g>
        </svg>
        Nuevo Insumo
    </button>
</div>

<!-- Listado de Insumos -->
<div class="listado-container">
    <h2 class="listado-titulo">Listado de insumos</h2>
    <!-- Línea divisoria -->
    <div class="divider"></div>

    <div class="busqueda-container">
        <div class="input-container">
            <input type="text" class="busqueda-input" id="busquedaInsumoInput" placeholder="Buscar...">
        </div>
        <select class="filtro form-control" id="filtroClasificacionInsumo" name="filtroClasificacionInsumo">
            <option value="">Todas las clasificaciones</option>
            <!-- Opciones de clasificaciones se llenarán dinámicamente -->
        </select>
        <select class="filtro form-control" id="filtroEstadoInsumo">
            <option value="">Todos los estados</option>
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select>
        <span class="registros-encontrados">Total: 0</span>
        <select class="registros-por-pagina" id="registrosPorPaginaInsumo">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>

    <!-- Tabla de datos -->
    <div class="tabla-container">
        <table class="tabla" id="tablaInsumos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Insumo</th>
                    <th>Estado del Insumo</th>
                    <th>Marca</th>
                    <th>Código</th>
                    <th>Clasificación</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Proveedor</th>
                    <th>Teléfono Proveedor</th>
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

<div id="createInsumoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="contenedor-titulo">
                <span id="modalInsumoTitle" class="titulo-modal">Crear nuevo insumo</span>
                <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeInsumo">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="insumoForm">
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="nombreInsumo">Nombre del Insumo:</label>
                        <input type="text" id="nombreInsumo" name="nombreInsumo" class="form-control" required>
                        <span class="error-message" id="errorNombreInsumo"></span>
                    </div>
                    <div class="col-3">
                        <label for="marcaInsumo">Marca:</label>
                        <input type="text" id="marcaInsumo" name="marcaInsumo" class="form-control" required>
                        <span class="error-message" id="errorMarcaInsumo"></span>
                    </div>
                    <div class="col-3">
                        <label for="codigoInsumo">Código:</label>
                        <input type="text" id="codigoInsumo" name="codigoInsumo" class="form-control" required>
                        <span class="error-message" id="errorCodigoInsumo"></span>
                    </div>
                    <div class="col-3">
                        <label for="clasificacionInsumo">Clasificación:</label>
                        <select id="clasificacionInsumo" name="clasificacionInsumo" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Opciones de clasificación se llenarán dinámicamente -->
                        </select>
                        <span class="error-message" id="errorClasificacionInsumo"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="cantidadInsumo">Cantidad:</label>
                        <input type="number" id="cantidadInsumo" name="cantidadInsumo" class="form-control" required>
                        <span class="error-message" id="errorCantidadInsumo"></span>
                    </div>
                    <div class="col-3">
                        <label for="estadoInsumo">Estado:</label>
                        <select id="estadoInsumo" name="estadoInsumo" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Opciones de estado se llenarán dinámicamente -->
                        </select>
                        <span class="error-message" id="errorEstadoInsumo"></span>
                    </div>
                    <div class="col-3">
                        <label for="proveedorInsumo">Proveedor:</label>
                        <input type="text" id="proveedorInsumo" name="proveedorInsumo" class="form-control" required>
                        <span class="error-message" id="errorProveedorInsumo"></span>
                    </div>
                    <div class="col-3">
                        <label for="telefonoProveedorInsumo">Teléfono Proveedor:</label>
                        <input type="text" id="telefonoProveedorInsumo" name="telefonoProveedorInsumo" class="form-control" required>
                        <span class="error-message" id="errorTelefonoProveedorInsumo"></span>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <label class="switch">
                <input type="checkbox" id="estadoInsumoToggle" checked>
                <span class="slider round"></span>
            </label>
            <label for="estadoInsumoToggle">Activo</label>
            <button id="modalInsumoActionBtn" class="btn-consultar">Crear Insumo</button>
        </div>
    </div>
</div>