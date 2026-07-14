<?php require 'verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activa El Turismo | Gestor de Videos</title>
    <link rel="stylesheet" href="css/styles.css?v=2">
    <style>
        .news-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .news-table th, .news-table td { padding: 12px 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .dark-mode .news-table th, .dark-mode .news-table td { border-bottom: 1px solid #444; }
        .action-btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 5px; transition: background-color 0.2s; }
        .btn-edit { background-color: #f59e0b; color: white; }
        .btn-edit:hover { background-color: #d97706; }
        .btn-delete { background-color: #ef4444; color: white; }
        .btn-delete:hover { background-color: #dc2626; }
        #edit-modal { display: none; margin-top: 30px; }
        .gestor-container { max-width: 1000px; margin: 0 auto; }
    </style>
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
            <h1>Gestor de Videos</h1>
            <p>Edita o elimina los videos publicados en el portal.</p>
        </section>
        
        <section class="panel-form-section">
            <div style="display: flex; gap: 1rem; justify-content: center; margin-bottom: 1.5rem;">
                <a href="panel.php" class="news-btn">Volver al Panel</a>
            </div>
            
            <div class="panel-form-card gestor-container">
                <h2>Videos Publicados</h2>
                <div id="gestor-news-list" style="overflow-x: auto;">
                    <table class="news-table">
                        <thead>
                            <tr>
                                <th>Fecha Pub.</th>
                                <th>Título</th>
                                <th>Duración</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="gestor-videos-tbody">
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 20px;">Cargando videos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Form Modal (Hidden by default) -->
            <div class="panel-form-card gestor-container" id="edit-modal">
                <h2>Editar Video</h2>
                <form class="article-form-prototype" id="edit-video-form" action="editar_video.php" method="POST">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-grid">
                        <label class="form-field">
                            <span>Fecha de Publicación</span>
                            <input type="date" name="fecha_publicacion" id="edit-fecha" required>
                        </label>
                        <label class="form-field">
                            <span>Duración</span>
                            <input type="text" name="duracion" id="edit-duracion" placeholder="Ej: 05:30" required>
                        </label>
                        <label class="form-field form-field-full">
                            <span>Título</span>
                            <input type="text" name="titulo" id="edit-titulo" placeholder="Título del video" required>
                        </label>
                        <label class="form-field form-field-full">
                            <span>Descripción</span>
                            <textarea name="descripcion" id="edit-descripcion" rows="4" placeholder="Descripción del video"></textarea>
                        </label>
                    </div>

                    <div class="form-actions" style="margin-top: 20px; display: flex; gap: 10px;">
                        <button type="submit" class="form-action-btn primary">Guardar Cambios</button>
                        <button type="button" class="form-action-btn" id="btn-cancel-edit" style="background-color: var(--text-muted); color: white;">Cancelar</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <div id="toast-container" class="toast-container"></div>
    <script src="js/main.js?v=2"></script>
    <script src="js/gestor_videos.js?v=2"></script>
</body>
</html>
