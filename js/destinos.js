document.addEventListener('DOMContentLoaded', function () {

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

    const tipoInfo = {
        restaurante: { icon: '🍽️', label: 'Restaurante' },
        turistico: { icon: '📍', label: 'Centro turístico' }
    };

    /* ===========================================================
       PÁGINA PÚBLICA: destinos.html (mapa + tarjetas)
       =========================================================== */
    const cardsContainer = document.getElementById('destinos-cards');
    if (cardsContainer) {
        const mapCol = document.getElementById('destinos-map-col');
        const mapIframe = document.getElementById('destinos-map-iframe');
        const detailPanel = document.getElementById('destino-detail');
        const detailTipo = document.getElementById('destino-detail-tipo');
        const detailNombre = document.getElementById('destino-detail-nombre');
        const detailDireccion = document.getElementById('destino-detail-direccion');
        const detailDescripcion = document.getElementById('destino-detail-descripcion');
        const detailDato = document.getElementById('destino-detail-dato');
        const detailClose = document.getElementById('destino-detail-close');

        const DEFAULT_MAP_SRC = mapIframe.src;

        function closeDetail() {
            detailPanel.classList.remove('visible');
            mapCol.classList.remove('detail-open');
            mapIframe.src = DEFAULT_MAP_SRC;
            document.querySelectorAll('.destino-card.active').forEach(c => c.classList.remove('active'));
        }

        function openDetail(destino, cardEl) {
            document.querySelectorAll('.destino-card.active').forEach(c => c.classList.remove('active'));
            cardEl.classList.add('active');

            const info = tipoInfo[destino.tipo] || tipoInfo.turistico;
            detailTipo.textContent = info.label;
            detailNombre.textContent = destino.nombre;
            detailDireccion.textContent = destino.direccion || '';
            detailDireccion.style.display = destino.direccion ? 'block' : 'none';
            detailDescripcion.textContent = destino.descripcion;
            detailDato.textContent = destino.dato_curioso ? `💡 Dato curioso: ${destino.dato_curioso}` : '';

            detailPanel.classList.add('visible');
            mapCol.classList.add('detail-open');
            mapIframe.src = `https://www.google.com/maps?q=${destino.lat},${destino.lng}&z=17&output=embed`;

            mapCol.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        if (detailClose) {
            detailClose.addEventListener('click', closeDetail);
        }

        fetch('obtener_destinos.php')
            .then(res => res.json())
            .then(destinos => {
                if (!Array.isArray(destinos) || destinos.length === 0) {
                    cardsContainer.innerHTML = '<div class="news-empty">Aún no hay destinos agregados. ¡Sé el primero en sumar uno!</div>';
                    return;
                }

                cardsContainer.innerHTML = '';
                destinos.forEach((destino, index) => {
                    const info = tipoInfo[destino.tipo] || tipoInfo.turistico;
                    const card = document.createElement('button');
                    card.type = 'button';
                    card.className = 'destino-card fade-in-up';
                    
                    card.innerHTML = `
                        <span class="destino-card-icon">${info.icon}</span>
                        <span class="destino-card-body">
                            <h4>${destino.nombre}</h4>
                            <p>${destino.direccion ? destino.direccion : info.label}</p>
                        </span>
                    `;
                    card.addEventListener('click', () => openDetail(destino, card));
                    cardsContainer.appendChild(card);

                    // Animar tarjeta
                    if (window.scrollObserver) {
                        window.scrollObserver.observe(card);
                    }
                });
            })
            .catch(err => {
                console.error('Error al cargar destinos:', err);
                cardsContainer.innerHTML = '<div class="news-empty">Error al cargar los destinos.</div>';
            });
    }

    /* ===========================================================
       PANEL ADMIN: panel_destinos.php (formulario de creación)
       =========================================================== */
    const destinoForm = document.getElementById('destino-form');
    if (destinoForm) {
        const latInput = document.getElementById('destino-lat');
        const lngInput = document.getElementById('destino-lng');
        const coordsPaste = document.getElementById('coords-paste');
        const mapPreview = document.getElementById('destino-map-preview');
        const uploadBtn = document.getElementById('destino-upload-btn');
        const imageInput = document.getElementById('destino-image-input');
        const previewBox = document.getElementById('destino-preview-box');
        const previewImage = document.getElementById('destino-preview');

        function updateMapPreview() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                mapPreview.src = `https://www.google.com/maps?q=${lat},${lng}&z=16&output=embed`;
            }
        }

        [latInput, lngInput].forEach(input => {
            input.addEventListener('change', updateMapPreview);
        });

        if (coordsPaste) {
            coordsPaste.addEventListener('input', () => {
                const match = coordsPaste.value.match(/-?\d+(\.\d+)?/g);
                if (match && match.length >= 2) {
                    latInput.value = match[0];
                    lngInput.value = match[1];
                    updateMapPreview();
                }
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
                    if (placeholder) placeholder.textContent = 'Vista previa de la imagen';
                };
                reader.readAsDataURL(file);
            });
        }

        destinoForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const submitBtn = destinoForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Guardando...';
            submitBtn.disabled = true;

            fetch(destinoForm.action, {
                method: destinoForm.method,
                body: new FormData(destinoForm)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToastLocal(data.message, 'success');
                        destinoForm.reset();
                        mapPreview.src = 'https://www.google.com/maps?q=Antofagasta,+Chile&output=embed';
                        if (previewBox && previewImage) {
                            previewBox.classList.remove('has-image');
                            previewImage.style.display = 'none';
                            const placeholder = previewBox.querySelector('span');
                            if (placeholder) placeholder.textContent = 'Sin imagen seleccionada';
                        }
                    } else {
                        showToastLocal(data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToastLocal('Error al conectar con el servidor.', 'error');
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        });
    }

    /* ===========================================================
       GESTOR ADMIN: gestor_destinos.php (listado + eliminar)
       =========================================================== */
    const tbody = document.getElementById('gestor-destinos-tbody');
    if (tbody) {
        function loadDestinos() {
            fetch('obtener_destinos.php')
                .then(res => res.json())
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No hay destinos publicados.</td></tr>';
                        return;
                    }
                    tbody.innerHTML = data.map(d => {
                        const info = tipoInfo[d.tipo] || tipoInfo.turistico;
                        return `
                            <tr>
                                <td>${d.nombre}</td>
                                <td>${info.icon} ${info.label}</td>
                                <td>${d.direccion || '-'}</td>
                                <td>
                                    <button type="button" class="action-btn btn-delete" data-id="${d.id}">Eliminar</button>
                                </td>
                            </tr>
                        `;
                    }).join('');

                    document.querySelectorAll('.btn-delete').forEach(btn => {
                        btn.addEventListener('click', function () {
                            if (confirm('¿Estás seguro de que deseas eliminar este destino? Esta acción no se puede deshacer.')) {
                                deleteDestino(this.getAttribute('data-id'));
                            }
                        });
                    });
                })
                .catch(err => {
                    console.error('Error al cargar destinos:', err);
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:red;">Error al cargar los destinos.</td></tr>';
                });
        }

        function deleteDestino(id) {
            const formData = new FormData();
            formData.append('id', id);
            fetch('eliminar_destino.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToastLocal(data.message, 'success');
                        loadDestinos();
                    } else {
                        showToastLocal(data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToastLocal('Error al conectar con el servidor.', 'error');
                });
        }

        loadDestinos();
    }
});
