document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('gestor-videos-tbody');
    const editModal = document.getElementById('edit-modal');
    const editForm = document.getElementById('edit-video-form');
    const btnCancel = document.getElementById('btn-cancel-edit');
    let videosData = [];

    function loadVideos() {
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px;">Cargando videos...</td></tr>';
        
        fetch('obtener_videos.php')
            .then(res => res.json())
            .then(data => {
                videosData = data;
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px;">No hay videos publicados.</td></tr>';
                    return;
                }
                
                let html = '';
                data.forEach(video => {
                    const formattedDate = new Date(video.fecha_publicacion).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    
                    html += `
                        <tr>
                            <td>${formattedDate}</td>
                            <td><strong>${video.titulo}</strong></td>
                            <td>${video.duracion}</td>
                            <td>
                                <button class="action-btn btn-edit" data-id="${video.id}">Editar</button>
                                <button class="action-btn btn-delete" data-id="${video.id}">Eliminar</button>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
                attachEventListeners();
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: red;">Error al cargar videos.</td></tr>';
            });
    }

    function attachEventListeners() {
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.getAttribute('data-id');
                const video = videosData.find(v => v.id == id);
                if (video) {
                    document.getElementById('edit-id').value = video.id;
                    
                    // Format date to YYYY-MM-DD for the input[type=date]
                    try {
                        const dateObj = new Date(video.fecha_publicacion);
                        if (!isNaN(dateObj)) {
                            document.getElementById('edit-fecha').value = dateObj.toISOString().split('T')[0];
                        }
                    } catch(e) {}

                    document.getElementById('edit-titulo').value = video.titulo;
                    document.getElementById('edit-descripcion').value = video.descripcion;
                    document.getElementById('edit-duracion').value = video.duracion;
                    
                    editModal.style.display = 'block';
                    editModal.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.getAttribute('data-id');
                if (confirm('¿Estás seguro de que deseas eliminar este video? Esta acción no se puede deshacer.')) {
                    fetch('eliminar_video.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + id
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                            setTimeout(() => window.location.reload(), 1000);
                            editModal.style.display = 'none';
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('Error al eliminar video.', 'error');
                    });
                }
            });
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', () => {
            editModal.style.display = 'none';
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
                    showToast(data.message, 'success');
                    editModal.style.display = 'none';
                    loadVideos();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error al actualizar video.', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Inicializar
    loadVideos();
});
