@extends('layouts.mobile')

@section('content')
    <!-- Imagen grande con texto centrado -->
    <div class="large-image-container">
        <img src="{{ asset('assets/images/fondo1.png') }}" alt="Large Image" class="large-image">
        <div class="titulo-super">¡BIENVENIDO AL SISTEMA DE INFORMACIÓN INDUASEO!</div>
    </div>

    <!-- Input de búsqueda -->
    <div class="search-container rectangle2">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar información...">
    </div>

    <!-- Contenedor de categorías -->
    <div class="categories-container" id="categoriesContainer">
        <!-- Los botones de categorías se cargarán aquí mediante JavaScript -->
    </div>

    <!-- Contenedor de resultados de búsqueda -->
    <div class="results-container" id="resultsContainer">
        <!-- Los resultados de búsqueda se cargarán aquí mediante JavaScript -->
    </div>

    <!-- Contenedor de novedades -->
    <div class="novedades-container-wrapper">
        <div class="novedades-container" id="novedades-container">
            <!-- Las novedades se cargarán aquí mediante JavaScript -->
        </div>
    </div>

    <!-- Modal for displaying files -->
    <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Archivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="fileFrame" src="" width="100%" height="100%"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const resultsContainer = document.getElementById('resultsContainer');
            const categoriesContainer = document.getElementById('categoriesContainer');
            const novedadesContainer = document.getElementById('novedades-container');
            let selectedCategory = null;

            // Add "Todos" category button
            const todosButton = document.createElement('button');
            todosButton.classList.add('category-button', 'selected');
            todosButton.textContent = 'Todos';
            todosButton.onclick = function() {
                selectedCategory = null;
                fetchNovedades();
                document.querySelectorAll('.category-button').forEach(btn => btn.classList.remove('selected'));
                todosButton.classList.add('selected');
            };
            categoriesContainer.appendChild(todosButton);

            // Fetch and display categories
            fetch(`{{ route('categorias.obtener') }}`)
                .then(response => response.json())
                .then(categories => {
                    categories.forEach(category => {
                        const button = document.createElement('button');
                        button.classList.add('category-button');
                        button.textContent = category.nombre;
                        button.onclick = function() {
                            selectedCategory = category.id;
                            fetchNovedades();
                            document.querySelectorAll('.category-button').forEach(btn => btn
                                .classList.remove('selected'));
                            button.classList.add('selected');
                        };
                        categoriesContainer.appendChild(button);
                    });
                })
                .catch(error => console.error('Error:', error));

            searchInput.addEventListener('input', function() {
                fetchNovedades();
            });

            function fetchNovedades() {
                const query = searchInput.value.trim();
                const url = new URL(`{{ route('informacion.novedades.buscar') }}`);
                if (query.length > 0) {
                    url.searchParams.append('buscar', query);
                }
                if (selectedCategory) {
                    url.searchParams.append('categoria', selectedCategory);
                }

                fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        resultsContainer.innerHTML = '';
                        novedadesContainer.innerHTML = '';
                        data.forEach(item => {
                            if (!selectedCategory || item.categoria_id === selectedCategory) {
                                const card = document.createElement('div');
                                card.classList.add('novedad-card');
                                card.style.cursor = 'pointer';
                                card.onclick = function() {
                                    const fileFrame = document.getElementById('fileFrame');
                                    const modalBody = document.querySelector('.modal-body');
                                    fileFrame.src = item.url;
                                    const fileModalLabel = document.getElementById(
                                    'fileModalLabel');
                                    fileModalLabel.textContent = item.url.split('/')
                                .pop(); // Mostrar el nombre del archivo
                                    const fileModal = new bootstrap.Modal(document.getElementById(
                                        'fileModal'));

                                    // Ajustar el tamaño del iframe según el tipo de archivo
                                    switch (item.tipo_multimedia.nombre) {
                                        case 'Documento':
                                            fileFrame.style.height = '800px';
                                            modalBody.style.height = '800px';
                                            break;
                                        case 'Video':
                                            fileFrame.style.height = '400px';
                                            modalBody.style.height = '400px';
                                            break;
                                        case 'Imagen':
                                            fileFrame.style.height = '500px';
                                            modalBody.style.height = '530px';
                                            break;
                                        default:
                                            fileFrame.style.height = 'auto';
                                            modalBody.style.height = 'auto';
                                    }

                                    fileModal.show();
                                };

                                const info = document.createElement('div');
                                info.classList.add('novedad-info');

                                const title = document.createElement('h6');
                                title.classList.add('novedad-title');
                                title.textContent = item.titulo;

                                const date = document.createElement('span');
                                date.classList.add('novedad-date');
                                date.textContent = new Date(item.fecha).toLocaleDateString();

                                const text = document.createElement('p');
                                text.classList.add('novedad-text');
                                text.textContent = item.descripcion;

                                const icon = document.createElement('img');
                                if (item.tipo_multimedia && item.tipo_multimedia.nombre) {
                                    switch (item.tipo_multimedia.nombre) {
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
                                novedadesContainer.appendChild(card);
                            }
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Fetch and display all novedades
            const sedeId = "{{ $turnos->first()->sede->id ?? '' }}"; // Obtener el primer sedeId disponible
            console.log('SedeID: ', sedeId);
            fetch(`{{ url('/novedades') }}/${sedeId}`)
                .then(response => response.json())
                .then(novedades => {
                    if (Array.isArray(novedades)) {
                        novedades.forEach(novedad => {
                            const card = document.createElement('div');
                            card.classList.add('novedad-card');
                            card.style.cursor = 'pointer';
                            card.onclick = function() {
                                const fileFrame = document.getElementById('fileFrame');
                                fileFrame.src = `{{ asset('storage') }}/${novedad.url}`;
                                const fileModalLabel = document.getElementById('fileModalLabel');
                                fileModalLabel.textContent = novedad.url.split('/')
                            .pop(); // Mostrar el nombre del archivo
                                const fileModal = new bootstrap.Modal(document.getElementById(
                                    'fileModal'));
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
                            novedadesContainer.appendChild(card);
                        });
                    } else {
                        console.error('Error: La respuesta no es un array.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    <link rel="stylesheet" href="{{ asset('assets/css/informacion-novedades.css') }}">
@endsection
