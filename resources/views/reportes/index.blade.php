@extends('layouts.dashboard')
<link rel="stylesheet" href="{{ asset('assets/css/reportes.css') }}?v={{ time() }}">

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
                <button id="limpiarFiltroBtn" class="btn-consultar">Limpiar Filtro</button>
                <button id="limpiarFiltroBtn" class="btn-consultar">Limpiar Filtro</button>
        </div>
    </div>

<div class="container-body mt-3">
<div class="listado-container">
    <h2 class="listado-titulo">Actividades</h2>
    <div class="tabla-container">
        <table class="tabla" id="tablaActividades">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Actividades finalizadas</th>
                    <th>Actividades sin finalizar</th>
                </tr>
            </thead>
            <tbody id="actividadesTableBody">
                <!-- Datos de turnos -->
            </tbody>
        </table>
    </div>
    <div class="actividades-paginacion"></div>
</div>

{{-- <div class="listado-container">
    <h2 class="listado-titulo">Activos</h2>
    <div class="tabla-container">
        <table class="tabla" id="tablaActividades">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>En buen estado</th>
                    <th>Mal estado</th>
                    <th>Mtto programados</th>
                </tr>
            </thead>
            <tbody id="actividadesTableBody">
                <!-- Datos de turnos -->
            </tbody>
        </table>
    </div>
    <div class="actividades-paginacion"></div>
</div> --}}

</div>



@endsection

@push('scripts')
<script src="{{ asset('assets/js/reportes.js') }}?v={{ time() }}"></script>
@endpush