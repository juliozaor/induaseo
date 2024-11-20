<!-- filepath: /c:/laragon/www/induaseo/resources/views/asignar-turnos/index.blade.php -->
@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/asignar-turnos.css') }}?v={{ time() }}">

@section('content')
    <div class="select-container">
        <div class="select-container2 row">
            <div class="col-md-5">
                <label for="clienteSelect">Cliente:</label>
                <select id="clienteSelect" class="form-control">
                    <option value="">Seleccione un cliente</option>
                    @foreach($clientes as $cliente)
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

    <div class="btn-nuevo">
        <button id="nuevaAsignacionBtn" class="btn-consultar" data-toggle="modal" data-target="#asignarTurnoModal" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
                <g id="Grupo_22674" data-name="Grupo 22674" transform="translate(-19 -11.164)">
                    <line id="Línea_338" data-name="Línea 338" y1="15" transform="translate(27.5 12.164)"
                        fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2" />
                    <line id="Línea_339" data-name="Línea 339" y1="15"
                        transform="translate(35 19.664) rotate(90)" fill="none" stroke="#fff" stroke-linecap="round"
                        stroke-width="2" />
                </g>
            </svg>
            Nueva Asignación
        </button>
    </div>

    

<div class="listado-container">
    <h2 class="listado-titulo">Asignación de Turnos</h2>
    <div class="divider"></div>

    <div class="busqueda-container">
        <div class="input-container">
            <input type="text" class="busqueda-input" id="busquedaTurnoInput" placeholder="Buscar...">
        </div>
        <span class="registros-encontrados">Total: 0</span>
        <select class="registros-por-pagina" id="registrosTurnoPorPagina">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>

    <div class="tabla-container">
        <table class="tabla" id="tablaTurnos">
            <thead>
                <tr>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Identificación</th>
                    <th>Nombres y Apellidos</th>
                    <th>Turno</th>
                    <th>Cant de Actividades</th>
                </tr>
            </thead>
            <tbody id="turnosTableBody">
                <!-- Datos de turnos -->
            </tbody>
        </table>
    </div>
    <div class="tabla-paginacion"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="asignarTurnoModal" tabindex="-1" role="dialog" aria-labelledby="asignarTurnoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="contenedor-titulo">
                    <span id="asignarTurnoModalLabel" class="titulo-modal">Asignar Turno</span>
                    <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="asignarTurnoForm">
                    <div class="form-group flex-grow-1 row">
                        <div class="col">
                            <label for="supervisorSelect">Supervisor:</label>
                            <select id="supervisorSelect" class="form-control">
                                <!-- Opciones de supervisores -->
                            </select>
                            <span class="error-message" id="errorSupervisorSelect"></span>
                        </div>
                        <div class="col">
                            <label for="sedeInput">Sede:</label>
                            <input type="text" id="sedeInput" class="form-control" readonly>
                            <span class="error-message" id="errorSedeInput"></span>
                        </div>
                    </div>
                    <h5>Turno de supervisión</h5>
                    <div class="form-group flex-grow-1 row">
                        <div class="col">
                            <label for="fechaInicioInput">Fecha Inicio:</label>
                            <input type="date" id="fechaInicioInput" class="form-control">
                            <span class="error-message" id="errorFechaInicioInput"></span>
                        </div>
                        <div class="col">
                            <label for="fechaFinInput">Fecha Fin:</label>
                            <input type="date" id="fechaFinInput" class="form-control">
                            <span class="error-message" id="errorFechaFinInput"></span>
                        </div>
                        <div class="col">
                            <label for="turnoSelect">Turno:</label>
                            <select id="turnoSelect" class="form-control">
                                <!-- Opciones de turnos -->
                            </select>
                            <span class="error-message" id="errorTurnoSelect"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-consultar" id="guardarTurnoBtn">Guardar</button>
            </div>
        </div>
    </div>
</div>

              <!-- Modal for Assigned Tasks -->
<div class="modal fade" id="tareasModal" tabindex="-1" role="dialog" aria-labelledby="tareasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="contenedor-titulo">
                    <span id="tareasModalLabel" class="titulo-modal">Tareas Asignadas</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tareasForm">
                    <div class="form-group">
                        <label for="supervisorNombre">Supervisor:</label>
                        <input type="text" id="supervisorNombre" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="sedeNombre">Sede:</label>
                        <input type="text" id="sedeNombre" class="form-control" readonly>
                    </div>
                    <h5>Tareas asignadas</h5>
                    <div class="tabla-container tabla-tareas">
                        <table class="tabla" id="tablaTareas">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tareasTableBody">
                                <!-- Datos de tareas -->
                            </tbody>
                        </table>
                    </div>
                    <div id="actividadSection">
                        <div class="form-group">
                            <label for="nuevaTareaNombre">Nueva Tarea:</label>
                            <div class="select-container2">
                                <input type="text" id="nuevaTareaNombre" class="form-control" placeholder="Nombre de la tarea">
                                <input type="text" id="nuevaTareaDescripcion" class="form-control" placeholder="Descripción de la tarea">
                                <button id="agregarTareaBtn" class="btn-actividad" style="min-width: 130px;">Agregar Tarea</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-consultar" id="volverBtn">Volver</button>
                <button type="button" class="btn-consultar" id="guardarTareaBtn">Guardar y finalizar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/asignar-turnos.js') }}?v={{ time() }}"></script>
@endpush