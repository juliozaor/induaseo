@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/actividades-evidencias.css') }}?v={{ time() }}">

@section('content')
    <div class="select-container">
        <div class="select-container2 row">
            <div class="col-md-10">
                <label for="sedeSelect">Sedes:</label>
                <select id="sedeSelect" class="form-control">
                    <option value="">Seleccione una sede</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button id="consultarBtn" class="btn-consultar">Consultar</button>
            </div>
        </div>
    </div>


<div class="listado-container">
    <h2 class="listado-titulo">Listado de tareas y evidencias</h2>
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
                    <th>Fecha</th>
                    <th>Turno</th>
                    <th>Regional</th>
                    <th>Actividades completadas</th>
                    <th>Supervisor</th>
                    <th>Observaciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="turnosTableBody">
                <!-- Datos de turnos -->
            </tbody>
        </table>
    </div>
    <div class="tabla-paginacion"></div>
</div>


<!-- Modal for Turno Details -->
<div class="modal fade" id="detalleTurnoModal" tabindex="-1" role="dialog" aria-labelledby="detalleTurnoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="contenedor-titulo">
                    <span id="detalleTurnoModalLabel" class="titulo-modal">Detalle del Turno</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="detalleSupervisor">Supervisor:</label>
                    <input type="text" id="detalleSupervisor" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="detalleSede">Sede:</label>
                    <input type="text" id="detalleSede" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="detalleFecha">Fecha:</label>
                    <input type="text" id="detalleFecha" class="form-control" readonly>
                </div>
                <h5>Actividades</h5>
                <div class="tabla-container">
                    <table class="tabla" id="tablaActividades">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Area</th>
                                <th>Actividad</th>
                                {{-- <th>Descripción</th> --}}
                                <th>Estado</th>
                                <th>Calificación</th>
                            </tr>
                        </thead>
                        <tbody id="actividadesTableBody">
                            <!-- Datos de actividades -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-consultar" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/actividades-evidencias.js') }}?v={{ time() }}"></script>
@endpush
