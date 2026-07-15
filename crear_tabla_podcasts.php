<?php
require_once 'conexion.php';

$query = "CREATE TABLE IF NOT EXISTS podcasts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    spotify_url VARCHAR(255) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    duracion VARCHAR(50),
    fecha_publicacion VARCHAR(50),
    iframe_codigo TEXT NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";

if ($conexion->query($query) === TRUE) {
    echo "Tabla podcasts creada exitosamente o ya existe.";
} else {
    echo "Error al crear la tabla: " . $conexion->error;
}
$conexion->close();
?>
