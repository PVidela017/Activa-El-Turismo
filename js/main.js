document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('theme-toggle');
    const backToTopButton = document.getElementById('back-to-top');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.body.classList.add('dark-mode');
        if (toggleButton) {
            toggleButton.textContent = '☀️';
            toggleButton.setAttribute('aria-label', 'Cambiar a modo claro');
        }
    }

    if (toggleButton) {
        toggleButton.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            toggleButton.textContent = isDark ? '☀️' : '🌙';
            toggleButton.setAttribute('aria-label', isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
        });
    }

    if (backToTopButton) {
        const toggleBackToTop = () => {
            if (window.scrollY > 250) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        };

        window.addEventListener('scroll', toggleBackToTop);
        toggleBackToTop();

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    const newsCardsContainer = document.getElementById('news-cards');
    const recentNewsCardsContainer = document.getElementById('recent-news-cards');
    const activeContainer = newsCardsContainer || recentNewsCardsContainer;

    if (activeContainer) {
        const isRecentOnly = !!recentNewsCardsContainer;

        const formatDate = (value) => {
            const date = new Date(value);
            // Ajustar zona horaria si es necesario
            date.setMinutes(date.getMinutes() + date.getTimezoneOffset());
            return date.toLocaleDateString('es-CL', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        };

        const loadNewsData = (search = '', cat = '') => {
            activeContainer.innerHTML = '<div class="news-loading">Cargando noticias...</div>';
            const url = `obtener_noticias.php?search=${encodeURIComponent(search)}&cat=${encodeURIComponent(cat)}`;
            
            fetch(url)
                .then(response => response.json())
                .then(noticias => {
                    if (noticias.error) {
                        activeContainer.innerHTML = '<div class="news-empty">Error al cargar las noticias.</div>';
                        return;
                    }
                    const listToRender = isRecentOnly ? noticias.slice(0, 2) : noticias;

                    activeContainer.innerHTML = listToRender.length > 0
                        ? listToRender.map((noticia) => {
                            return `
                                <article class="news-card new-layout fade-in-up">
                                    <div class="news-card-left">
                                        <div class="news-card-header">
                                            <h3>${noticia.titulo}</h3>
                                            <span class="news-date">${formatDate(noticia.fecha)}</span>
                                        </div>
                                        ${noticia.subtitulo ? `<p class="news-subtitle">${noticia.subtitulo}</p>` : ''}
                                        <p class="news-author">Por ${noticia.autor}</p>
                                        
                                        <a href="noticia_completa.html?slug=${noticia.slug_not}" class="news-btn read-more-btn">Leer Noticia completa</a>
                                    </div>
                                    <div class="news-card-right">
                                        ${noticia.imagen_path ? `<img src="${noticia.imagen_path}" alt="Imagen de la noticia" class="news-image">` : `<div class="news-image-placeholder empty-img"></div>`}
                                        ${noticia.pie_imagen ? `<p class="news-caption">${noticia.pie_imagen}</p>` : ''}
                                    </div>
                                </article>
                            `;
                        }).join('')
                        : '<div class="news-empty">No se encontraron noticias con esos criterios.</div>';

                    // Animar los elementos renderizados
                    activeContainer.querySelectorAll('.fade-in-up').forEach(el => window.scrollObserver && window.scrollObserver.observe(el));
                })
                .catch(error => {
                    console.error('Error cargando noticias:', error);
                    activeContainer.innerHTML = '<div class="news-empty">Error al cargar las noticias.</div>';
                });
        };

        const searchInput = document.getElementById('search-input');
        const catFilter = document.getElementById('category-filter');
        
        if (catFilter) {
            fetch('obtener_categorias.php')
                .then(res => res.json())
                .then(cats => {
                    cats.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id_cat;
                        opt.textContent = c.nombre_cat;
                        catFilter.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error cargando categorías', err));
        }

        if (searchInput || catFilter) {
            let timeout = null;
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        loadNewsData(searchInput.value, catFilter ? catFilter.value : '');
                    }, 400);
                });
            }
            if (catFilter) {
                catFilter.addEventListener('change', () => {
                    loadNewsData(searchInput ? searchInput.value : '', catFilter.value);
                });
            }
        }

        loadNewsData();
    }

    const singleNewsContainer = document.getElementById('single-news-container');
    if (singleNewsContainer) {
        const urlParams = new URLSearchParams(window.location.search);
        const newsSlug = urlParams.get('slug');

        if (!newsSlug) {
            singleNewsContainer.innerHTML = '<div class="news-empty">Noticia no especificada. <br><br> <a href="noticias.html" class="action-btn">Volver a Noticias</a></div>';
        } else {
            const formatDate = (value) => {
                const date = new Date(value);
                date.setMinutes(date.getMinutes() + date.getTimezoneOffset());
                return date.toLocaleDateString('es-CL', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            };

            fetch(`obtener_noticias.php?slug=${newsSlug}`)
                .then(response => response.json())
                .then(noticia => {
                    if (noticia.error) {
                        singleNewsContainer.innerHTML = `<div class="news-empty">${noticia.error}. <br><br> <a href="noticias.html" class="action-btn">Volver a Noticias</a></div>`;
                        return;
                    }

                    const cuerpoFormateado = noticia.cuerpo.replace(/\n/g, '<br>');

                    singleNewsContainer.innerHTML = `
                        <article class="single-news-detail">
                            <div class="single-news-header">
                                <h1 class="single-news-title">${noticia.titulo}</h1>
                                ${noticia.subtitulo ? `<p class="single-news-subtitle">${noticia.subtitulo}</p>` : ''}
                                <div class="single-news-meta">
                                    <span class="news-author">Por <strong>${noticia.autor}</strong></span>
                                    <span class="meta-divider">•</span>
                                    <span class="news-date">${formatDate(noticia.fecha)}</span>
                                </div>
                            </div>
                            
                            ${noticia.imagen_path ? `
                                <div class="single-news-media">
                                    <img src="${noticia.imagen_path}" alt="${noticia.titulo}" class="single-news-image">
                                    ${noticia.pie_imagen ? `<p class="single-news-caption">${noticia.pie_imagen}</p>` : ''}
                                </div>
                            ` : ''}

                            <div class="single-news-content">
                                <p class="single-news-body">${cuerpoFormateado}</p>
                            </div>
                            
                            <div class="single-news-actions">
                                <a href="noticias.html" class="action-btn">← Volver a Noticias</a>
                            </div>
                        </article>
                    `;
                })
                .catch(error => {
                    console.error('Error cargando la noticia:', error);
                    singleNewsContainer.innerHTML = '<div class="news-empty">Error al cargar la noticia. <br><br> <a href="noticias.html" class="action-btn">Volver a Noticias</a></div>';
                });
        }

    }

    const articleForm = document.getElementById('article-form-prototype');
    const uploadButton = document.getElementById('upload-image-btn');
    const imageInput = document.getElementById('article-image-input');
    const previewBox = document.getElementById('image-preview-box');
    const previewImage = document.getElementById('image-preview');

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icon = type === 'success' ? '✅' : '❌';
        
        toast.innerHTML = `<span>${icon}</span> <span>${message}</span>`;
        toastContainer.appendChild(toast);
        
        toast.offsetHeight;
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    if (articleForm) {
        articleForm.addEventListener('submit', (event) => {
            event.preventDefault();
            
            const formData = new FormData(articleForm);
            const submitBtn = articleForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Publicando...';
            submitBtn.disabled = true;

            fetch(articleForm.action, {
                method: articleForm.method,
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    articleForm.reset();
                    if (previewBox && previewImage) {
                        previewBox.classList.remove('has-image');
                        previewImage.style.display = 'none';
                        previewImage.src = '';
                        const placeholderText = previewBox.querySelector('span');
                        if (placeholderText) {
                            placeholderText.textContent = 'Sin imagen seleccionada';
                        }
                    }
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ocurrió un error al conectar con el servidor.', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    if (uploadButton && imageInput) {
        uploadButton.addEventListener('click', () => {
            imageInput.click();
        });
    }

    if (imageInput && previewBox && previewImage) {
        imageInput.addEventListener('change', () => {
            const file = imageInput.files && imageInput.files[0];
            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                previewImage.src = event.target.result;
                previewImage.style.display = 'block';
                previewBox.classList.add('has-image');
                const placeholderText = previewBox.querySelector('span');
                if (placeholderText) {
                    placeholderText.textContent = 'Vista previa de la imagen';
                }
            };
            reader.readAsDataURL(file);
        });
    }

    const bgSlides = document.querySelectorAll('.bg-slide');

    if (bgSlides.length > 0) {
        let currentBgSlide = 0;
        const slideCount = bgSlides.length;

        function nextBgSlide() {
            bgSlides[currentBgSlide].classList.remove('active');
            currentBgSlide = (currentBgSlide + 1) % slideCount;
            bgSlides[currentBgSlide].classList.add('active');
        }

        setInterval(nextBgSlide, 5000);
    }

    const videoCardsContainer = document.getElementById('video-cards-container');
    if (videoCardsContainer) {
        fetch('obtener_videos.php')
            .then(res => res.json())
            .then(videos => {
                if (videos.length > 0) {
                    videoCardsContainer.innerHTML = videos.map(v => `
                        <article class="episode fade-in-up">
                            <div class="episode-left">
                                <h3 class="episode-title">${v.titulo}</h3>
                                ${v.descripcion ? `<p class="episode-recap">${v.descripcion}</p>` : ''}
                                <p class="episode-meta">Duración: ${v.duracion} · Fecha: ${new Date(v.fecha_publicacion).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })}</p>
                            </div>
                            <div class="episode-right">
                                ${v.iframe_codigo}
                            </div>
                        </article>
                    `).join('');

                    // Animar los videos renderizados
                    videoCardsContainer.querySelectorAll('.fade-in-up').forEach(el => window.scrollObserver && window.scrollObserver.observe(el));
                } else {
                    videoCardsContainer.innerHTML = '<div class="news-empty">No hay videos disponibles.</div>';
                }
            })
            .catch(err => {
                console.error(err);
                videoCardsContainer.innerHTML = '<div class="news-empty">Error al cargar videos.</div>';
            });
    }

    const videoForm = document.getElementById('video-form-prototype');
    if (videoForm) {
        videoForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = videoForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Agregando...';
            submitBtn.disabled = true;

            fetch(videoForm.action, {
                method: videoForm.method,
                body: new FormData(videoForm)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    videoForm.reset();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error de red al agregar video.', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    const podcastForm = document.getElementById('podcast-form-prototype');
    if (podcastForm) {
        podcastForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = podcastForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Agregando...';
            submitBtn.disabled = true;

            fetch(podcastForm.action, {
                method: podcastForm.method,
                body: new FormData(podcastForm)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    podcastForm.reset();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error de red al agregar podcast.', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    const accordionHeaders = document.querySelectorAll('.accordion-header');
    accordionHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const content = header.nextElementSibling;
            const icon = header.querySelector('.accordion-icon');
            
            // Toggle actual
            content.classList.toggle('active');
            if (icon) icon.classList.toggle('open');
        });
    });

    // --- Animaciones Fade In ---
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.15
    };

    window.scrollObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in-up').forEach(el => {
        window.scrollObserver.observe(el);
    });
});
