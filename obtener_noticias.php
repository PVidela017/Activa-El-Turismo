<?php
header('Content-Type: application/json');
require 'conexion.php';

$sql = "SELECT id, fecha, titulo, subtitulo, autor, cuerpo, imagen_path, pie_imagen FROM noticias ORDER BY fecha DESC, fecha_creacion DESC";
$resultado = $conexion->query($sql);

$noticias = array();

if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $noticias[] = $fila;
    }
}

echo json_encode($noticias);

$conexion->close();
?>
