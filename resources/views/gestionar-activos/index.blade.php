@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/gestion-activos.css') }}?v={{ time() }}">

@section('content')
    <div class="select-container">
        <div class="select-container2 row">
            <div class="col-md-5">
                <label for="clienteSelect">Cliente:</label>
                <select id="clienteSelect" class="form-control">
                    <option value="">Seleccione un cliente</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label for="sedeSelect">Sede:</label>
                <select id="sedeSelect" class="form-control">
                    <option value="">Seleccione una sede</option>
                </select>
            </div>
            <div class="col-md-2">
                <button id="consultarBtn" class="btn-consultar">Consultar</button>
            </div>
        </div>
    </div>


    <ul class="nav nav-pills p-3" id="pills-tab" role="tablist" style="display: none; background-color: #E8E8E8;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-activos-tab" data-toggle="pill" data-target="#pills-activos"
                type="button" role="tab" aria-controls="pills-activos" aria-selected="true">Activos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-mantenimiento-tab" data-toggle="pill" data-target="#pills-mantenimiento"
                type="button" role="tab" aria-controls="pills-mantenimiento"
                aria-selected="false">Mantenimiento</button>
        </li>
        <li class="nav-item ml-auto align-self-start crear-activo-tab" role="presentation">
            <div class="btn-nuevo">
                <button id="cerarActivoBtn" class="btn-consultar" data-toggle="modal"
                    data-target="#crearActivoModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
                        <g id="Grupo_22674" data-name="Grupo 22674" transform="translate(-19 -11.164)">
                            <line id="Línea_338" data-name="Línea 338" y1="15"
                                transform="translate(27.5 12.164)" fill="none" stroke="#fff"
                                stroke-linecap="round" stroke-width="2" />
                            <line id="Línea_339" data-name="Línea 339" y1="15"
                                transform="translate(35 19.664) rotate(90)" fill="none" stroke="#fff"
                                stroke-linecap="round" stroke-width="2" />
                        </g>
                    </svg>
                    Crear activo
                </button>
            </div>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent" style="display: none;">
        <div class="tab-pane fade show active" id="pills-activos" role="tabpanel" aria-labelledby="pills-activos-tab">


            <div class="listado-container">
                <h2 class="listado-titulo">Lista de activos</h2>
                <div class="divider"></div>

                <div class="busqueda-container">
                    <div class="input-container">
                        <input type="text" class="busqueda-input" id="busquedaTurnoInput" name="busquedaTurnoInput" placeholder="Buscar...">
                    </div>
                    <div class="input-container2">
                        <select id="estadoActivoSelect" name="estadoActivoSelect" class="busqueda-select">
                            <option value="">Todos los estados</option>
                            <!-- Opciones de estados se llenarán dinámicamente -->
                        </select>
                    </div>
                    <span class="registros-encontrados" id="totalActivos">Total: 0</span>
                    <select class="registros-por-pagina" id="registrosTurnoPorPagina">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <div class="tabla-container">
                    <table class="tabla" id="tablaActivos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Estado del elemento</th>
                                <th>Sede</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Creado por</th>
                                <th>Fecha creación</th>
                                <th>Editado por</th>
                                <th>Fecha actualización</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="activosTableBody">
                            <!-- Datos de turnos -->
                        </tbody>
                    </table>
                </div>
                <div class="tabla-paginacion"></div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="crearActivoModal" tabindex="-1" role="dialog"
                aria-labelledby="crearActivoModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="contenedor-titulo">
                                <span id="crearActivoModalLabel" class="titulo-modal">Asignar Activo</span>
                                <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="crearActivoForm">
                                <input type="hidden" id="activoId" name="activoId">
                                <div class="form-group flex-grow-1 row">
                                    <div class="col">
                                        <label for="clienteInput">Cliente</label>
                                        <input type="text" id="clienteInput" name="clienteInput" class="form-control"
                                            readonly name="cliente_id">
                                        <span class="error-message" id="errorClienteInput"></span>
                                    </div>
                                    <div class="col">
                                        <label for="sedeInput">Sede</label>
                                        <input type="text" id="sedeInput" name="sedeInput" class="form-control"
                                            readonly name="sede_id">
                                        <span class="error-message" id="errorSedeInput"></span>
                                    </div>
                                    <div class="col">
                                        <label for="estadoActivo">Estado:</label>
                                        <select id="estadoActivo" name="estadoActivo" class="form-control" required>
                                            <option value="">Seleccione</option>
                                            <!-- Opciones de estado se llenarán dinámicamente -->
                                        </select>
                                        <span class="error-message" id="errorEstadoActivo"></span>
                                    </div>
                                </div>
                                <div class="form-group flex-grow-1 row">
                                    <div class="col">
                                        <label for="activoSelect">Seleccionar activo</label>
                                        <select id="activoSelect" name="activoSelect" class="form-control"
                                            name="activo_id">
                                            <!-- Opciones de activos -->
                                        </select>
                                        <span class="error-message" id="erroractivoSelect"></span>
                                    </div>
                                    <div class="col">
                                        <label for="codigoInput">Código, No. de serie</label>
                                        <input type="text" id="codigoInput" name="codigoInput" class="form-control"
                                            readonly>
                                        <span class="error-message" id="errorCodigoInput"></span>
                                    </div>
                                    <div class="col">
                                        <label for="cantidadInput">Cantidad</label>
                                        <input type="text" id="cantidadInput" class="form-control" name="cantidad">
                                        <span class="error-message" id="errorCantidadInput"></span>
                                    </div>
                                </div>
                                <div class="form-group flex-grow-1 row">
                                    <div class="col">
                                        <label for="imagenesInput">Imagen Formatos permitidos PNG, JPG</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="imagenesInput"
                                                    name="imagenesInput" accept="image/*">
                                                <label class="custom-file-label" for="imagenesInput">Selecciona un archivo
                                                    desde el dispositivo</label>
                                            </div>
                                        </div>
                                        <span class="error-message" id="errorImagenesInput"></span>
                                        <div id="imagenesPreview" class="imagenes-preview mt-3"></div>
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
                            <button type="button" class="btn-consultar" id="guardarActivoBtn">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="pills-mantenimiento" role="tabpanel" aria-labelledby="pills-mantenimiento-tab">
            <div class="listado-container">
                <h2 class="listado-titulo">Lista de mantenimientos</h2>
                <div class="divider"></div>

                <div class="busqueda-container">
                    <div class="input-container">
                        <input type="text" class="busqueda-input" id="busquedaMantenimientoInput" name="busquedaMantenimientoInput" placeholder="Buscar...">
                    </div>
                    <div class="input-container2">
                        <select id="estadoMantenimientoSelect" name="estadoMantenimientoSelect" class="busqueda-select">
                            <option value="">Todos los estados</option>
                            <!-- Opciones de estados se llenarán dinámicamente -->
                        </select>
                    </div>
                    <span class="registros-encontrados" id="totalMantenimientos">Total: 0</span>
                    <select id="registrosMantenimientoPorPagina" class="registros-por-pagina">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <div class="tabla-container">
                    <table class="tabla" id="tablaMantenimientos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Último mtto.</th>
                                <th>Mtto programado</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Estado del elemento</th>
                                <th>Estado</th>
                                <th>Sede</th>
                                <th>Cliente</th>
                                <th>Creado por</th>
                                <th>Fecha creación</th>
                                <th>Editado por</th>
                                <th>Fecha actualización</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="mantenimientosTableBody">
                            <!-- Datos de mantenimientos -->
                        </tbody>
                    </table>
                </div>
                <div class="tabla-paginacion"></div>
            </div>
        </div>
    </div>
