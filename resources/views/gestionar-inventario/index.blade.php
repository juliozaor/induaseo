@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/gestion-inventario.css') }}?v={{ time() }}">

@section('content')
    <!-- Pestañas de navegación -->
    <ul class="nav nav-pills p-3" id="pills-tab" role="tablist" style="background-color: #E8E8E8;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-inventario-tab" data-toggle="pill" data-target="#pills-inventario" type="button" role="tab" aria-controls="pills-inventario" aria-selected="true">Inventario</button>
        </li>
        <!--<li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-mantenimiento-tab" data-toggle="pill" data-target="#pills-mantenimiento" type="button" role="tab" aria-controls="pills-mantenimiento" aria-selected="false">Solicitudes</button>
        </li>-->
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="pills-tabContent">
        <!-- Pestaña de Inventario -->
        <div class="tab-pane fade show active" id="pills-inventario" role="tabpanel" aria-labelledby="pills-inventario-tab">
            <!-- Botón para asignar nuevo inventario -->
            <div class="btn-nuevo">
                <button id="crearInventarioBtn" class="btn-consultar" data-toggle="modal" data-target="#crearInventarioModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
                        <g id="Grupo_22674" data-name="Grupo 22674" transform="translate(-19 -11.164)">
                            <line id="Línea_338" data-name="Línea 338" y1="15" transform="translate(27.5 12.164)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2" />
                            <line id="Línea_339" data-name="Línea 339" y1="15" transform="translate(35 19.664) rotate(90)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2" />
                        </g>
                    </svg>
                    Asignar
                </button>
            </div>

            <!-- Contenedor de la lista de inventarios -->
            <div class="listado-container">
                <h2 class="listado-titulo">Lista de inventarios</h2>
                <div class="divider"></div>

                <!-- Barra de búsqueda -->
                <div class="busqueda-container">
                    <div class="input-container">
                        <input type="text" class="busqueda-input" id="busquedaInventarioInput" placeholder="Buscar...">
                    </div>
                    <span class="registros-encontrados">Total: 0</span>
                    <select class="registros-por-pagina" id="registrosInventarioPorPagina">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <!-- Tabla de inventarios -->
                <div class="tabla-container">
                    <table class="tabla" id="tablaInventarios">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Sede</th>
                                <th>Cliente</th>
                                <th>Creado por</th>
                                <th>Última actualización</th>
                                <th>Editado por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="inventariosTableBody">
                            <!-- Datos de inventarios -->
                        </tbody>
                    </table>
                </div>
                <div class="tabla-paginacion"></div>
            </div>

            <!-- Modal para crear/editar inventario -->
            <div class="modal fade" id="crearInventarioModal" tabindex="-1" role="dialog" aria-labelledby="crearInventarioModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="contenedor-titulo">
                                <span id="crearInventarioModalLabel" class="titulo-modal">Asignar</span>
                                <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="crearInventarioForm">
                                <input type="hidden" id="inventarioId" name="inventarioId">
                                <div class="form-group flex-grow-1 row">
                                    <div class="col">
                                        <label for="clienteSelect">Cliente</label>
                                        <select id="clienteSelect" name="clienteSelect" class="form-control">
                                            <option value="">Seleccione un cliente</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <span class="error-message" id="errorClienteSelect"></span>
                                    </div>
                                    <div class="col">
                                        <label for="sedeSelect">Asignar sede</label>
                                        <select id="sedeSelect" name="sedeSelect" class="form-control" >
                                            <option value="">Seleccione una o varias sedes</option>
                                        </select>
                                        <div class="selected-options mt-3" id="selectedOptions"></div>
                                        <span class="error-message" id="errorSedeSelect"></span>
                                    </div>
                                </div>
                                <div class="form-group flex-grow-1 row">
                                    <div class="col">
                                        <label for="itemSelect">Nombre artículo</label>
                                        <select id="itemSelect" name="itemSelect" class="form-control">
                                            <!-- Opciones de artículos se llenarán dinámicamente -->
                                        </select>
                                        <span class="error-message" id="errorItemSelect"></span>
                                    </div>
                                    <div class="col">
                                        <label for="codigoInput">Código producto</label>
                                        <input type="text" id="codigoInput" name="codigoInput" class="form-control" readonly>
                                        <span class="error-message" id="errorCodigoInput"></span>
                                    </div>
                                    <div class="col">
                                        <label for="cantidadInput">Cantidad <span id="cantidadDisponible" class="text-muted"></span></label>
                                        <input type="number" id="cantidadInput" class="form-control" name="cantidad" min="1">
                                        <span class="error-message" id="errorCantidadInput"></span>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            {{-- <label class="switch">
                                <input type="checkbox" id="estadoInventarioToggle" checked>
                                <span class="slider round"></span>
                            </label>
                            <label for="estadoInventarioToggle">Activo</label> --}}
                            <button type="button" class="btn-consultar" id="guardarInventarioBtn">Asignar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pestaña de Mantenimiento -->
        <div class="tab-pane fade" id="pills-mantenimiento" role="tabpanel" aria-labelledby="pills-mantenimiento-tab"> dos </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/gestion-inventario.js') }}?v={{ time() }}"></script>
@endpush
