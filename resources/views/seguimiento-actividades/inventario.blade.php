@extends('layouts.mobile')

@section('content')
    <!-- Imagen grande con texto centrado -->
    <div class="large-image-container">
        <img src="{{ asset('assets/images/fondo1.png') }}" alt="Large Image" class="large-image">
        <div class="titulo-super titulo-actividades text-center">CONTROL DE INVENTARIO</div>
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
            <a class="nav-link" id="pills-mantenimientos-tab " data-toggle="pill" data-target="#pills-mantenimientos"
                type="button" role="tab" aria-controls="pills-mantenimientos" aria-selected="false"
                onclick="obtenerMantenimientos()">
                <img class="icono-actividades" src="{{ asset('assets/icons/lista.svg') }}"
                    alt="Icono"class="rectangle-icon">
                Mantenimientos
            </a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        {{-- Inventario Inicial --}}
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="contenedor-actividades">
                @if ($sedesInsumos->isNotEmpty())
                    <h3 style="margin-left: 10px;">Insumos</h3>
                    @foreach ($sedesInsumos as $sedesInsumo)
                        @foreach ($sedesInsumo['insumos'] as $insumo)
                            <!-- Enlace para abrir el modal de insumo -->
                            <a class="item-actividad" href="#" data-toggle="modal"
                                data-target="#actividadModal{{ $sedesInsumo['id'] }}">

                                <div class="contenedor-actividad">
                                    <span><img src="{{ $insumo->imagen }}" alt="{{ $insumo->nombre_elemento }}"
                                            class="imagen-insumo">{{ $insumo->nombre_elemento }}</span> <span
                                        class="flecha">></span>
                                </div>

                            </a>
                            <!-- Modal de insumo -->
                            <div class="modal fade" id="actividadModal{{ $sedesInsumo['id'] }}" tabindex="-1"
                                role="dialog" aria-labelledby="actividadModalLabel{{ $sedesInsumo['id'] }}"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="actividadModalLabel{{ $sedesInsumo['id'] }}">
                                                {{ $insumo->nombre_elemento }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="contenedor-imagen-modal">
                                                <img src="{{ $insumo->imagen }}" alt="{{ $insumo->nombre_elemento }}"
                                                    class="img-fluid">
                                            </div>
                                            {{-- <p>Estado: {{ optional($insumo->estados)->nombre }}</p> --}}
                                            <h4>Cantidad: {{ $sedesInsumo['cantidad'] }}</h4>
                                            {{-- <div class="form-group">
                                                <label for="novedades{{ $sedesInsumo['id'] }}">Novedades</label>
                                                <select class="form-control" id="novedades{{ $sedesInsumo['id'] }}"></select>
                                            </div>
                                            <div class="form-group">
                                                <label for="observaciones{{ $sedesInsumo['id'] }}">Observaciones</label>
                                                <textarea class="form-control" id="observaciones{{ $sedesInsumo['id'] }}" rows="3"></textarea>
                                            </div> --}}
                                        </div>
                                        {{-- <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" id="guardarBtnInicial"
                                                onclick="actualizarInsumo({{ $sedesInsumo['id'] }})">Guardar</button>
                                            <button type="button" class="btn btn-danger"
                                                id="reportarBtn{{ $sedesInsumo['id'] }}"
                                                style="display: none;">Reportar</button>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endif

                @if ($sedesActivos->isNotEmpty())
                    <h3 style="margin-left: 10px;">Activos</h3>
                    @foreach ($sedesActivos as $sedesActivo)
                        <!-- Enlace para abrir el modal de activo -->
                        <a class="item-actividad" href="#" data-toggle="modal"
                            data-target="#activoModal{{ $sedesActivo['id'] }}">
                            <div class="contenedor-actividad">
                                <span><img src="{{ $sedesActivo['imagen'] }}" alt="{{ $sedesActivo['nombre'] }}"
                                        class="imagen-insumo">
                                    {{ $sedesActivo['nombre'] }} - {{ $sedesActivo['serie'] }}
                                </span> <span class="flecha">></span>
                            </div>
                        </a>
                        <!-- Modal de activo -->
                        <div class="modal fade" id="activoModal{{ $sedesActivo['id'] }}" tabindex="-1" role="dialog"
                            aria-labelledby="activoModalLabel{{ $sedesActivo['id'] }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="activoModalLabel{{ $sedesActivo['id'] }}">
                                            {{ $sedesActivo['nombre'] }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="contenedor-imagen-modal">
                                            <img src="{{ $sedesActivo['imagen'] }}" alt="{{ $sedesActivo['nombre'] }}"
                                                class="img-fluid">
                                        </div>
                                        <p>Estado: {{ $sedesActivo['estado'] }}</p>
                                        <p>Serie: {{ $sedesActivo['serie'] }}</p>
                                        {{-- <p>Cantidad: {{ $sedesActivo->cantidad }}</p> --}}
                                        <div class="form-group">
                                            <label for="novedades{{ $sedesActivo['id'] }}">Novedades</label>
                                            <select class="form-control" id="novedades{{ $sedesActivo['id'] }}"
                                                onchange="toggleButton(this, {{ $sedesActivo['id'] }})">
                                                <!-- Opciones se llenarán dinámicamente -->
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="observaciones{{ $sedesActivo['id'] }}">Observaciones</label>
                                            <textarea class="form-control" id="observaciones{{ $sedesActivo['id'] }}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary"
                                            id="guardarBtn{{ $sedesActivo['id'] }}"
                                            onclick="actualizarActivo({{ $sedesActivo['id'] }})">Guardar</button>
                                        <button type="button" class="btn btn-danger"
                                            id="reportarBtn{{ $sedesActivo['id'] }}" style="display: none;"
                                            onclick="reportarActivo({{ $sedesActivo['id'] }})" disabled="true">
                                            Reportar
                                        </button>
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
            <div class="d-flex justify-content-end align-items-center" style="margin-right: 10px;">
                <button type="button" class="btn btn-primary" id="solicitarBtn"
                    onclick="solicitarItems({{ $sedeId }})">
                    Solicitar
                </button>
            </div>
            <div class="contenedor-actividades">
                @if ($insumos->isNotEmpty())
                    <h3 style="margin-left: 10px;">Insumos</h3>
                    @foreach ($insumos as $index => $insumo)
                        <!-- Insumos -->
                        <div class="item-actividad">
                            <div class="contenedor-actividad d-flex justify-content-between align-items-center">
                                <span>
                                    {{-- <img src="{{ $insumo->imagen }}" alt="{{ $insumo->nombre_elemento }}"
                                        class="imagen-insumo"> --}}
                                    {{ $insumo->nombre_elemento }}
                                </span>
                                <div class="d-flex align-items-center">
                                    <input type="number" id="CanInsumoSal{{ $insumo->id }}" class="form-control"
                                        placeholder="Cantidad" min="0"
                                        style="width: 150px; margin-left: 10px; margin-right: 10px;">
                                    <input type="checkbox" id="CheckInsumoSal{{ $insumo->id }}"
                                        style="margin-left: 10px;">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if ($activos->isNotEmpty())
                    <h3 style="margin-left: 10px;">Activos</h3>
                    @foreach ($activos as $index => $activo)
                        <!-- Activos -->
                        <div class="item-actividad">
                            <div class="contenedor-actividad d-flex justify-content-between align-items-center">
                                <span>
                                    {{-- <img src="{{ $activo->imagen }}" alt="{{ $activo->nombre_elemento }}"
                                        class="imagen-insumo"> --}}
                                    {{ $activo->nombre_elemento }}
                                </span>
                                <div class="d-flex align-items-center">
                                    <input type="number" id="CanActivoSal{{ $activo->id }}" class="form-control"
                                        placeholder="Cantidad" min="0"
                                        style="width: 150px; margin-left: 10px; margin-right: 10px;">
                                    <input type="checkbox" id="CheckActivoSal{{ $activo->id }}"
                                        style="margin-left: 10px;">
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
                <div id="mantenimientos">

                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //cargarInventario();
            setMinDate();
        });

        function cargarInventario() {
            fetch(`inventario-turno`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    /* console.log(data); */
                })
                .catch(error => {
                    /* // console.error('Error:', error); */
                    alert('Error al cargar el inventario: ' + error.message);
                });
        }

        function toggleButton(selectElement, id) {
            //console.log(`Valor del select: ${selectElement.value}`);
            const guardarBtn = document.getElementById(`guardarBtn${id}`);
            const reportarBtn = document.getElementById(`reportarBtn${id}`);
            if (selectElement.value == '2') {
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
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        /* text: data.message, */
                    });
                    /* alert(data.message); */
                    // Cerrar el modal después de guardar
                    $(`#actividadModal${id}`).modal('hide');
                })
                .catch(error => {
                    /* // console.error('Error:', error); */
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
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        /* text: data.message, */
                    });
                    /* alert(data.message); */
                    // Cerrar el modal después de guardar
                    $(`#activoModal${id}`).modal('hide');
                })
                .catch(error => {
                    /* // console.error('Error:', error); */
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
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        /* text: data.message, */
                    });
                    /* alert(data.message); */
                    // Cerrar el modal después de reportar
                    /* $(`#activoModal${id}`).modal('hide');
                    $('.modal-backdrop').remove();
                    document.body.classList.remove('modal-open'); */
                })
                .catch(error => {
                    /* // console.error('Error:', error); */
                    alert('Error al reportar el activo: ' + error.message);
                });
        }

        // Reiniciar valores del select y campo de observación al cerrar el modal
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('select').val('').trigger('change');
            $(this).find('textarea').val('');
        });

        // Reiniciar valores del select y campo de observación al abrir el modal
        $('.modal').on('show.bs.modal', function() {
            const modal = $(this);
            const id = modal.attr('id').replace('actividadModal', '').replace('activoModal', '');
            const select = modal.find('select');
            const textarea = modal.find('textarea');
            const cantidadInput = modal.find('input[type="number"]');

            // Llenar select de novedades dinámicamente
            fetch(`{{ route('obtener.estados') }}`)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text)
                        });
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
                    const isFinalModal = modal.attr('id').includes('finalModal');
                    const isMantenimientoModal = modal.attr('id').includes('mantenimientoModal');
                    const isActivoModal = modal.attr('id').includes('activoModal');
                    //const fetchUrl = isActivoModal ? `{{ route('obtener.activo', '') }}/${id}` : `{{ route('obtener.insumo', '') }}/${id}`;
                    let fetchUrl = '';
                    if (isActivoModal) {
                        fetchUrl = `{{ route('obtener.activo', '') }}/${id}`;
                    } else if (!isMantenimientoModal && !isActivoModal && !isFinalModal) {
                        fetchUrl = `{{ route('obtener.insumo', '') }}/${id}`;
                    }
                    if (!isMantenimientoModal && !isFinalModal) {
                        fetch(fetchUrl)
                            .then(response => {
                                if (!response.ok) {
                                    return response.text().then(text => {
                                        throw new Error(text)
                                    });
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
                                // console.error("Error al cargar el insumo o activo:", error);
                                alert("Error al cargar el insumo o activo: " + error.message);
                            });
                    }

                })
                .catch(error => {
                    // console.error("Error al cargar los estados:", error);
                    alert("Error al cargar los estados: " + error.message);
                });

            // Reiniciar valores del select y campo de observación
            //select.val('').trigger('change');

            // Reiniciar valor del input de cantidad y textarea de observaciones
        });

        function solicitarItems(sedeId) {
            const items = [];
            // Collect checked insumos
            @foreach ($insumos as $insumo)
                {
                    const checkInsumo{{ $insumo->id }} = document.getElementById('CheckInsumoSal{{ $insumo->id }}');
                    if (checkInsumo{{ $insumo->id }}.checked) {
                        const cantidadInsumo{{ $insumo->id }} = document.getElementById(
                            'CanInsumoSal{{ $insumo->id }}').value;
                        items.push({
                            id: {{ $insumo->id }},
                            nombre: '{{ $insumo->nombre_elemento }}',
                            cantidad: cantidadInsumo{{ $insumo->id }},
                            tipo: 'insumo'
                        });
                    }
                }
            @endforeach

            // Collect checked activos
            @foreach ($activos as $activo)
                {
                    const checkActivo{{ $activo->id }} = document.getElementById('CheckActivoSal{{ $activo->id }}');
                    if (checkActivo{{ $activo->id }}.checked) {
                        const cantidadActivo{{ $activo->id }} = document.getElementById(
                            'CanActivoSal{{ $activo->id }}').value;
                        items.push({
                            id: {{ $activo->id }},
                            nombre: '{{ $activo->nombre_elemento }}',
                            cantidad: cantidadActivo{{ $activo->id }},
                            tipo: 'activo'
                        });
                    }
                }
            @endforeach

            // Enviar los items al servidor
            fetch(`{{ route('enviar.solicitud.items') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: items,
                        sedeId: {{ $sedeId }}
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        /* text: data.message, */
                    });
                    /* alert(data.message); */
                })
                .catch(error => {
                    // console.error('Error:', error);
                    alert('Error al enviar la solicitud de inventario: ' + error.message);
                });
        }

        // Función para verificar si el activo está reportado
        function verificarActivoReportado(id) {
            fetch(`{{ url('/activo-reportado') }}/${id}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    const reportarBtn = document.getElementById(`reportarBtn${id}`);
                    if (data.reportado) {
                        reportarBtn.disabled = true;
                    } else {
                        reportarBtn.disabled = false;
                    }
                })
                .catch(error => {
                    // console.error('Error:', error);
                });
        }

        function obtenerMantenimientos() {
            const sedeId = {{ $sedeId }};
            fetch(`{{ route('obtener.mantenimientos') }}?sede_id=${sedeId}`)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    //console.log(data);
                    /* <img src="${mantenimiento.sede_activo.activo.imagen}"
                                        alt="${mantenimiento.sede_activo.activo.nombre_elemento}"
                                        class="imagen-insumo">
                                        <img src="${mantenimiento.sede_activo.activo.imagen}"
                                                alt="${mantenimiento.sede_activo.activo.nombre_elemento}"
                                                class="img-fluid">
                                                <img src="${mantenimiento.sede_activo.activo.imagen}"
                                                alt="${mantenimiento.sede_activo.activo.nombre_elemento}"
                                                class="img-fluid">*/
                    const mantenimientos = data;
                    const mantenimientosDiv = document.getElementById('mantenimientos');
                    mantenimientosDiv.innerHTML = '';
                    mantenimientos.forEach(mantenimiento => {
                        const mantenimientoDiv = document.createElement('div');
                        mantenimientoDiv.classList.add('item-actividad');
                        mantenimientoDiv.innerHTML = `
                            <div class="item-actividad">
                            <div class="contenedor-actividad">
                                <span>

                                    ${mantenimiento.sede_activo.activo.nombre_elemento}
                                </span>
                                <div class="iconos">
                                    <span class="icono" data-toggle="modal" id="editarBtn${mantenimiento.id}"
                                        data-target="#mantenimientoModal${mantenimiento.id}"
                                        onclick="llenarDatosMantenimiento(${mantenimiento.id})">
                                        <img src="{{ asset('assets/icons/editar.png') }}" alt="Editar"
                                            style="width: 28px; height: 28px;">
                                    </span>
                                    <span class="icono" data-toggle="modal" id="finalizarBtn${mantenimiento.id}"
                                        data-target="#finalModal${mantenimiento.id}"
                                        onclick="cargarEstados(${mantenimiento.id})">
                                        <img src="{{ asset('assets/icons/finalizar.png') }}" alt="Finalizar"
                                            style="width: 28px; height: 28px; margin-left: 15px;">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Modal de mantenimiento de activo -->
                        <div class="modal fade" id="mantenimientoModal${mantenimiento.id}" tabindex="-1"
                            role="dialog" aria-labelledby="mantenimientoModalLabel${mantenimiento.id}"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="mantenimientoModalLabel${mantenimiento.id}">
                                            ${mantenimiento.sede_activo.activo.nombre_elemento}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="contenedor-imagen-modal">

                                        </div>
                                        <p>Estado: ${mantenimiento.sede_activo.estados ? mantenimiento.sede_activo.estados.nombre : ''}</p>
                                        <div class="form-group">
                                            <label for="fecha${mantenimiento.id}">Fecha mantenimiento</label>
                                            <input type="date" class="form-control"
                                                id="fecha${mantenimiento.id}">
                                        </div>
                                        <div class="form-group">
                                            <label for="observacionesMant${mantenimiento.id}">Observaciones</label>
                                            <textarea class="form-control" id="observacionesMant${mantenimiento.id}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary"
                                            id="programarBtn${mantenimiento.id}"
                                            onclick="actualizarMantenimiento(${mantenimiento.id})">
                                            Programar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal de finalización de mantenimiento -->
                        <div class="modal fade" id="finalModal${mantenimiento.id}" tabindex="-1" role="dialog"
                            aria-labelledby="finalModalLabel${mantenimiento.id}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="finalModalLabel${mantenimiento.id}">
                                            ${mantenimiento.sede_activo.activo.nombre_elemento}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="contenedor-imagen-modal">

                                        </div>
                                        <div class="form-group">
                                            <label for="novedades${mantenimiento.id}">Estado</label>
                                            <select class="form-control" id="novedadesF${mantenimiento.id}">
                                                <!-- Opciones se llenarán dinámicamente -->
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="observacionesMant${mantenimiento.id}">Observaciones</label>
                                            <textarea class="form-control" id="observacionesMantF${mantenimiento.id}" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary"
                                            id="finalizarBtn${mantenimiento.id}"
                                            onclick="finalizarMantenimiento(${mantenimiento.id})">
                                            Finalizar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                        mantenimientosDiv.appendChild(mantenimientoDiv);
                    });
                })
                .catch(error => {
                    // console.error('Error:', error);
                    alert('Error al cargar los mantenimientos: ' + error.message);
                });
        }

        // Llamar a obtenerMantenimientos cuando se hace clic en la pestaña "Mantenimientos"
        // document.getElementById('pills-mantenimientos-tab').addEventListener('click', obtenerMantenimientos);

        // Llamar a la función verificarActivoReportado al abrir el modal
        $('.modal').on('show.bs.modal', function() {
            const modal = $(this);
            const id = modal.attr('id').replace('activoModal', '');
            /* console.log(`ID: ${id}`); */
            if (modal.attr('id').includes('activoModal')) {
                verificarActivoReportado(id);
            }
        });

        function cargarEstados(id) {
            fetch(`{{ route('obtener.estados') }}`)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(estados => {
                    console.log(estados);
                    const select = document.getElementById('novedadesF' + id);
                    select.innerHTML = '<option value="">Seleccione</option>';
                    estados.forEach(estado => {
                        const option = document.createElement('option');
                        option.value = estado.id;
                        option.textContent = estado.nombre;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    // console.error('Error:', error);
                    alert('Error al cargar los estados: ' + error.message);
                });
        }

        // Llenar el campo de observaciones y fecha con los datos del mantenimiento
        function llenarDatosMantenimiento(id) {
            // console.log(`ID: ${id}`);
            fetch(`{{ url('/obtener-datos-mantenimiento') }}/${id}`)
                .then(response => response.json())
                .then(data => {
                    const observacionesTextarea = document.getElementById(`observacionesMant${id}`);
                    const fechaInput = document.getElementById(`fecha${id}`);
                    observacionesTextarea.value = data.observaciones_reportadas;
                    fechaInput.value = data.mtto_programado;
                })
                .catch(error => {
                    // console.error('Error:', error);
                });
        }

        // Llamar a la función llenarDatosMantenimiento al abrir el modal
        $('.modal').on('show.bs.modal', function() {
            const modal = $(this);
            const id = modal.attr('id').replace('mantenimientoModal', '');
            if (modal.attr('id').includes('mantenimientoModal')) {
                llenarDatosMantenimiento(id);
            }
        });

        // Function to update maintenance details
        function actualizarMantenimiento(id) {
            const fecha = document.getElementById(`fecha${id}`).value;
            const observaciones = document.getElementById(`observacionesMant${id}`).value;
            /* console.log('Está entrando a la función'); */
            fetch(`{{ route('actualizar.mantenimiento.details') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: id,
                        mtto_programado: fecha,
                        observaciones_reportadas: observaciones
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        /*text:  data.message ,*/
                    });
                    /* alert(data.message); */
                    // Close the modal after saving
                    /* $(`#mantenimientoModal${id}`).modal('hide');
                    $('.modal-backdrop').remove();
                    document.body.classList.remove('modal-open'); */
                })
                .catch(error => {
                    // console.error('Error:', error);
                    alert('Error al actualizar el mantenimiento: ' + error.message);
                });
        }

        // Function to set the minimum date for the date input
        function setMinDate() {
            const today = new Date().toISOString().split('T')[0];
            document.querySelectorAll('input[type="date"]').forEach(input => {
                input.setAttribute('min', today);
            });
        }

        // Call setMinDate on page load
        // document.addEventListener('DOMContentLoaded', setMinDate);

        // Function to finalize maintenance
        function finalizarMantenimiento(id) {
            const estadoId = document.getElementById(`novedadesF${id}`).value;
            const observaciones = document.getElementById(`observacionesMantF${id}`).value;

            fetch(`{{ route('finalizar.mantenimiento') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: id,
                        estado_id: estadoId,
                        observaciones: observaciones
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        /* text: data.message, */
                    });
                    /* alert(data.message); */
                    // Close the modal after saving
                    $(`#finalModal${id}`).modal('hide');
                    $('.modal-backdrop').remove();
                    document.body.classList.remove('modal-open');
                    obtenerMantenimientos();
                })
                .catch(error => {
                    // console.error('Error:', error);
                    alert('Error al finalizar el mantenimiento: ' + error.message);
                });
        }
        //obtenerMantenimientos();
    </script>
@endpush
