@extends('layouts.mobile')

@section('content')
    <!-- Imagen grande con texto centrado -->
    <div class="large-image-container">
        <img src="{{ asset('assets/images/fondo1.png') }}" alt="Large Image" class="large-image">
        <div class="titulo-super titulo-actividades">CONTROL DE INVENTARIO</div>
    </div>

    <!-- Rectángulo con icono y texto -->

    <ul class="nav nav-pills mb-3 rectangle2" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active " id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button"
                role="tab" aria-controls="pills-home" aria-selected="true"><img class="icono-actividades"
                    src="{{ asset('assets/icons/lista.svg') }}" alt="Icono" class="rectangle-icon"> Inventario
                inicial</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="pills-profile-tab " data-toggle="pill" data-target="#pills-profile" type="button"
                role="tab" aria-controls="pills-profile" aria-selected="false"><img class="icono-actividades"
                    src="{{ asset('assets/icons/lista.svg') }}" alt="Icono" class="rectangle-icon"> Inventario salida</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="contenedor-actividades">
                @if ($sedesInsumos->isNotEmpty())
                    @foreach ($sedesInsumos as $index => $sedesInsumo)
                        <a class="item-actividad" href="#" data-toggle="modal"
                            data-target="#actividadModal{{ $sedesInsumo->id }}">
                            <div class="contenedor-actividad">

                                <span><img src="{{ $sedesInsumo->insumo->imagen }}"
                                        alt="{{ $sedesInsumo->insumo->nombre_elemento }}"
                                        class="imagen-insumo">{{ $sedesInsumo->insumo->nombre_elemento }}</span> <span
                                    class="flecha">></span>
                            </div>
                        </a>
                        <!-- Modal -->
                        <div class="modal fade" id="actividadModal{{ $sedesInsumo->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="actividadModalLabel{{ $sedesInsumo->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="actividadModalLabel{{ $sedesInsumo->id }}">
                                            {{ $sedesInsumo->insumo->nombre_elemento }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="contenedor-imagen-modal">
                                            <img src="{{ $sedesInsumo->insumo->imagen }}"
                                                alt="{{ $sedesInsumo->insumo->nombre_elemento }}" class="img-fluid">
                                        </div>
                                        <p>Tipo: {{ $sedesInsumo->insumo->estados->nombre }}</p>
                                        <p>Cantidad: {{ $sedesInsumo->cantidad }}</p>
                                        <div class="form-group">
                                            <label for="novedades{{ $sedesInsumo->id }}">Novedades</label>
                                            <select class="form-control" id="novedades{{ $sedesInsumo->id }}">
                                                <option value="novedad1">Novedad 1</option>
                                                <option value="novedad2">Novedad 2</option>
                                                <option value="novedad3">Novedad 3</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="observaciones{{ $sedesInsumo->id }}">Observaciones</label>
                                            <textarea class="form-control" id="observaciones{{ $sedesInsumo->id }}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
              {{--   @if ($sedesAcivos->isNotEmpty())
                    @foreach ($sedesAcivos as $index => $sedesActivo)
                        <a class="item-actividad" href="#" data-toggle="modal"
                            data-target="#actividadModal{{ $sedesActivo->id }}">
                            <div class="contenedor-actividad">

                                <span><img src="{{ $sedesActivo->activo->imagen }}"
                                        alt="{{ $sedesActivo->activo->nombre_elemento }}"
                                        class="imagen-insumo">{{ $sedesActivo->activo->nombre_elemento }}</span> <span
                                    class="flecha">></span>
                            </div>
                        </a>
                        <div class="modal fade" id="actividadModal{{ $sedesActivo->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="actividadModalLabel{{ $sedesActivo->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="actividadModalLabel{{ $sedesActivo->id }}">
                                            {{ $sedesActivo->activo->nombre_elemento }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="contenedor-imagen-modal">
                                            <img src="{{ $sedesActivo->activo->imagen }}"
                                                alt="{{ $sedesActivo->activo->nombre_elemento }}" class="img-fluid">
                                        </div>
                                        <p>Tipo: {{ $sedesActivo->activo->estados->nombre }}</p>
                                        <p>Cantidad: {{ $sedesActivo->cantidad }}</p>
                                        <div class="form-group">
                                            <label for="novedades{{ $sedesActivo->id }}">Novedades</label>
                                            <select class="form-control" id="novedades{{ $sedesActivo->id }}">
                                                <option value="novedad1">Novedad 1</option>
                                                <option value="novedad2">Novedad 2</option>
                                                <option value="novedad3">Novedad 3</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="observaciones{{ $sedesActivo->id }}">Observaciones</label>
                                            <textarea class="form-control" id="observaciones{{ $sedesActivo->id }}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif --}}
            </div>
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
            <div class="contenedor-actividades">
                <h5>pagina 2</h5>
            </div>
        </div>
    </div>

@endsection
