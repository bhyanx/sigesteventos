// public/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });


    // Previsualización de imágenes en formularios
    const coverImageInput = document.getElementById('cover_image');
    if (coverImageInput) {
        coverImageInput.addEventListener('blur', function() {
            const imageUrl = this.value.trim();
            if (imageUrl) {
                // Si ya existe una previsualización, actualízala
                let previewContainer = document.getElementById('coverPreview');
                if (!previewContainer) {
                    // Crear el contenedor de previsualización si no existe
                    previewContainer = document.createElement('div');
                    previewContainer.id = 'coverPreview';
                    previewContainer.className = 'mt-2';
                    this.parentNode.appendChild(previewContainer);
                }
               
                previewContainer.innerHTML = `
                    <div class="card" style="max-width: 200px;">
                        <img src="${imageUrl}" class="card-img-top" alt="Previsualización de portada" onerror="this.onerror=null;this.src='https://via.placeholder.com/200x300?text=Imagen+no+disponible';">
                        <div class="card-body p-2">
                            <p class="card-text small text-muted text-center">Previsualización</p>
                        </div>
                    </div>
                `;
            }
        });
    }


    // Confirmación de eliminación
    const deleteButtons = document.querySelectorAll('[data-delete-book]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de que deseas eliminar este libro? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });


    // Animación de entrada para tarjetas
    const bookCards = document.querySelectorAll('.book-card');
    bookCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});


// Función para validar formularios
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
   
    let isValid = true;
   
    // Validar campos requeridos
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
   
    // Validar ISBN
    const isbnField = form.querySelector('#isbn');
    if (isbnField && isbnField.value.trim()) {
        const isbn = isbnField.value.trim().replace(/-/g, '');
        const isbnRegex = /^(?:\d{10}|\d{13})$/;
       
        if (!isbnRegex.test(isbn)) {
            isbnField.classList.add('is-invalid');
            isValid = false;
           
            // Añadir mensaje de error
            let errorMessage = document.getElementById('isbn-error');
            if (!errorMessage) {
                errorMessage = document.createElement('div');
                errorMessage.id = 'isbn-error';
                errorMessage.className = 'invalid-feedback';
                errorMessage.textContent = 'El ISBN debe tener 10 o 13 dígitos';
                isbnField.parentNode.appendChild(errorMessage);
            }
        } else {
            isbnField.classList.remove('is-invalid');
        }
    }
   
    return isValid;
}
