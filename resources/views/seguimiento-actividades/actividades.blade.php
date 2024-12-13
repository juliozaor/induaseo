@extends('layouts.mobile')

@section('content')
    <!-- Imagen grande con texto centrado -->
    <div class="large-image-container">
        <img src="{{ asset('assets/images/fondo1.png') }}" alt="Large Image" class="large-image">
        <div class="titulo-super titulo-actividades">ACTIVIDADES ASIGNADAS</div>
    </div>

    <!-- Rectángulo con icono y texto -->

    <ul class="nav nav-pills mb-3 rectangle2" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active " id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button"
                role="tab" aria-controls="pills-home" aria-selected="true"><img class="icono-actividades"
                    src="{{ asset('assets/icons/lista.svg') }}" alt="Icono" class="rectangle-icon"> Actividades
                asignadas</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="pills-profile-tab " data-toggle="pill" data-target="#pills-profile" type="button"
                role="tab" aria-controls="pills-profile" aria-selected="false"><img class="icono-actividades"
                    src="{{ asset('assets/icons/check.svg') }}" alt="Icono" class="rectangle-icon"> Tareas
                finalizadas</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="contenedor-actividades">
                @if ($actividadesTrue->isNotEmpty())
                    @foreach ($actividadesTrue as $index => $actividad)
                    <a class="item-actividad" href="#" data-toggle="modal" data-target="#actividadModal{{ $actividad->id }}">
                        <div class="contenedor-actividad">
                            <span>{{ $index + 1 }}. {{ $actividad->nombre }}</span> <span class="flecha">></span>
                        </div>
                    </a>

                    <!-- Modal -->
                    <div class="modal fade" id="actividadModal{{ $actividad->id }}" tabindex="-1" role="dialog" aria-labelledby="actividadModalLabel{{ $actividad->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="actividadModalLabel{{ $actividad->id }}">{{ $actividad->nombre }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ $actividad->descripcion }}</p>
                                    <form action="{{ route('guardarCalificacion', $actividad->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <button class="btn boton-secundario" type="button" onclick="document.getElementById('evidenciaInput{{ $actividad->id }}').click()">Agregar Evidencia <svg xmlns="http://www.w3.org/2000/svg" width="24.953" height="19.677" viewBox="0 0 24.953 19.677">
                                            <g id="Grupo_23192" data-name="Grupo 23192" transform="translate(0)">
                                              <path id="Trazado_17437" data-name="Trazado 17437" d="M-539.182,550.077q-4.791,0-9.581,0a2.885,2.885,0,0,1-2.79-2.09,2.459,2.459,0,0,1-.089-.648q0-5.97,0-11.94a2.815,2.815,0,0,1,2.751-2.756c1.208-.011,2.416-.008,3.623,0,.234,0,.328-.063.344-.309a2.192,2.192,0,0,1,1.827-1.885,7.257,7.257,0,0,1,.848-.045q3.222,0,6.444,0a2.4,2.4,0,0,1,1.466.43,2.1,2.1,0,0,1,.894,1.525c.025.242.122.283.337.281,1.175-.009,2.351-.007,3.526,0a2.819,2.819,0,0,1,2.849,2.39,2.967,2.967,0,0,1,.025.509c0,3.875-.023,7.75.014,11.624a2.925,2.925,0,0,1-2.935,2.92c-2.586-.017-5.172,0-7.757,0Zm0-1.331H-536c2.123,0,4.246-.005,6.37,0a1.547,1.547,0,0,0,1.346-.638,1.793,1.793,0,0,0,.289-1.109q0-4.121,0-8.242c0-1.118.006-2.237-.006-3.355a1.41,1.41,0,0,0-.813-1.287,1.95,1.95,0,0,0-.945-.179c-1.394,0-2.788,0-4.182,0-.545,0-.776-.237-.777-.788,0-.13,0-.259,0-.389-.009-.735-.342-1.064-1.08-1.064h-6.418c-.154,0-.308,0-.462,0a.868.868,0,0,0-.91.924c0,.154,0,.308,0,.462,0,.616-.237.853-.85.853-1.4,0-2.8,0-4.206,0a1.539,1.539,0,0,0-1.7,1.593q-.008,5.823,0,11.645a1.528,1.528,0,0,0,.169.751,1.529,1.529,0,0,0,1.461.82Q-543.947,548.739-539.182,548.746Z" transform="translate(551.643 -530.404)" />
                                              <path id="Trazado_17438" data-name="Trazado 17438" d="M-409.693,635.327a5.851,5.851,0,0,1,5.844,5.849,5.863,5.863,0,0,1-5.871,5.852,5.865,5.865,0,0,1-5.831-5.868A5.847,5.847,0,0,1-409.693,635.327Zm-.02,10.372a4.482,4.482,0,0,0,4.525-4.437,4.471,4.471,0,0,0-4.52-4.574,4.462,4.462,0,0,0-4.5,4.493A4.478,4.478,0,0,0-409.713,645.7Z" transform="translate(422.169 -630.224)" />
                                              <path id="Trazado_17439" data-name="Trazado 17439" d="M-144.1,635.889a1.188,1.188,0,0,1-1.174,1.183,1.214,1.214,0,0,1-1.192-1.2,1.21,1.21,0,0,1,1.209-1.182A1.185,1.185,0,0,1-144.1,635.889Z" transform="translate(166.173 -629.618)" />
                                            </g>
                                          </svg>
                                          </button>
                                        <input type="file" id="evidenciaInput{{ $actividad->id }}" name="evidencias[]" accept="image/jpeg, image/png" style="display: none;" multiple onchange="handleFileSelect(event, {{ $actividad->id }})">
                                        <div class="evidencias" id="evidenciasContainer{{ $actividad->id }}">
                                            <!-- Miniaturas de las evidencias cargadas -->
                                        </div>
                                        <span class="mt-3">Calificar</span>
                                        <div class="calificacion" id="calificacion{{ $actividad->id }}">
                                            <!-- Cinco estrellas para calificar -->
                                            <div class="rating">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <input type="radio" id="star{{ $i }}-{{ $actividad->id }}" name="calificacion" value="{{ $i }}" />
                                                    <label for="star{{ $i }}-{{ $actividad->id }}" class="fa fa-star"></label>
                                                @endfor
                                            </div>
                                        </div>
                                        <button type="submit" class="btn boton-primario mt-3">Guardar y Finalizar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p>No se ha finalizado ninguna actividad aún.</p>
                @endif
            </div>
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
            <div class="contenedor-actividades">
                @if ($actividadesFalse->isNotEmpty())
                    @foreach ($actividadesFalse as $index => $actividad)
                    <a class="item-actividad" href="#" disabled>
                        <div class="contenedor-actividad">
                            <span>{{ $index + 1 }}. {{ $actividad->nombre }}</span> <span class="flecha">{{$actividad->cantidad}} ></span>
                        </div>
                    </a>
                  
                    @endforeach
                @else
                    <p>No se ha finalizado ninguna actividad aún.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="footer-actividades">
        <form action="{{ route('finalizarTurno') }}" method="POST">
            @csrf
            <span>Observaciones</span>
            <textarea class="form-control is-invalid" id="validationTextarea" name="observaciones" placeholder="Escribe en este espacio las observaciones generales..." ></textarea>
            <div class="porcentaje-actividad">
                <span>Actividades finalizadas <span>{{ $actividadesFalse->count() }}/{{ $actividadesTrue->count() + $actividadesFalse->count() }}</span></span>
            </div>
            <button type="submit" class="btn boton-secundario">Finalizar turno</button>
        </form>
    </div>

@endsection
