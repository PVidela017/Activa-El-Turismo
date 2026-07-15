<?php
header('Content-Type: application/json');
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conexion->prepare("SELECT id, nombre, tipo, lat, lng, direccion, descripcion, dato_curioso, imagen_path FROM destinos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode($resultado->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Destino no encontrado']);
    }
    $stmt->close();
} else {
    $where_clauses = array();
    $types = "";
    $params = array();

    if (isset($_GET['tipo']) && $_GET['tipo'] !== '') {
        $where_clauses[] = "tipo = ?";
        $types .= "s";
        $params[] = $_GET['tipo'];
    }

    $sql = "SELECT id, nombre, tipo, lat, lng, direccion, descripcion, dato_curioso, imagen_path FROM destinos";
    if (count($where_clauses) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    $sql .= " ORDER BY fecha_creacion DESC";

    $stmt = $conexion->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();

    $destinos = array();
    while ($fila = $resultado->fetch_assoc()) {
        $destinos[] = $fila;
    }
    echo json_encode($destinos);
    $stmt->close();
}

$conexion->close();
?>
