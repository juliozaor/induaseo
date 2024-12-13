function handleFileSelect(event, actividadId) {
    const files = event.target.files;
    const evidenciasContainer = document.getElementById(`evidenciasContainer${actividadId}`);
    
    if (evidenciasContainer.children.length + files.length > 3) {
        alert('Solo puedes agregar un máximo de 3 imágenes.');
        return;
    }

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

function removeImage(button) {
    const span = button.parentNode;
    span.parentNode.removeChild(span);
}

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

function setTurnoData(turnoId, sedeId) {
    localStorage.setItem('turno_id', turnoId);
    localStorage.setItem('sede_id', sedeId);
}

function getTurnoData() {
    return {
        turnoId: localStorage.getItem('turno_id'),
        sedeId: localStorage.getItem('sede_id')
    };
}

window.setTurnoData = setTurnoData;
