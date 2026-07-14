<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado.']);
    exit;
}

header('Content-Type: application/json');
require 'conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $fecha = isset($_POST['fecha_publicacion']) ? trim($_POST['fecha_publicacion']) : '';
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $duracion = isset($_POST['duracion']) ? trim($_POST['duracion']) : '';

    if ($id <= 0 || empty($titulo) || empty($fecha) || empty($duracion)) {
        $response['message'] = 'Faltan campos obligatorios.';
        echo json_encode($response);
        exit;
    }

    $titulo_db = $conexion->real_escape_string($titulo);
    $descripcion_db = $conexion->real_escape_string($descripcion);
    $fecha_db = $conexion->real_escape_string($fecha);
    $duracion_db = $conexion->real_escape_string($duracion);

    $sql = "UPDATE videos SET 
                titulo = '$titulo_db', 
                descripcion = '$descripcion_db', 
                fecha_publicacion = '$fecha_db', 
                duracion = '$duracion_db' 
            WHERE id = $id";

    if ($conexion->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Video actualizado correctamente.';
    } else {
        $response['message'] = 'Error al actualizar: ' . $conexion->error;
    }
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
$conexion->close();
?>
