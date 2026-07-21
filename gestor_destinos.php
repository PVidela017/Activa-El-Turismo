<?php require 'verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activa El Turismo | Gestor de Destinos</title>
    <link rel="stylesheet" href="css/styles.css?v=2">
    <style>
        .news-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .news-table th, .news-table td { padding: 12px 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .dark-mode .news-table th, .dark-mode .news-table td { border-bottom: 1px solid #444; }
        .action-btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 5px; transition: background-color 0.2s; }
        .btn-delete { background-color: #ef4444; color: white; }
        .btn-delete:hover { background-color: #dc2626; }
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
            <h1>Gestor de Destinos</h1>
            <p>Elimina los destinos publicados en el mapa.</p>
        </section>

        <section class="panel-form-section">
            <div style="display: flex; gap: 1rem; justify-content: center; margin-bottom: 1.5rem;">
                <a href="panel.php" class="news-btn">Volver al Panel</a>
                <a href="destinos.html" class="news-btn">Ver Destinos</a>
            </div>

            <div class="panel-form-card gestor-container">
                <h2>Destinos publicados</h2>
                <div id="gestor-destinos-list" style="overflow-x: auto;">
                    <table class="news-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="gestor-destinos-tbody">
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 20px;">Cargando destinos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <div id="toast-container" class="toast-container"></div>
    <script src="js/main.js?v=2"></script>
    <script src="js/destinos.js?v=2"></script>
</body>
</html>
