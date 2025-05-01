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

<!-- Listado de Áreas -->
<div class="listado-container">
    <h2 class="listado-titulo">Listado de áreas</h2>
    <!-- Línea divisoria -->
    <div class="divider"></div>

    <div class="busqueda-container">
        <div class="input-container">
            <input type="text" class="busqueda-input" id="busquedaInput" placeholder="Buscar Área...">
        </div>
        <div class="input-container2">
            <select id="clienteFiltro" class="busqueda-select">
                <option value="">Todos los clientes</option>
                {{-- Aquí se llenarán los clientes desde JavaScript --}}
            </select>
        </div>
        <div class="input-container2">
            <select id="sedeFiltro" class="busqueda-select">
                <option value="">Todas las sedes</option>
                {{-- Aquí se llenarán las sedes desde JavaScript --}}
            </select>
        </div>
        <div class="input-container2">
            <select id="estadoFiltro" class="busqueda-select">
                <option value="">Todos los estados</option>
                <option value="1">Activo</option>
                <option value="2">Inactivo</option>
            </select>
        </div>
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
        <table class="tabla" id="tablaAreas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Área</th>
                    <th>Cliente</th>
                    <th>Sede</th>
                    <th>Total Actividades</th>
                    <th>Estado</th>
                    <th>Fecha Actualización</th>
                    <th>Actualizado por</th>
                    <th>Creado por</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- <tr>
                    <td class="total-actividades"></td>
                </tr> --}}
            </tbody>
        </table>
    </div>
    <div class="tabla-paginacion"></div>
</div>

<div id="createAreaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="contenedor-titulo">
                <span id="modalTitle" class="titulo-modal">Crear nueva área</span>
                <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="areaForm">
                <div class="form-group flex-grow-1 row">
                    <div class="col-6">
                        <label for="cliente">Cliente:</label>
                        <select id="cliente" name="cliente" class="form-control" required></select>
                        <span class="error-message" id="errorCliente"></span>
                    </div>
                    <div class="col-6">
                        <label for="sede">Sede:</label>
                        <select id="sede" name="sede" class="form-control" required></select>
                        <span class="error-message" id="errorSede"></span>
                    </div>
                    <div class="col-12">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required disabled>
                        <span class="error-message" id="errorNombre"></span>
                        <span class="error-message" id="nombreExistente" style="display: none; color: red;">El nombre del área ya existe en la sede seleccionada.</span>
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
            <button id="modalActionBtn" class="btn-consultar" >Crear Área</button>
        </div>
        <div id="actividadSection" style="display: none;">
            {{-- Aquí puedes agregar actividades para la nueva área --}}
            <div class="form-group">
                <label for="actividadSelect">Seleccionar Actividad:</label>
                <div class="select-container2">
                    <select id="actividadSelect" class="form-control">
                        {{-- Aquí se llenarán las actividades desde JavaScript --}}
                    </select>
                    <button id="agregarActividadBtn" class="btn-actividad" style="min-width: 130px;">Agregar Actividad</button>
                </div>
            </div>
            <div class="tabla-container" style="max-height: 100px">
                <table class="tabla" id="tablaActividades">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div style="text-align: right; margin-top: 10px">
                <button id="guardarFinalizarBtn" class="btn-consultar">Guardar y finalizar</button>
            </div>
        </div>
    </div>
</div>
