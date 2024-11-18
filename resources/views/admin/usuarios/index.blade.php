<!-- filepath: /c:/laragon/www/induaseo/resources/views/admin/usuarios/index.blade.php -->
@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/usuario.css') }}?v={{ time() }}">
@section('content')
<div class="btn-nuevo">
    <button class="btn-consultar" id="openUserModalBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
            <g id="Grupo_22674" data-name="Grupo 22674" transform="translate(-19 -11.164)">
                <line id="Línea_338" data-name="Línea 338" y1="15" transform="translate(27.5 12.164)"
                    fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2" />
                <line id="Línea_339" data-name="Línea 339" y1="15"
                    transform="translate(35 19.664) rotate(90)" fill="none" stroke="#fff" stroke-linecap="round"
                    stroke-width="2" />
            </g>
        </svg>
        Nuevo Usuario</button>
</div>

<!-- Listado de Usuarios -->
<div class="listado-container">
    <h2 class="listado-titulo">Listado de Usuarios</h2>
    <!-- Línea divisoria -->
    <div class="divider"></div>

    <div class="busqueda-container">
        <div class="input-container">
            <input type="text" class="busqueda-input" id="busquedaUsuarioInput" placeholder="Buscar...">
        </div>
        <span class="registros-encontrados">Total: 0</span>
        <select class="registros-por-pagina" id="registrosUsuarioPorPagina">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>

    <!-- Tabla de datos -->
    <div class="tabla-container">
        <table class="tabla" id="tablaUsuarios">
            <thead>
                <tr>
                    <th>Identificación</th>
                    <th>Nombres y Apellidos</th>
                    <th>Perfil</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Correo Electrónico</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="tabla-paginacion"></div>
</div>

<div id="createUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="contenedor-titulo">
                <span id="userModalTitle" class="titulo-modal">Crear nuevo usuario</span>
                <span class="subtitulo-modal">- Todos los campos son obligatorios</span>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeUserModal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="userForm">
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="perfil">Perfil:</label>
                        <select id="perfil" name="perfil" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Opciones de roles -->
                        </select>
                        <span class="error-message" id="errorPerfil"></span>
                    </div>
                    <div class="col-3">
                        <label for="tipoIdentificacion">Tipo de Identificación:</label>
                        <select id="tipoIdentificacion" name="tipoIdentificacion" class="form-control" required>
                            <option value="">Seleccione</option>
                            <!-- Opciones de tipos de documentos -->
                        </select>
                        <span class="error-message" id="errorTipoIdentificacion"></span>
                    </div>
                    <div class="col-3">
                        <label for="numeroIdentificacion">Número de Identificación:</label>
                        <input type="text" id="numeroIdentificacion" name="numeroIdentificacion" class="form-control" required>
                        <span class="error-message" id="errorNumeroIdentificacion"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="nombres">Nombres:</label>
                        <input type="text" id="nombres" name="nombres" class="form-control" required>
                        <span class="error-message" id="errorNombres"></span>
                    </div>
                    <div class="col-3">
                        <label for="apellidos">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" class="form-control" required>
                        <span class="error-message" id="errorApellidos"></span>
                    </div>
                    <div class="col-3">
                        <label for="fechaNacimiento">Fecha de Nacimiento:</label>
                        <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="form-control" required>
                        <span class="error-message" id="errorFechaNacimiento"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row">
                    <div class="col-3">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" class="form-control">
                        <span class="error-message" id="errorTelefono"></span>
                    </div>
                    <div class="col-3">
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" id="correo" name="correo" class="form-control" required>
                        <span class="error-message" id="errorCorreo"></span>
                    </div>
                    <div class="col-3">
                        <label for="cargo">Cargo:</label>
                        <input type="text" id="cargo" name="cargo" class="form-control">
                        <span class="error-message" id="errorCargo"></span>
                    </div>
                </div>
                <div class="form-group flex-grow-1 row" id="clienteSelectContainer" style="display: none;">
                    <div class="col-3">
                        <label for="cliente_id">Cliente:</label>
                        <select id="cliente_id" name="cliente_id" class="form-control">
                            <option value="">Seleccione</option>
                            <!-- Opciones de clientes -->
                        </select>
                        <span class="error-message" id="errorClienteId"></span>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button id="userModalActionBtn" class="btn-consultar">Crear Usuario</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/usuario.js') }}?v={{ time() }}"></script>
@endpush