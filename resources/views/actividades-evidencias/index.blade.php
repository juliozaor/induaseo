@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/actividades-evidencias.css') }}?v={{ time() }}">

@section('content')
    <div class="select-container">
        <div class="select-container2 row">
            <div class="col-md-10">
                <label for="sedeSelect">Sede:</label>
                <select id="sedeSelect" class="form-control">
                    <option value="">Seleccione una sede</option>
                    @foreach ($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                    @endforeach
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
                        <!-- Datos de actividades -->
                    </tbody>
                </table>
                <div id="actividadesPaginacion" class="paginacion"></div>
                <div id="actividadesMensaje" class="mensaje">No hay registros para mostrar.</div>
            </div>
        </div>

        <div class="listado-container">
            <h2 class="listado-titulo">Listado de turnos</h2>
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
            <div class="paginacion" id="turnosPaginacion"></div>
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

    <!-- Modal for Survey -->
    <div class="modal fade" id="encuestaModal" tabindex="-1" role="dialog" aria-labelledby="encuestaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="encuestaModalLabel" style="color: #EC6F35;">Encuesta de Satisfacción del Servicio de Limpieza Industrial</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="encuestaForm">
                        <div class="form-group">
                            <label>1. ¿Qué tan satisfecho está con el servicio de limpieza recibido?</label>
                            <select class="form-control">
                                <option>Muy satisfecho</option>
                                <option>Satisfecho</option>
                                <option>Neutral</option>
                                <option>Insatisfecho</option>
                                <option>Muy insatisfecho</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>2. ¿Cómo calificaría la puntualidad del equipo de limpieza?</label>
                            <select class="form-control">
                                <option>Excelente</option>
                                <option>Buena</option>
                                <option>Regular</option>
                                <option>Mala</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>3. ¿El personal de limpieza fue profesional y respetuoso?</label>
                            <select class="form-control">
                                <option>Sí</option>
                                <option>No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>4. ¿Se cumplieron sus expectativas en cuanto a la calidad de la limpieza?</label>
                            <select class="form-control">
                                <option>Superó expectativas</option>
                                <option>Cumplió expectativas</option>
                                <option>No cumplió expectativas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>5. ¿Recomendaría nuestro servicio a otras empresas?</label>
                            <select class="form-control">
                                <option>Sí</option>
                                <option>No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>6. ¿Hay algún aspecto en el que podamos mejorar?</label>
                            <textarea class="form-control" rows="3" maxlength="500"></textarea>
                        </div>
                        <div class="form-group">
                            <label>7. Calificar desempeño general, siendo 0 malo y 10 muy bueno</label>
                            <div class="d-flex justify-content-between">
                                @for ($i = 0; $i <= 10; $i++)
                                    <label>
                                        <input type="radio" name="calificacion" value="{{ $i }}"> {{ $i }}
                                    </label>
                                @endfor
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-consultar" data-dismiss="modal">Cerrar</button>
                    <button id='enviarEncuesta' type="submit" class="btn-consultar">Enviar</button>
                </div>
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
                                    <th>Area</th>
                                    <th>Actividad</th>
                                    {{-- <th>Descripción</th> --}}
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
<script src="{{ asset('assets/js/actividades-evidencias.js') }}?v={{ time() }}"></script>
@endpush
