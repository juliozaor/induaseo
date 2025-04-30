// Maneja la selección de archivos para evidencias
function handleFileSelect(event, actividadId) {
    const files = event.target.files;
    const evidenciasContainer = document.getElementById(`evidenciasContainer${actividadId}`);

    // Verifica si se excede el límite de 3 imágenes
    if (evidenciasContainer.children.length + files.length > 5) {
        alert('Solo puedes agregar un máximo de 5 imágenes.');
        return;
    }

    // Procesa cada archivo seleccionado
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (!file.type.match('image.*')) {
            continue;
        }

        const reader = new FileReader();
        reader.onload = (function(theFile) {
            return function(e) {
                const span = document.createElement('span');
                span.innerHTML = `<img class="thumb" src="${e.target.result}" title="${theFile.name}"/><button onclick="removeImage(this)">Eliminar</button>`;
                evidenciasContainer.appendChild(span);
            };
        })(file);
        reader.readAsDataURL(file);
    }
}

// Elimina una imagen de las evidencias
function removeImage(button) {
    const span = button.parentNode;
    span.parentNode.removeChild(span);
}

// Califica una actividad con estrellas
function rateActivity(actividadId, rating) {
    const stars = document.querySelectorAll(`#calificacion${actividadId} .fa`);
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('fa-star-o');
            star.classList.add('fa-star');
        } else {
            star.classList.remove('fa-star');
            star.classList.add('fa-star-o');
        }
    });
}

// Guarda los datos del turno en el almacenamiento local
function setTurnoData(turnoId, sedeId) {
    localStorage.setItem('turno_id', turnoId);
    localStorage.setItem('sede_id', sedeId);
}

// Obtiene los datos del turno del almacenamiento local
function getTurnoData() {
    return {
        turnoId: localStorage.getItem('turno_id'),
        sedeId: localStorage.getItem('sede_id')
    };
}

// Expone la función setTurnoData globalmente
window.setTurnoData = setTurnoData;
