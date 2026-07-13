<?php
session_start();

// Si ya está logueado, redirigir al panel
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: panel.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'conexion.php';

    $correo   = trim($_POST['correo']   ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($correo) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        $stmt = $conexion->prepare(
            "SELECT id_usu, nombre_usu, password_usu FROM usuarios WHERE correo_usu = ? AND rol_usu = 'admin' LIMIT 1"
        );
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Soporta contraseña en texto plano Y hasheada con password_hash()
            $passwordValida = ($user['password_usu'] === $password)
                           || password_verify($password, $user['password_usu']);

            if ($passwordValida) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_nombre']    = $user['nombre_usu'];
                $_SESSION['admin_id']        = $user['id_usu'];
                header('Location: panel.php');
                exit;
            } else {
                $error = 'Contraseña incorrecta.';
            }
        } else {
            $error = 'Correo no encontrado o sin permisos de administrador.';
        }
        $stmt->close();
        $conexion->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activa El Turismo | Acceso Administrador</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <div class="logo-container">
                <a href="index.html" class="logo-link">
                    <img src="img/Logo ActivaelTurismo.png" alt="Activa el Turismo logo" class="site-logo">
                </a>
                <a href="index.html" class="logo-link site-title-link">Activa El Turismo <br> Difundiendo el alma de la Región de Antofagasta.</a>
            </div>
            <nav class="site-nav" aria-label="Main navigation">
                <a href="index.html" class="nav-link">Inicio</a>
                <a href="noticias.html" class="nav-link">Noticias</a>
                <a href="#" class="nav-link">Destinos</a>
                <a href="videos.html" class="nav-link">Videos</a>
                <a href="podcasts.html" class="nav-link">Podcasts</a>
                <a href="#" class="nav-link">Eventos</a>
                <a href="#site-footer" class="nav-link">Contacto</a>
                <button class="theme-toggle" id="theme-toggle" type="button" aria-label="Cambiar a modo oscuro">🌙</button>
            </nav>
        </div>
    </header>

    <main>
        <section class="login-section">
            <div class="login-card">
                <div class="login-card-header">
                    <h1 class="login-title">Acceso Administrador</h1>
                    <p class="login-subtitle">Ingresa tus credenciales para gestionar las noticias del portal.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="login-error" id="login-error-msg">
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form class="login-form" method="POST" action="login.php" id="login-form">
                    <label class="login-field">
                        <span class="login-label">Correo electrónico</span>
                        <div class="login-input-wrap">
                            <span class="login-input-icon">✉</span>
                            <input
                                type="email"
                                name="correo"
                                id="login-correo"
                                class="login-input"
                                placeholder="admin@ejemplo.cl"
                                value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                                required
                                autocomplete="email"
                            >
                        </div>
                    </label>

                    <label class="login-field">
                        <span class="login-label">Contraseña</span>
                        <div class="login-input-wrap">
                            <input
                                type="password"
                                name="password"
                                id="login-password"
                                class="login-input"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="login-toggle-pw" id="toggle-pw" aria-label="Mostrar contraseña">👁</button>
                        </div>
                    </label>

                    <button type="submit" class="login-submit-btn" id="login-submit">
                        Iniciar Sesión
                    </button>
                </form>

                <p class="login-back-link"><a href="index.html">← Volver al sitio</a></p>
            </div>
        </section>
    </main>

    <script src="js/main.js"></script>
    <script>
        // Toggle mostrar/ocultar contraseña
        const togglePw  = document.getElementById('toggle-pw');
        const pwInput   = document.getElementById('login-password');
        if (togglePw && pwInput) {
            togglePw.addEventListener('click', () => {
                const isText = pwInput.type === 'text';
                pwInput.type = isText ? 'password' : 'text';
                togglePw.textContent = isText ? '👁' ;
            });
        }
    </script>
</body>
</html>
