@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/reportes.css') }}?v={{ time() }}">

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

    <div class="container-body mt-3">
        <div class="listado-container">
            <h2 class="listado-titulo">Actividades</h2>
            <div class="filter-container">
                <div class="d-flex">
                    <div class="inputfecha">
                        <label for="fechaInicio">Fecha Inicio:</label>
                        <input type="date" id="fechaInicio" class="form-control">
                    </div>
                    <div class="inputfecha">
                        <label for="fechaFin">Fecha Fin:</label>
                        <input type="date" id="fechaFin" class="form-control">
                    </div>
                </div>
                <div class="btn-container">
                    <button id="aplicarFiltroBtn" class="btn-consultar">Filtrar</button>
                    <button id="limpiarFiltroBtn" class="btn-consultar">Limpiar Filtro</button>
                    <button id="exportarBtn" class="btn-consultar">Exportar Actividades</button>
                    {{-- <a href="#" id="exportarActivosBtn" class="btn-consultar">Exportar Activos</a> --}}
                </div>
            </div>
            <div class="tabla-container">
                <table class="tabla" id="tablaActividades">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Área</th>
                            <th>Actividad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="actividadesTableBody">
                        <!-- Datos de turnos -->
                    </tbody>
                </table>
                <div id="actividadesPaginacion" class="paginacion"></div>
                <div id="actividadesMensaje" class="mensaje">No hay registros para mostrar.</div>
            </div>
            <div class="activos-paginacion"></div>
        </div>

        <div class="listado-container">
            <h2 class="listado-titulo">Activos</h2>
            <div class="d-flex justify-content-end mb-2">
                <button id="exportarActivosBtn" class="btn-consultar">Exportar Activos</button>
            </div>
            <div class="tabla-container">
                <table class="tabla" id="tablaActivos">
                    <thead>
                        <tr>
                            <th>Activo</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody id="activosTableBody">
                        <!-- Datos de turnos -->
                    </tbody>
                </table>
                <div id="activosPaginacion" class="paginacion"></div>
                <div id="activosMensaje" class="mensaje">No hay registros para mostrar.</div>
            </div>
            <div class="activos-paginacion"></div>
            <hr>
            <h2 class="listado-titulo">Insumos</h2>
            <div class="d-flex justify-content-end mb-2">
                <button id="exportarInsumosBtn" class="btn-consultar">Exportar Insumos</button>
            </div>
            <div class="tabla-container">
                <table class="tabla" id="tablaInsumos">
                    <thead>
                        <tr>
                            <th>Insumo</th>
                            <th>Cantidad</th>
                            {{-- <th>Estado</th>
                            <th>Observación</th> --}}
                        </tr>
                    </thead>
                    <tbody id="insumosTableBody">
                        <!-- Datos de insumos -->
                    </tbody>
                </table>
                <div id="insumosPaginacion" class="paginacion"></div>
                <div id="insumosMensaje" class="mensaje">No hay registros para mostrar.</div>
            </div>
            <div class="insumos-paginacion"></div>
        </div>

        <div class="listado-container2">
            <h2 class="listado-titulo">Consolidado</h2>
            <div id="consolidado">
                <ul id="turnoMenu" class="menu">
                    <!-- Turnos se agregarán aquí dinámicamente -->
                </ul>
            </div>
        </div>

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
                                    <th>Área</th>
                                    <th>Actividad</th>
                                    <th>Estado</th>
                                    <th>Calificación</th>
                                </tr>
                            </thead>
                            <tbody id="actividadesTableBody2">
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

    <!-- Modal for Activity Details -->
    <div class="modal fade" id="detalleActividadModal" tabindex="-1" role="dialog" aria-labelledby="detalleActividadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="contenedor-titulo">
                        <span id="detalleActividadModalLabel" class="titulo-modal">Detalle de la Actividad</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="detalleActividadNombre">Nombre:</label>
                        <input type="text" id="detalleActividadNombre" class="form-control" readonly>
                    </div>
                    <div class="form-group d-flex justify-content-between">
                        <div style="flex: 1; margin-right: 10px;">
                            <label for="detalleActividadEstado">Estado:</label>
                            <input type="text" id="detalleActividadEstado" class="form-control" readonly>
                        </div>
                        <div style="flex: 1;">
                            <label for="detalleActividadCalificacion">Calificación:</label>
                            <input type="text" id="detalleActividadCalificacion" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Evidencias:</label>
                        <div id="detalleActividadImagenes" class="d-flex flex-wrap">
                            <!-- Imágenes se agregarán aquí dinámicamente -->
                        </div>
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
    <script src="{{ asset('assets/js/reportes.js') }}?v={{ time() }}"></script>
@endpush
