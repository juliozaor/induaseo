@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/alertas.css') }}?v={{ time() }}">

@section('content')
<div class="listado-container">
    <h2 class="listado-titulo">Listado de alertas</h2>
    <div class="divider"></div>

    <div class="busqueda-container">
        <div class="input-container">
            <input type="text" class="busqueda-input" id="busquedaTurnoInput" placeholder="Buscar...">
            <select id="clienteSelect" class="form-control">
                <option value="">Seleccione un cliente</option>
            </select>
            <select id="sedeSelect" class="form-control">
                <option value="">Seleccione una sede</option>
            </select>
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
                    <th>ID</th>
                    <th>Alerta</th>
                    <th>Fecha</th>
                    <th>Actividades</th>
                    <th>Turno</th>
                    <th>Cliente</th>
                    <th>Sede</th>
                    <th>Supervisor</th>
                    <th>Celular</th>
                    <th>Ver más</th>
                </tr>
            </thead>
            <tbody id="turnosTableBody">
                <!-- Los datos de la tabla se llenarán dinámicamente con JavaScript -->
            </tbody>

        </table>
    </div>
    <div id="alertasMensaje" class="mensaje">No hay registros para mostrar.</div>
    <div class="tabla-paginacion"></div>
</div>


<!-- Modal for Turno Details -->
<div class="modal fade" id="detalleTurnoModal" tabindex="-1" role="dialog" aria-labelledby="detalleTurnoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="contenedor-titulo">
                    <span id="detalleTurnoModalLabel" class="titulo-modal">Visualizar alerta</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                <div class="form-group col-md-3">
                    <label for="detalleAlerta">Alerta:</label>
                    <input type="text" id="detalleAlerta" class="form-control" readonly>
                </div>
                <div class="form-group col-md-3">
                    <label for="detalleFecha">Fecha:</label>
                    <input type="text" id="detalleFecha" class="form-control" readonly>
                </div>
                <div class="form-group col-md-3">
                    <label for="detalleActividades">Actividades:</label>
                    <input type="text" id="detalleActividades" class="form-control" readonly>
                </div>
                <div class="form-group col-md-3">
                    <label for="detalleTurno">Turno:</label>
                    <input id="detalleTurno" class="form-control" readonly></input>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="detalleSede">Sede:</label>
                    <input type="text" id="detalleSede" class="form-control" readonly>
                </div>
                <div class="form-group col-md-3">
                    <label for="detalleSupervisor">Supervisor:</label>
                    <input type="text" id="detalleSupervisor" class="form-control" readonly>
                </div>
                <div class="form-group col-md-3">
                    <label for="detalleCelular">Celular:</label>
                    <input type="text" id="detalleCelular" class="form-control" readonly>
                </div>
                <div class="form-group col-md-3">
                    <label for="detalleCliente">Cliente:</label>
                    <input id="detalleCliente" class="form-control" readonly></input>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="detalleObservaciones">Observaciones:</label>
                    <textarea id="detalleObservaciones" class="form-control" readonly></textarea>
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
<script src="{{ asset('assets/js/alertas.js') }}?v={{ time() }}"></script>
@endpush
