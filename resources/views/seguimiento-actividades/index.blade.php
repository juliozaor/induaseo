@extends('layouts.mobile')

@section('content')
    <!-- Imagen grande con texto centrado -->
    <div class="large-image-container">
        <img src="{{ asset('assets/images/fondo1.png') }}" alt="Large Image" class="large-image">
        <div class="titulo-super">¡BIENVENIDO AL SISTEMA DE SUPERVISIÓN DE ACTIVIDADES INDUASEO!</div>
    </div>

    <!-- Rectángulo con icono y texto -->
    <div class="rectangle">
        <img src="{{ asset('assets/icons/turnoasignado.svg') }}" alt="Icono" class="rectangle-icon">
        <span class="rectangle-text">Turnos asignados</span>
    </div>

    @foreach($turnos as $turno)
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Turno de supervisión</h5>
            <div class="card-info">
                <img src="{{ asset('assets/icons/reloj.svg') }}" alt="Supervisión" class="card-icon">
                <span>13:00 - 18:00</span>
                <img src="{{ asset('assets/icons/calendar.svg') }}" alt="Fecha" class="card-icon">
                <span>{{$turno->fecha_inicio}} al {{$turno->fecha_fin}}</span>
            </div>
            <h5 class="card-title">Sede</h5>
            <div class="card-info">
                <img src="{{ asset('assets/icons/location.svg') }}" alt="Ubicación" class="card-icon">
                <span>{{$turno->sede->nombre}} <br> {{$turno->sede->direccion}}</span>
            </div>{{ $turno->turno_id }}
            <a href="{{ route('actividades.turno', ['id' => $turno->turno->id, 'sede_id' => $turno->sede->id]) }}" class="btn boton-secundario" onclick="setTurnoData({{ $turno->turno->id }}, {{ $turno->sede->id }})">Iniciar turno</a>
        </div>
    </div>
    @endforeach

    <span class="titulo-novedades">Novedades</span>
    <div class="novedades-slider" id="novedades-slider">
        <!-- Las novedades se cargarán aquí mediante JavaScript -->
    </div>

    <!-- Modal -->
    <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Archivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="fileFrame" src="" width="100%" height="500px"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sedeId = "{{ $turnos->first()->sede->id ?? '' }}"; // Obtener el primer sedeId disponible
            fetch(`{{ url('/novedades') }}/${sedeId}`)
                .then(response => response.json())
                .then(novedades => {
                    const slider = document.getElementById('novedades-slider');
                    novedades.forEach(novedad => {
                        const card = document.createElement('div');
                        card.classList.add('novedad-card');
                        card.style.cursor = 'pointer';
                        card.onclick = function() {
                            const fileFrame = document.getElementById('fileFrame');
                            fileFrame.src = `{{ asset('storage') }}/${novedad.url}`;
                            const fileModalLabel = document.getElementById('fileModalLabel');
                            fileModalLabel.textContent = novedad.url.split('/').pop(); // Mostrar el nombre del archivo
                            const fileModal = new bootstrap.Modal(document.getElementById('fileModal'));
                            fileModal.show();
                        };

                        const info = document.createElement('div');
                        info.classList.add('novedad-info');

                        const title = document.createElement('h6');
                        title.classList.add('novedad-title');
                        title.textContent = novedad.titulo;

                        const date = document.createElement('span');
                        date.classList.add('novedad-date');
                        date.textContent = new Date(novedad.fecha).toLocaleDateString();

                        const text = document.createElement('p');
                        text.classList.add('novedad-text');
                        text.textContent = novedad.descripcion;

                        const icon = document.createElement('img');
                        if (novedad.tipo_multimedia && novedad.tipo_multimedia.nombre) {
                            switch (novedad.tipo_multimedia.nombre) {
                                case 'Documento':
                                    icon.src = `{{ asset('assets/icons/pdf-icon.png') }}`;
                                    break;
                                case 'Video':
                                    icon.src = `{{ asset('assets/icons/video-icon.png') }}`;
                                    break;
                                case 'Imagen':
                                    icon.src = `{{ asset('assets/icons/image-icon.png') }}`;
                                    break;
                                default:
                                    icon.src = `{{ asset('assets/icons/default-icon.png') }}`;
                            }
                        } else {
                            icon.src = `{{ asset('assets/icons/default-icon.png') }}`;
                        }
                        icon.alt = 'Novedad';
                        icon.classList.add('novedad-icon');

                        info.appendChild(title);
                        info.appendChild(date);
                        info.appendChild(text);

                        card.appendChild(info);
                        card.appendChild(icon);
                        slider.appendChild(card);
                    });
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    <style>
        .novedad-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            max-width: 200px; /* Ajustar el ancho máximo de la tarjeta */
            margin: 0 auto; /* Centrar la tarjeta horizontalmente */
        }

        .novedad-info {
            flex: 1;
        }

        .novedad-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .novedad-icon {
            width: 40px;
            height: 40px;
            margin-left: 10px;
        }
    </style>
@endsection