@endsection

<!-- Modal para Mantenimientos -->
<div class="modal fade" id="mantenimientoModal" tabindex="-1" role="dialog" aria-labelledby="mantenimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="contenedor-titulo">
                    <span id="mantenimientoModalLabel" class="titulo-modal">Detalles del Mantenimiento</span>
                    <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="mantenimientoForm">
                    <div class="form-group flex-grow-1 row">
                        <div class="col">
                            <label for="nombreActivo">Nombre del Activo</label>
                            <input type="text" id="nombreActivo" name="nombreActivo" class="form-control" readonly>
                        </div>
                        <div class="col">
                            <label for="estadoElemento">Estado del Elemento</label>
                            <input type="text" id="estadoElemento" name="estadoElemento" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group flex-grow-1 row">
                        <div class="col">
                            <label for="sedeMantenimiento">Sede</label>
                            <input type="text" id="sedeMantenimiento" name="sedeMantenimiento" class="form-control" readonly>
                        </div>
                        <div class="col">
                            <label for="clienteMantenimiento">Cliente</label>
                            <input type="text" id="clienteMantenimiento" name="clienteMantenimiento" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="observacionesReportadas">Observaciones Reportadas</label>
                        <textarea id="observacionesReportadas" name="observacionesReportadas" class="form-control" readonly></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-consultar" id="guardarMantenimientoBtn">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/js/gestion-activos.js') }}?v={{ time() }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activosTab = document.getElementById('pills-activos-tab');
            const mantenimientoTab = document.getElementById('pills-mantenimiento-tab');
            const crearActivoTab = document.querySelector('.crear-activo-tab');

            activosTab.addEventListener('click', function () {
                crearActivoTab.style.visibility = 'visible';
            });

            mantenimientoTab.addEventListener('click', function () {
                crearActivoTab.style.visibility = 'hidden';
            });
        });
    </script>
@endpush
