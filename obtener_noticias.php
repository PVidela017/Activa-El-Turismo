<?php
header('Content-Type: application/json');
require 'conexion.php';

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $sql = "SELECT id, fecha, titulo, subtitulo, autor, cuerpo, imagen_path, pie_imagen, slug_not FROM noticias WHERE slug_not = ?";
    $stmt = $conexion->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            echo json_encode($resultado->fetch_assoc());
        } else {
            echo json_encode(['error' => 'Noticia no encontrada']);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error al preparar la consulta de base de datos']);
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT id, fecha, titulo, subtitulo, autor, cuerpo, imagen_path, pie_imagen, slug_not FROM noticias WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            echo json_encode($resultado->fetch_assoc());
        } else {
            echo json_encode(['error' => 'Noticia no encontrada']);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error al preparar la consulta de base de datos']);
    }
} else {
    $sql = "SELECT id, fecha, titulo, subtitulo, autor, cuerpo, imagen_path, pie_imagen, slug_not FROM noticias ORDER BY fecha DESC, fecha_creacion DESC";
    $resultado = $conexion->query($sql);
    
    $noticias = array();
    
    if ($resultado->num_rows > 0) {
        while($fila = $resultado->fetch_assoc()) {
            $noticias[] = $fila;
        }
    }
    
    echo json_encode($noticias);
}

$conexion->close();
?>

