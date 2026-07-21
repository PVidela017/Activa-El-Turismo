document.addEventListener('DOMContentLoaded', function() {
    const episodesList = document.getElementById('episodes-list');
    const mainPlayerWrapper = document.getElementById('main-player-wrapper');
    const mainPlayerTitle = document.getElementById('main-player-title');
    const mainPlayerDesc = document.getElementById('main-player-desc');
    const mainPlayerMeta = document.getElementById('main-player-meta');

    let podcastsData = [];

    // Cargar podcasts desde la base de datos
    fetch('obtener_podcasts.php')
        .then(response => response.json())
        .then(data => {
            podcastsData = data;
            if (data.length === 0) {
                episodesList.innerHTML = '<p style="text-align: center; color: var(--text-muted);">No hay podcasts disponibles.</p>';
                return;
            }

            // Renderizar la lista de episodios
            let html = '';
            data.forEach((podcast, index) => {
                const formattedDate = new Date(podcast.fecha_publicacion).toLocaleDateString('es-ES', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                });
                
                html += `
                    <article class="episode fade-in-up" data-id="${podcast.id}" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; display: flex; flex-direction: column; background: var(--card-bg);">
                        <h3 style="margin-bottom: 0.5rem; color: var(--heading-color); font-size: 1.25rem;">${podcast.titulo}</h3>
                        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${podcast.descripcion}</p>
                        <p style="font-size: 0.85rem; font-weight: 500;">Duración: ${podcast.duracion} · Fecha: ${formattedDate}</p>
                    </article>
                `;
            });
            episodesList.innerHTML = html;

            // Animar los episodios generados
            episodesList.querySelectorAll('.fade-in-up').forEach(el => window.scrollObserver && window.scrollObserver.observe(el));

            // Cargar el primer podcast por defecto si existe
            if (data.length > 0) {
                loadPodcastToMain(data[0].id);
            }

            // Añadir eventos click a cada tarjeta
            document.querySelectorAll('.episode').forEach(card => {
                card.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    loadPodcastToMain(id);
                    
                    // Hacer scroll hacia arriba suavemente si estamos en móvil para ver el reproductor
                    window.scrollTo({
                        top: mainPlayerWrapper.offsetTop - 100,
                        behavior: 'smooth'
                    });
                });
                
                // Efectos hover
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = 'var(--shadow-lg)';
                    this.style.borderColor = 'var(--primary-color)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'none';
                    this.style.boxShadow = 'none';
                    this.style.borderColor = 'var(--border-color)';
                });
            });
        })
        .catch(err => {
            console.error('Error al cargar podcasts:', err);
            episodesList.innerHTML = '<p style="text-align: center; color: red;">Error al cargar los episodios. Por favor, intenta más tarde.</p>';
        });

    function loadPodcastToMain(id) {
        const podcast = podcastsData.find(p => p.id == id);
        if (!podcast) return;

        const formattedDate = new Date(podcast.fecha_publicacion).toLocaleDateString('es-ES', {
            day: '2-digit', month: 'long', year: 'numeric'
        });

        mainPlayerWrapper.innerHTML = podcast.iframe_codigo;
        mainPlayerTitle.textContent = podcast.titulo;
        mainPlayerDesc.textContent = podcast.descripcion;
        mainPlayerMeta.textContent = `Duración: ${podcast.duracion} · Publicado el ${formattedDate}`;
        
        // Resaltar la tarjeta seleccionada
        document.querySelectorAll('.episode').forEach(card => {
            if (card.getAttribute('data-id') == id) {
                card.style.background = 'var(--bg-color)';
                card.style.borderLeft = '4px solid var(--primary-color)';
            } else {
                card.style.background = 'var(--card-bg)';
                card.style.borderLeft = '1px solid var(--border-color)';
            }
        });
    }
});
