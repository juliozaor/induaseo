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
        Nueva Actividad</button>
</div>

<!-- Listado de Actividades -->
<div class="listado-container">
    <h2 class="listado-titulo">Listado de Actividades</h2>
    <!-- Línea divisoria -->
    <div class="divider"></div>

    <div class="busqueda-container">
        <div class="input-container">
            <input type="text" class="busqueda-input" id="busquedaInput" placeholder="Buscar...">
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
        <table class="tabla" id="tablaActividades">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Frecuencia</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se llenarán los datos dinámicamente -->
            </tbody>
        </table>
    </div>
    <div class="tabla-paginacion"></div>
</div>

<div id="createActividadModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="contenedor-titulo">
                <span id="modalTitle" class="titulo-modal">Crear nueva actividad</span>
                <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="actividadForm">
                <div class="form-group flex-grow-1 row">
                    <div class="col-6">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                        <span class="error-message" id="errorNombre"></span>
                        <span class="error-message" id="errorNombreDuplicado"></span> <!-- Nuevo span para error de nombre duplicado -->
                    </div>
                    <div class="col-6">
                        <label for="frecuencia">Frecuencia:</label>
                        <select id="frecuencia" name="frecuencia" class="form-control" required></select>
                        <span class="error-message" id="errorFrecuencia"></span>
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
            <button id="modalActionBtn" class="btn-consultar">Crear Actividad</button>
        </div>
    </div>
</div>
{{-- @endsection

@section('scripts')
<script src="{{ asset('assets/js/actividades.js') }}"></script>
@endsection --}}
