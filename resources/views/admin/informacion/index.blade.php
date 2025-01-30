@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/informacion.css') }}?v={{ time() }}">
@section('content')
<div class="btn-nuevo">
    <button class="btn-consultar" id="openInformacionModalBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
            <g id="Grupo_22674" data-name="Grupo 22674" transform="translate(-19 -11.164)">
                <line id="Línea_338" data-name="Línea 338" y1="15" transform="translate(27.5 12.164)" fill="none"
                    stroke="#fff" stroke-linecap="round" stroke-width="2" />
                <line id="Línea_339" data-name="Línea 339" y1="15" transform="translate(35 19.664) rotate(90)"
                    fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2" />
            </g>
        </svg>
        Nueva Información
    </button>
</div>

<!-- Listado de Información -->
<div class="listado-container">
    <h2 class="listado-titulo">Listado de Información</h2>
    <hr>
    <div class="busqueda-container">
        <div class="input-container">
            <input type="text" class="busqueda-input" id="busquedaInformacionInput" placeholder="Buscar...">
        </div>
        <span class="registros-encontrados">Total: 0</span>
        <select class="registros-por-pagina" id="registrosInformacionPorPagina">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>

    <div class="tabla-container">
        <table class="tabla" id="tablaInformacion">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Cliente</th>
                    <th>Sede</th>
                    <th>Tipo Multimedia</th>
                    <th>Categoría</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="tabla-paginacion"></div>
</div>

<div id="createInformacionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="contenedor-titulo">
                <span id="informacionModalTitle" class="titulo-modal">Crear nueva información</span>
                <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeInformacionModal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="informacionForm">
                <div class="form-group flex-grow-1 row">
                    <div class="col-6">
                        <label for="cliente_id">Cliente:</label>
                        <select id="cliente_id" name="cliente_id" class="form-control">
                            <option value="">Seleccione</option>
                            <!-- Opciones de clientes -->
                        </select>
                        <span class="error-message" id="errorClienteId"></span>
                    </div>
                    <div class="col-6">
                        <label for="sede_id">Sede:</label>
                        <select id="sede_id" name="sede_id" class="form-control">
                            <option value="">Seleccione</option>
                            <!-- Opciones de sedes -->
                        </select>
                        <span class="error-message" id="errorSedeId"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row">
                    <div class="col-6">
                        <label for="tipo_multimedia_id">Tipo de Multimedia:</label>
                        <select id="tipo_multimedia_id" name="tipo_multimedia_id" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Opciones de tipos de multimedia -->
                        </select>
                        <span class="error-message" id="errorTipoMultimediaId"></span>
                    </div>
                    <div class="col-6">
                        <label for="categoria_id">Categoría:</label>
                        <select id="categoria_id" name="categoria_id" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Opciones de categorías -->
                        </select>
                        <span class="error-message" id="errorCategoriaId"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="titulo">Título:</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" required>
                        <span class="error-message" id="errorTitulo"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row">
                    <div class="col-12">
                        <label for="descripcion" class="form-label">Desripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        <span class="error-message" id="errorDescripcion"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row">
                    <div class="col-12">
                        <label for="url" class="form-label">Adjuntar multimedia <span class="subtitulo-modal">- Formatos permitidos: PDF, MP4, PNG, JPG</span></label>
                        <input class="form-control" type="file" id="url" name="url">
                        <span class="error-message" id="errorUrl"></span>
                        <span id="fileLabel" class="file-label"></span> <!-- Add this line -->
                    </div>
                </div>
                
            </form>
        </div>
        <div class="modal-footer">
            <button id="informacionModalActionBtn" class="btn-consultar">Crear Información</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/informacion.js') }}?v={{ time() }}"></script>
@endpush
