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

        fetch('obtener_noticias.php')
            .then(response => response.json())
            .then(noticias => {
                const listToRender = isRecentOnly ? noticias.slice(0, 2) : noticias;

                activeContainer.innerHTML = listToRender.length > 0
                    ? listToRender.map((noticia) => {
                        // Reemplazar saltos de línea por <br> para mantener el formato del texto
                        const cuerpoFormateado = noticia.cuerpo.replace(/\n/g, '<br>');

                        return `
                            <article class="news-card new-layout">
                                <div class="news-card-left">
                                    <div class="news-card-header">
                                        <h3>${noticia.titulo}</h3>
                                        <span class="news-date">${formatDate(noticia.fecha)}</span>
                                    </div>
                                    ${noticia.subtitulo ? `<p class="news-subtitle">${noticia.subtitulo}</p>` : ''}
                                    <p class="news-author">Por ${noticia.autor}</p>
                                    
                                    <div class="news-card-content-hidden">
                                        <p class="news-body">${cuerpoFormateado}</p>
                                    </div>
                                    
                                    <button type="button" class="news-btn read-more-btn">Leer Noticia completa</button>
                                </div>
                                <div class="news-card-right">
                                    ${noticia.imagen_path ? `<img src="${noticia.imagen_path}" alt="Imagen de la noticia" class="news-image">` : `<div class="news-image-placeholder empty-img"></div>`}
                                    ${noticia.pie_imagen ? `<p class="news-caption">${noticia.pie_imagen}</p>` : ''}
                                </div>
                            </article>
                        `;
                    }).join('')
                    : '<div class="news-empty">No hay noticias disponibles.</div>';

                // Añadir evento a los botones de expandir
                const readMoreBtns = activeContainer.querySelectorAll('.read-more-btn');
                readMoreBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const card = this.closest('.news-card');
                        card.classList.toggle('expanded');
                        
                        if (card.classList.contains('expanded')) {
                            this.textContent = 'Cerrar Noticia';
                        } else {
                            this.textContent = 'Leer Noticia completa';
                        }
                    });
                });
            })
            .catch(error => {
                console.error('Error cargando noticias:', error);
                activeContainer.innerHTML = '<div class="news-empty">Error al cargar las noticias.</div>';
            });
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

    const sliderTrack = document.querySelector('.slider-track');
    const sliderItems = document.querySelectorAll('.slider-item');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    const indicators = document.querySelectorAll('.indicator');

    if (!sliderTrack || sliderItems.length === 0) {
        return;
    }

    let currentSlide = 0;
    const slideCount = sliderItems.length;

    function updateSlider() {
        const offset = -currentSlide * 100;
        sliderTrack.style.transform = `translateX(${offset}%)`;

        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentSlide);
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slideCount;
        updateSlider();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slideCount) % slideCount;
        updateSlider();
    }

    function goToSlide(index) {
        currentSlide = index;
        updateSlider();
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', prevSlide);
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', nextSlide);
    }

    indicators.forEach((indicator) => {
        indicator.addEventListener('click', (e) => {
            const slideIndex = parseInt(e.target.dataset.slide, 10);
            goToSlide(slideIndex);
        });
    });

    setInterval(nextSlide, 5000);
});
