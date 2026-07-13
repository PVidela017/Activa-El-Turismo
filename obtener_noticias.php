<?php
header('Content-Type: application/json');
require 'conexion.php';

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $sql = "SELECT id, fecha, titulo, subtitulo, autor, cuerpo, imagen_path, pie_imagen, id_cat, slug_not FROM noticias WHERE slug_not = ?";
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
    $sql = "SELECT id, fecha, titulo, subtitulo, autor, cuerpo, imagen_path, pie_imagen, id_cat, slug_not FROM noticias WHERE id = ?";
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
    $where_clauses = array();
    $types = "";
    $params = array();

    if (isset($_GET['cat']) && $_GET['cat'] !== '') {
        $where_clauses[] = "id_cat = ?";
        $types .= "i";
        $params[] = intval($_GET['cat']);
    }

    if (isset($_GET['search']) && trim($_GET['search']) !== '') {
        $search = '%' . trim($_GET['search']) . '%';
        $where_clauses[] = "(titulo LIKE ? OR cuerpo LIKE ?)";
        $types .= "ss";
        $params[] = $search;
        $params[] = $search;
    }

    $sql = "SELECT id, fecha, titulo, subtitulo, autor, cuerpo, imagen_path, pie_imagen, id_cat, slug_not FROM noticias";
    if (count($where_clauses) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    $sql .= " ORDER BY fecha DESC, fecha_creacion DESC";

    $stmt = $conexion->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $noticias = array();
        if ($resultado->num_rows > 0) {
            while($fila = $resultado->fetch_assoc()) {
                $noticias[] = $fila;
            }
        }
        echo json_encode($noticias);
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error al preparar la consulta de base de datos']);
    }
}

$conexion->close();
?>

