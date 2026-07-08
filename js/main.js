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
