document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('gestor-news-tbody');
    if (!tbody) return; // No estamos en la página del gestor

    const editModal = document.getElementById('edit-modal');
    const editForm = document.getElementById('edit-article-form');
    const cancelBtn = document.getElementById('cancel-edit-btn');
    
    const editId = document.getElementById('edit-id');
    const editFecha = document.getElementById('edit-fecha');
    const editTitulo = document.getElementById('edit-titulo');
    const editSubtitulo = document.getElementById('edit-subtitulo');
    const editAutor = document.getElementById('edit-autor');
    const editCuerpo = document.getElementById('edit-cuerpo');
    const editPieImagen = document.getElementById('edit-pieImagen');
    const currentImageLink = document.getElementById('current-image-link');
    
    const uploadBtn = document.getElementById('edit-upload-image-btn');
    const imageInput = document.getElementById('edit-article-image-input');
    const previewBox = document.getElementById('edit-image-preview-box');
    const previewImage = document.getElementById('edit-image-preview');

    let currentNewsList = [];

    function showToastLocal(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            alert(message);
            return;
        }
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const icon = type === 'success' ? '✅' : '❌';
        toast.innerHTML = `<span>${icon}</span> <span>${message}</span>`;
        toastContainer.appendChild(toast);
        toast.offsetHeight;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function loadNews() {
        fetch('obtener_noticias.php')
            .then(response => response.json())
            .then(data => {
                currentNewsList = data;
                renderNews(data);
            })
            .catch(error => {
                console.error('Error al cargar noticias:', error);
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:red;">Error al cargar las noticias.</td></tr>';
            });
    }

    function renderNews(noticias) {
        if (noticias.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No hay noticias publicadas.</td></tr>';
            return;
        }

        tbody.innerHTML = noticias.map(n => `
            <tr>
                <td>${n.fecha}</td>
                <td>${n.titulo}</td>
                <td>${n.autor}</td>
                <td>
                    <button type="button" class="action-btn btn-edit" data-id="${n.id}">Editar</button>
                    <button type="button" class="action-btn btn-delete" data-id="${n.id}">Eliminar</button>
                </td>
            </tr>
        `).join('');

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                openEditForm(this.getAttribute('data-id'));
            });
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('¿Estás seguro de que deseas eliminar esta noticia? Esta acción no se puede deshacer.')) {
                    deleteNews(this.getAttribute('data-id'));
                }
            });
        });
    }

    function openEditForm(id) {
        const noticia = currentNewsList.find(n => n.id == id);
        if (!noticia) return;

        editId.value = noticia.id;
        editFecha.value = noticia.fecha;
        editTitulo.value = noticia.titulo;
        editSubtitulo.value = noticia.subtitulo || '';
        editAutor.value = noticia.autor;
        editCuerpo.value = noticia.cuerpo;
        editPieImagen.value = noticia.pie_imagen || '';

        if (noticia.imagen_path) {
            currentImageLink.href = noticia.imagen_path;
            currentImageLink.textContent = noticia.imagen_path.split('/').pop();
        } else {
            currentImageLink.removeAttribute('href');
            currentImageLink.textContent = 'Ninguna';
        }

        imageInput.value = '';
        previewBox.classList.remove('has-image');
        previewImage.style.display = 'none';
        previewImage.src = '';
        const placeholder = previewBox.querySelector('span');
        if (placeholder) placeholder.textContent = 'Sin imagen nueva seleccionada';

        editModal.style.display = 'block';
        editModal.scrollIntoView({ behavior: 'smooth' });
    }

    function deleteNews(id) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('eliminar_noticia.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToastLocal(data.message, 'success');
                editModal.style.display = 'none';
                loadNews();
            } else {
                showToastLocal(data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToastLocal('Error al conectar con el servidor.', 'error');
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            editModal.style.display = 'none';
            editForm.reset();
        });
    }

    if (uploadBtn && imageInput) {
        uploadBtn.addEventListener('click', () => imageInput.click());
    }

    if (imageInput && previewBox && previewImage) {
        imageInput.addEventListener('change', () => {
            const file = imageInput.files && imageInput.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                previewImage.src = event.target.result;
                previewImage.style.display = 'block';
                previewBox.classList.add('has-image');
                const placeholder = previewBox.querySelector('span');
                if (placeholder) placeholder.textContent = 'Vista previa de la nueva imagen';
            };
            reader.readAsDataURL(file);
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = editForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Guardando...';
            submitBtn.disabled = true;
            
            fetch(editForm.action, {
                method: editForm.method,
                body: new FormData(editForm)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastLocal(data.message, 'success');
                    editModal.style.display = 'none';
                    loadNews();
                } else {
                    showToastLocal(data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToastLocal('Ocurrió un error al conectar con el servidor.', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    loadNews();
});
