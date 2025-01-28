@extends('layouts.mobile')

@section('content')
    <!-- Imagen grande con texto centrado -->
    <div class="large-image-container">
        <img src="{{ asset('assets/images/fondo1.png') }}" alt="Large Image" class="large-image">
        <div class="titulo-super titulo-actividades">CONTROL DE INVENTARIO</div>
    </div>

    <!-- Rectángulo con icono y texto -->

    <ul class="nav nav-pills mb-3 rectangle2" id="pills-tab" role="tablist">
        <!-- Pestaña de Inventario inicial -->
        <li class="nav-item" role="presentation">
            <a class="nav-link active " id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button"
                role="tab" aria-controls="pills-home" aria-selected="true"><img class="icono-actividades"
                    src="{{ asset('assets/icons/lista.svg') }}" alt="Icono" class="rectangle-icon"> Inventario
                inicial</a>
        </li>
        <!-- Pestaña de Inventario salida -->
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="pills-profile-tab " data-toggle="pill" data-target="#pills-profile" type="button"
                role="tab" aria-controls="pills-profile" aria-selected="false"><img class="icono-actividades"
                    src="{{ asset('assets/icons/lista.svg') }}" alt="Icono" class="rectangle-icon"> Inventario salida</a>
        </li>
        <!-- Pestaña de Mantenimientos -->
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="pills-mantenimientos-tab " data-toggle="pill" data-target="#pills-mantenimientos" type="button"
                role="tab" aria-controls="pills-mantenimientos" aria-selected="false"><img class="icono-actividades"
                    src="{{ asset('assets/icons/lista.svg') }}" alt="Icono" class="rectangle-icon"> Mantenimientos</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        {{-- Inventario Inicial --}}
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="contenedor-actividades">
                @if ($sedesInsumos->isNotEmpty())
                    <h3 style="margin-left: 10px;">Insumos</h3>
                    @foreach ($sedesInsumos as $index => $sedesInsumo)
                        <!-- Enlace para abrir el modal de insumo -->
                        <a class="item-actividad" href="#" data-toggle="modal"
                            data-target="#actividadModal{{ $sedesInsumo->id }}">
                            <div class="contenedor-actividad">
                                <span><img src="{{ $sedesInsumo->insumo->imagen }}"
                                        alt="{{ $sedesInsumo->insumo->nombre_elemento }}"
                                        class="imagen-insumo">{{ $sedesInsumo->insumo->nombre_elemento }}</span> <span
                                    class="flecha">></span>
                            </div>
                        </a>
                        <!-- Modal de insumo -->
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
                                        <p>Estado: {{ optional($sedesInsumo->insumo->estados)->nombre }}</p>
                                        <p>Cantidad: {{ $sedesInsumo->cantidad }}</p>
                                        <div class="form-group">
                                            <label for="novedades{{ $sedesInsumo->id }}">Novedades</label>
                                            <select class="form-control" id="novedades{{ $sedesInsumo->id }}">
                                                <!-- Opciones se llenarán dinámicamente -->
                                            </select>{{-- onchange="toggleButton(this, {{ $sedesInsumo->id }})" --}}
                                        </div>
                                        <div class="form-group">
                                            <label for="observaciones{{ $sedesInsumo->id }}">Observaciones</label>
                                            <textarea class="form-control" id="observaciones{{ $sedesInsumo->id }}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="guardarBtnInicial" onclick="actualizarInsumo({{ $sedesInsumo->id }})">Guardar</button>
                                        <button type="button" class="btn btn-danger" id="reportarBtn{{ $sedesInsumo->id }}" style="display: none;">Reportar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if ($sedesActivos->isNotEmpty())
                    <h3 style="margin-left: 10px;">Activos</h3>
                    @foreach ($sedesActivos as $index => $sedesActivo)
                        <!-- Enlace para abrir el modal de activo -->
                        <a class="item-actividad" href="#" data-toggle="modal"
                            data-target="#activoModal{{ $sedesActivo->id }}">
                            <div class="contenedor-actividad">
                                <span><img src="{{ $sedesActivo->activo->imagen }}"
                                        alt="{{ $sedesActivo->activo->nombre_elemento }}"
                                        class="imagen-insumo">
                                        {{ $sedesActivo->activo->nombre_elemento }} - {{ $sedesActivo->activo->serie }}
                                    </span> <span
                                    class="flecha">></span>
                            </div>
                        </a>
                        <!-- Modal de activo -->
                        <div class="modal fade" id="activoModal{{ $sedesActivo->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="activoModalLabel{{ $sedesActivo->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="activoModalLabel{{ $sedesActivo->id }}">
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
                                        <p>Estado: {{ optional($sedesActivo->activo->estados)->nombre }}</p>
                                        <p>Serie: {{ $sedesActivo->activo->serie }}</p>
                                        {{-- <p>Cantidad: {{ $sedesActivo->cantidad }}</p> --}}
                                        <div class="form-group">
                                            <label for="novedades{{ $sedesActivo->id }}">Novedades</label>
                                            <select class="form-control" id="novedades{{ $sedesActivo->id }}"
                                                onchange="toggleButton(this, {{ $sedesActivo->id }})">
                                                <!-- Opciones se llenarán dinámicamente -->
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="observaciones{{ $sedesActivo->id }}">Observaciones</label>
                                            <textarea class="form-control" id="observaciones{{ $sedesActivo->id }}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="guardarBtn{{ $sedesActivo->id }}" onclick="actualizarActivo({{ $sedesActivo->id }})">Guardar</button>
                                        <button type="button" class="btn btn-danger" id="reportarBtn{{ $sedesActivo->id }}" style="display: none;" onclick="reportarActivo({{ $sedesActivo->id }})">Reportar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        {{-- Inv Salida --}}
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
            <div class="contenedor-actividades">
                @if ($insumos->isNotEmpty())
                    <h3 style="margin-left: 10px;">Insumos</h3>
                    @foreach ($insumos as $index => $insumo)
                        <!-- Enlace para abrir el modal de insumo -->
                        <a class="item-actividad" href="#" data-toggle="modal"
                            data-target="#salidaModalInsumo{{ $insumo->id }}">
                            <div class="contenedor-actividad">
                                <span><img src="{{ $insumo->imagen }}"
                                        alt="{{ $insumo->nombre_elemento }}"
                                        class="imagen-insumo">{{ $insumo->nombre_elemento }}</span> <span
                                    class="flecha">></span>
                            </div>
                        </a>
                        <!-- Modal de insumo -->
                        <div class="modal fade" id="salidaModalInsumo{{ $insumo->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="actividadModalLabel{{ $insumo->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="actividadModalLabel{{ $insumo->id }}">
                                            {{ $insumo->nombre_elemento }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="contenedor-imagen-modal">
                                            <img src="{{ $insumo->imagen }}"
                                                alt="{{ $insumo->nombre_elemento }}" class="img-fluid">
                                        </div>
                                        <p>Estado: {{ optional($insumo->estados)->nombre }}</p>
                                        <div class="form-group">
                                            <label for="cantidadIns{{ $insumo->id }}">Cantidad</label>
                                            <input type="number" class="form-control" id="cantidadIns{{ $insumo->id }}" min="0">
                                        </div>
                                        <div class="form-group">
                                            <label for="observacionesSalidaIns{{ $insumo->id }}">Observaciones</label>
                                            <textarea class="form-control" id="observacionesSalidaIns{{ $insumo->id }}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="solicitarBtn{{ $insumo->id }}"
                                            onclick="solicitarInsumo({{ $insumo->id }}, {{ $sedeId }})">
                                            Solicitar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if ($activos->isNotEmpty())
                    <h3 style="margin-left: 10px;">Activos</h3>
                    @foreach ($activos as $index => $activo)
                        <!-- Enlace para abrir el modal de activo -->
                        <a class="item-actividad" href="#" data-toggle="modal"
                            data-target="#salidaModalActivo{{ $activo->id }}">
                            <div class="contenedor-actividad">
                                <span><img src="{{ $activo->imagen }}"
                                        alt="{{ $activo->nombre_elemento }}"
                                        class="imagen-insumo">{{ $activo->nombre_elemento }}</span> <span
                                    class="flecha">></span>
                            </div>
                        </a>
                        <!-- Modal de activo -->
                        <div class="modal fade" id="salidaModalActivo{{ $activo->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="actividadModalLabelAct{{ $activo->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="actividadModalLabelAct{{ $activo->id }}">
                                            {{ $activo->nombre_elemento }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="contenedor-imagen-modal">
                                            <img src="{{ $activo->imagen }}"
                                                alt="{{ $activo->nombre_elemento }}" class="img-fluid">
                                        </div>
                                        <p>Estado: {{ optional($activo->estados)->nombre }}</p>
                                        <div class="form-group">
                                            <label for="cantidadAct{{ $activo->id }}">Cantidad</label>
                                            <input type="number" class="form-control" id="cantidadAct{{ $activo->id }}" min="0">
                                        </div>
                                        <div class="form-group">
                                            <label for="observacionesSalidaAct{{ $activo->id }}">Observaciones</label>
                                            <textarea class="form-control" id="observacionesSalidaAct{{ $activo->id }}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="solicitarBtn{{ $activo->id }}"
                                            onclick="solicitarActivo({{ $activo->id }}, {{ $sedeId }})">Solicitar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        {{-- Mantenimientos --}}
        <div class="tab-pane fade" id="pills-mantenimientos" role="tabpanel" aria-labelledby="pills-mantenimientos-tab">
            <div class="contenedor-actividades">
                @if ($mantenimientos->isNotEmpty())
                    @foreach ($mantenimientos as $mantenimiento)
                        <!-- Item de mantenimiento de activo -->
                        <div class="item-actividad" href="#">
                            <div class="contenedor-actividad">
                                <span><img src="{{ $mantenimiento->sedeActivo->activo->imagen }}"
                                        alt="{{ $mantenimiento->sedeActivo->activo->nombre_elemento }}"
                                        class="imagen-insumo">{{ $mantenimiento->sedeActivo->activo->nombre_elemento }}</span>
                                <div class="iconos">
                                    <span class="icono" data-toggle="modal" data-target="#mantenimientoModal{{ $mantenimiento->sedeActivo->id }}">
                                        <img src="{{ asset('assets/icons/editar.png') }}" alt="Editar"
                                        style="width: 28px; height: 28px;">
                                    </span>
                                    <span class="icono">
                                        <img src="{{ asset('assets/icons/finalizar.png') }}" alt="Finalizar"
                                        style="width: 28px; height: 28px; margin-left: 15px;">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Modal de mantenimiento de activo -->
                        <div class="modal fade" id="mantenimientoModal{{ $mantenimiento->sedeActivo->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="actividadModalLabel{{ $mantenimiento->sedeActivo->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="actividadModalLabel{{ $mantenimiento->sedeActivo->id }}">
                                            {{ $mantenimiento->sedeActivo->activo->nombre_elemento }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="contenedor-imagen-modal">
                                            <img src="{{ $mantenimiento->sedeActivo->activo->imagen }}"
                                                alt="{{ $mantenimiento->sedeActivo->activo->nombre_elemento }}" class="img-fluid">
                                        </div>
                                        <p>Tipo: {{ optional($mantenimiento->sedeActivo->activo->estados)->nombre }}</p>
                                        <p>Cantidad: {{ $mantenimiento->sedeActivo->cantidad }}</p>
                                        <div class="form-group">
                                            <label for="novedades{{ $mantenimiento->sedeActivo->id }}">Novedades</label>
                                            <select class="form-control" id="novedades{{ $mantenimiento->sedeActivo->id }}"
                                                onchange="toggleButton(this, {{ $mantenimiento->sedeActivo->id }})">
                                                <!-- Opciones se llenarán dinámicamente -->
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="observaciones{{ $mantenimiento->sedeActivo->id }}">Observaciones</label>
                                            <textarea class="form-control" id="observaciones{{ $mantenimiento->sedeActivo->id }}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="guardarBtn{{ $mantenimiento->sedeActivo->id }}">Guardar</button>
                                        <button type="button" class="btn btn-danger" id="reportarBtn{{ $mantenimiento->sedeActivo->id }}" style="display: none;" onclick="reportarActivo({{ $mantenimiento->sedeActivo->id }})">Reportar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function toggleButton(selectElement, id) {
            //console.log(`Valor del select: ${selectElement.value}`);
            const guardarBtn = document.getElementById(`guardarBtn${id}`);
            const reportarBtn = document.getElementById(`reportarBtn${id}`);
            if (selectElement.value == '3') {
                guardarBtn.style.display = 'none';
                reportarBtn.style.display = 'block';
            } else {
                guardarBtn.style.display = 'block';
                reportarBtn.style.display = 'none';
            }
        }

        // Función para actualizar el insumo
        function actualizarInsumo(id) {
            const estadoId = document.getElementById(`novedades${id}`).value;
            const observacion = document.getElementById(`observaciones${id}`).value;

            fetch(`{{ route('actualizar.insumo') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: id,
                    estado_id: estadoId,
                    observacion: observacion
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                // Cerrar el modal después de guardar
                $(`#actividadModal${id}`).modal('hide');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el insumo: ' + error.message);
            });
        }

        // Función para actualizar el activo
        function actualizarActivo(id) {
            const estadoId = document.getElementById(`novedades${id}`).value;
            const observacion = document.getElementById(`observaciones${id}`).value;

            fetch(`{{ route('actualizar.activo') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: id,
                    estado_id: estadoId,
                    observacion: observacion
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                // Cerrar el modal después de guardar
                $(`#activoModal${id}`).modal('hide');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el activo: ' + error.message);
            });
        }

        // Función para reportar el activo
        function reportarActivo(id) {
            const estadoId = document.getElementById(`novedades${id}`).value;
            const observacion = document.getElementById(`observaciones${id}`).value;

            fetch(`{{ route('reportar.activo') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: id,
                    estado_id: estadoId,
                    observacion: observacion
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                // Cerrar el modal después de reportar
                $(`#activoModal${id}`).modal('hide');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al reportar el activo: ' + error.message);
            });
        }

        // Función para solicitar un insumo
        function solicitarInsumo(id, sedeId) {
            //const nombre = document.getElementById(`actividadModalLabel${id}`).innerText;
            const cantidad = document.getElementById(`cantidadIns${id}`).value;
            const observaciones = document.getElementById(`observacionesSalidaIns${id}`).value;

            fetch(`{{ route('solicitar.insumo.activo') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: id,
                    cantidad: cantidad,
                    observaciones: observaciones,
                    tipo: 'insumo',
                    sedeId: sedeId
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                // Cerrar el modal después de solicitar
                $(`#salidaModal${id}`).modal('hide');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al solicitar el insumo: ' + error.message);
            });
        }

        // Función para solicitar un activo
        function solicitarActivo(id, sedeId) {
            //const nombre = document.getElementById(`actividadModalLabelAct${id}`).innerText;
            const cantidad = document.getElementById(`cantidadAct${id}`).value;
            const observaciones = document.getElementById(`observacionesSalidaAct${id}`).value;

            fetch(`{{ route('solicitar.insumo.activo') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: id,
                    cantidad: cantidad,
                    observaciones: observaciones,
                    tipo: 'activo',
                    sedeId: sedeId
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                // Cerrar el modal después de solicitar
                $(`#salidaModal${id}`).modal('hide');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al solicitar el activo: ' + error.message);
            });
        }

        // Reiniciar valores del select y campo de observación al cerrar el modal
        $('.modal').on('hidden.bs.modal', function () {
            $(this).find('select').val('').trigger('change');
            $(this).find('textarea').val('');
        });

        // Reiniciar valores del select y campo de observación al abrir el modal
        $('.modal').on('show.bs.modal', function () {
            const modal = $(this);
            const id = modal.attr('id').replace('actividadModal', '').replace('salidaModal', '').replace('mantenimientoModal', '').replace('activoModal', '');
            const select = modal.find('select');
            const textarea = modal.find('textarea');
            const cantidadInput = modal.find('input[type="number"]');

            // Llenar select de novedades dinámicamente
            fetch(`{{ route('obtener.estados') }}`)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(text) });
                    }
                    return response.json();
                })
                .then(estados => {
                    //console.log(estados);
                    select.html('<option value="">Seleccione</option>');
                    estados.forEach(estado => {
                        const option = document.createElement("option");
                        option.value = estado.id;
                        option.textContent = estado.nombre;
                        select.append(option);
                    });

                    // Obtener el estado_id del insumo o activo y establecerlo como valor por defecto
                    const isSalidaModal = modal.attr('id').includes('salidaModal');
                    const isActivoModal = modal.attr('id').includes('activoModal');
                    const fetchUrl = isActivoModal ? `{{ route('obtener.activo', '') }}/${id}` : `{{ route('obtener.insumo', '') }}/${id}`;
                    if (isSalidaModal) {
                        cantidadInput.val('');
                        textarea.val('')
                    } else {
                        fetch(fetchUrl)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => { throw new Error(text) });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (isActivoModal) {
                                select.val(data.activo.estado_id).trigger('change');
                                textarea.val(data.activo.observacion);
                            } else {
                                select.val(data.insumo.estado_id).trigger('change');
                                textarea.val(data.insumo.observacion);
                            }
                        })
                        .catch(error => {
                            console.error("Error al cargar el insumo o activo:", error);
                            alert("Error al cargar el insumo o activo: " + error.message);
                        });
                    }

                })
                .catch(error => {
                    console.error("Error al cargar los estados:", error);
                    alert("Error al cargar los estados: " + error.message);
                });

            // Reiniciar valores del select y campo de observación
            //select.val('').trigger('change');

            // Reiniciar valor del input de cantidad y textarea de observaciones
        });

    </script>
@endpush
