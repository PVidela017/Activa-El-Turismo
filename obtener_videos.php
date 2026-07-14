<?php
header('Content-Type: application/json');
require 'conexion.php';

$sql = "SELECT id, youtube_url, titulo, descripcion, duracion, fecha_publicacion, iframe_codigo, fecha_agregado FROM videos ORDER BY fecha_agregado DESC";
$resultado = $conexion->query($sql);

$videos = array();

if ($resultado && $resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $videos[] = $fila;
    }
}

echo json_encode($videos);
$conexion->close();
?>
