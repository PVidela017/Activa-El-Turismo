<?php require 'verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activa El Turismo | Panel de Noticias</title>
    <link rel="stylesheet" href="css/styles.css?v=2">
</head>

<body>
    <header class="site-header">
        <div class="header-inner">
            <div class="logo-container">
                <a href="index.html" class="logo-link">
                    <img src="img/Logo ActivaelTurismo.png" alt="Activa el Turismo logo" class="site-logo">
                </a>
                <a href="index.html" class="logo-link site-title-link">Activa El Turismo <br> Difundiendo el alma de la
                    Región de Antofagasta.</a>
            </div>

            <nav class="site-nav" aria-label="Main navigation">
                <a href="index.html" class="nav-link">Inicio</a>
                <a href="noticias.html" class="nav-link">Noticias</a>
                <a href="destinos.html" class="nav-link">Destinos</a>
                <a href="videos.html" class="nav-link">Videos</a>
                <a href="podcasts.html" class="nav-link">Podcasts</a>
                <a href="#" class="nav-link">Eventos</a>
                <a href="#site-footer" class="nav-link">Contacto</a>
                <span class="nav-admin-badge">👤 <?= htmlspecialchars($_SESSION['admin_nombre']) ?></span>
                <a href="logout.php" class="nav-link nav-logout-btn" title="Cerrar sesión">Cerrar Sesión</a>
                <button class="theme-toggle" id="theme-toggle" type="button"
                    aria-label="Cambiar a modo oscuro">🌙</button>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <h1>Panel de Noticias</h1>
            <p>Ingresa los datos del artículo que deseas publicar.</p>
        </section>
        <section class="panel-form-section">
            <div style="display: flex; gap: 1rem; justify-content: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <a href="noticias.html" class="news-btn">Volver a Noticias</a>
                <a href="gestor_noticias.php" class="news-btn" style="background-color: #2563eb; color: #fff;">Gestionar Noticias</a>
                <a href="gestor_videos.php" class="news-btn" style="background-color: #ef4444; color: #fff;">Gestionar Videos</a>
            </div>
            <div class="panel-form-card accordion-card">
                <h2 class="accordion-header">Crear artículo <span class="accordion-icon">▼</span></h2>
                <div class="accordion-content">
                    <form class="article-form-prototype" id="article-form-prototype" action="guardar_noticia.php"
                        method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <label class="form-field">
                            <span>Fecha</span>
                            <input type="date" name="fecha">
                        </label>
                        <label class="form-field">
                            <span>Título</span>
                            <input type="text" name="titulo" placeholder="Título del artículo">
                        </label>
                        <label class="form-field">
                            <span>Subtítulo</span>
                            <input type="text" name="subtitulo" placeholder="Subtítulo breve">
                        </label>
                        <label class="form-field">
                            <span>Autor</span>
                            <input type="text" name="autor" placeholder="Nombre del autor/autora">
                        </label>
                        <label class="form-field form-field-full">
                            <span>Cuerpo</span>
                            <textarea name="cuerpo" rows="8"
                                placeholder="Escribe el contenido principal del artículo..."></textarea>
                        </label>
                    </div>

                    <div class="image-upload-block">
                        <button type="button" class="image-upload-btn" id="upload-image-btn">Subir imagen</button>
                        <input type="file" id="article-image-input" name="imagen" accept="image/*" hidden>
                        <div class="image-preview-box" id="image-preview-box">
                            <span>Sin imagen seleccionada</span>
                            <img id="image-preview" alt="Previsualización de la imagen">
                        </div>
                        <label class="form-field">
                            <span>Pie de imagen</span>
                            <input type="text" name="pieImagen" placeholder="Añade contexto a la imagen">
                        </label>
                        <label class="form-field">
                            <span>Categoría</span>
                            <select name="id_cat" required>
                                <option value="" disabled selected>Selecciona una categoría</option>
                                <?php
                                require_once 'conexion.php';
                                $query_cat = "SELECT id_cat, nombre_cat FROM categorias ORDER BY nombre_cat ASC";
                                $result_cat = $conexion->query($query_cat);
                                if ($result_cat && $result_cat->num_rows > 0) {
                                    while ($row_cat = $result_cat->fetch_assoc()) {
                                        echo '<option value="' . $row_cat['id_cat'] . '">' . htmlspecialchars($row_cat['nombre_cat']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="form-action-btn primary">Publicar</button>
                    </div>
                </form>
                </div>
            </div>
            
            <div class="panel-form-card accordion-card" style="margin-top: 2rem;">
                <h2 class="accordion-header">Agregar Video <span class="accordion-icon">▼</span></h2>
                <div class="accordion-content">
                    <form class="video-form-prototype" id="video-form-prototype" action="agregar_video.php" method="POST">
                        <div class="form-grid">
                            <label class="form-field form-field-full">
                                <span>Enlace de YouTube</span>
                                <input type="url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=..." required>
                            </label>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="form-action-btn primary">Agregar Video</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <button class="back-to-top" id="back-to-top" type="button" aria-label="Volver arriba">↑</button>

    <div id="toast-container" class="toast-container"></div>

    <script src="js/main.js?v=2"></script>

    <footer id="site-footer" class="site-footer">
        <div class="footer-inner">
            <div class="footer-left">
                <a href="index.html" class="logo-link footer-logo-link">
                    <img src="img/Logo ActivaelTurismo.png" alt="Activa el Turismo logo" class="footer-logo">
                </a>
                <a href="index.html">
                    <p class="footer-copy">Activa El Turismo 2026 <br> Difundiendo el alma de la Región de Antofagasta.
                    </p>
                </a>
            </div>
            <div class="footer-links">
                <h3>¡Síguenos en nuestras Redes Sociales!</h3>
                <div class="footer-socials-row">
                    <a href="https://www.facebook.com/activaelturismo/" class="footer-link footer-social"
                        aria-label="Facebook"><img src="img/facebook-brands-solid.png" alt="Facebook"></a>
                    <a href="https://www.youtube.com/@activaelturismo5733" class="footer-link footer-social"
                        aria-label="YouTube"><img src="img/youtube-brands-solid.png" alt="YouTube"></a>
                    <a href="https://open.spotify.com/show/64Todh3Bnbe72WPwACtRf4" class="footer-link footer-social"
                        aria-label="Spotify"><img src="img/spotify-brands-solid.png" alt="Spotify"></a>
                </div>
            </div>
            <div class="footer-right">
                <div class="footer-contact">
                    <h3>Contacto</h3>
                    <a href="mailto:contacto@activaelturismo.cl" class="footer-contact-link">
                        <span class="footer-contact-icon">✉</span>
                        contacto@activaelturismo.cl
                    </a>
                    <a href="tel:+56912345678" class="footer-contact-link">
                        <span class="footer-contact-icon">☎</span>
                        +56 9 1234 5678
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
