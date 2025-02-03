@extends('layouts.mobile')

@section('content')
    <!-- Imagen grande con texto centrado -->
    <div class="large-image-container">
        <img src="{{ asset('assets/images/fondo1.png') }}" alt="Large Image" class="large-image">
        <div class="titulo-super">¡BIENVENIDO AL SISTEMA DE INFORMACIÓN INDUASEO!</div>
    </div>

    <!-- Input de búsqueda -->
    <div class="search-container">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar información...">
    </div>

    <!-- Contenedor de resultados de búsqueda -->
    <div class="results-container" id="resultsContainer">
        <!-- Los resultados de búsqueda se cargarán aquí mediante JavaScript -->
    </div>

    <!-- Contenedor de novedades -->
    <span class="titulo-novedades">Novedades</span>
    <div class="novedades-slider" id="novedades-slider">
        <!-- Las novedades se cargarán aquí mediante JavaScript -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const resultsContainer = document.getElementById('resultsContainer');

            searchInput.addEventListener('input', function() {
                const query = searchInput.value.trim();
                if (query.length > 0) {
                    fetch(`{{ route('informacion.novedades.buscar') }}?buscar=${query}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            data.forEach(item => {
                                const card = document.createElement('div');
                                card.classList.add('result-card');

                                const title = document.createElement('h6');
                                title.classList.add('result-title');
                                title.textContent = item.titulo;

                                const description = document.createElement('p');
                                description.classList.add('result-description');
                                description.textContent = item.descripcion;

                                card.appendChild(title);
                                card.appendChild(description);
                                resultsContainer.appendChild(card);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    resultsContainer.innerHTML = '';
                }
            });

            // Fetch and display all novedades
            fetch(`{{ url('/novedades') }}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(novedades => {
                    if (Array.isArray(novedades)) {
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
                            icon.alt = 'Novedad';
                            icon.classList.add('novedad-icon');

                            info.appendChild(title);
                            info.appendChild(date);
                            info.appendChild(text);

                            card.appendChild(info);
                            card.appendChild(icon);
                            slider.appendChild(card);
                        });
                    } else {
                        console.error('Error: La respuesta no es un array.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    <style>
        .search-container {
            padding: 10px;
            text-align: center;
        }

        .results-container {
            padding: 10px;
        }

        .result-card {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .result-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .result-description {
            font-size: 0.875rem;
        }

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
