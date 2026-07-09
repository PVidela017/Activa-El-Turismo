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
    if (newsCardsContainer) {
        const noticias = [
            {
                titulo: 'Fiesta del Desierto 2026',
                autor: 'Luis Carrasco',
                fecha: '2026-07-01',
                subtitulo: 'Una celebración para toda la región',
                entrada: 'La Fiesta del Desierto reúne a comunidades y turistas en un encuentro de cultura, música y gastronomía.',
                cuerpo: 'Este año, la celebración tendrá espacios de arte en vivo, ferias de emprendedores locales y talleres de cocina tradicional. El evento busca mostrar la riqueza patrimonial de la Región de Antofagasta y promover el turismo responsable.',
                imagen: 'img/muelle salitrero.jpg',
                pieimg: 'Visitantes celebrando junto a la costa desértica.'
            },
            {
                titulo: 'Rutas patrimoniales renovadas',
                autor: 'María González',
                fecha: '2026-06-15',
                subtitulo: 'Nuevas sendas para redescubrir la historia minera',
                entrada: 'Las rutas patrimoniales de la región han sido mejoradas para ofrecer recorridos más seguros y accesibles.',
                cuerpo: 'Incluyen señalización interactiva, paradas en puntos históricos y actividades guiadas para conocer el pasado minero y su impacto en la identidad local. Estas renovaciones esperan atraer a viajeros interesados en turismo cultural y sostenible.',
                imagen: 'img/ruinas huancacha.jpg',
                pieimg: 'Antiguo campamento minero ahora parte de una ruta accesible.'
            }
        ];

        const formatDate = (value) => {
            const date = new Date(value);
            return date.toLocaleDateString('es-CL', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        };

        newsCardsContainer.innerHTML = noticias.length > 0
            ? noticias.map((noticia) => {
                const imageStyle = noticia.imagen
                    ? `background-image: url('${noticia.imagen}'); background-size: cover; background-position: center;`
                    : '';

                return `
                    <article class="news-card">
                        <div class="news-card-top">
                            <div class="news-image-placeholder" style="${imageStyle}"></div>
                            <span class="news-date">${formatDate(noticia.fecha)}</span>
                        </div>
                        <h3>${noticia.titulo}</h3>
                        ${noticia.subtitulo ? `<p class="news-subtitle">${noticia.subtitulo}</p>` : ''}
                        <p class="news-author">Por ${noticia.autor}</p>
                        <p>${noticia.entrada}</p>
                        <p>${noticia.cuerpo}</p>
                        <a href="noticias.html" class="news-btn">Leer Noticia completa</a>
                    </article>
                `;
            }).join('')
            : '<div class="news-empty">No hay noticias disponibles.</div>';
    }

    const articleForm = document.getElementById('article-form-prototype');
    const uploadButton = document.getElementById('upload-image-btn');
    const imageInput = document.getElementById('article-image-input');
    const previewBox = document.getElementById('image-preview-box');
    const previewImage = document.getElementById('image-preview');

    if (articleForm) {
        articleForm.addEventListener('submit', (event) => {
            event.preventDefault();
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
