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
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $duracion = isset($_POST['duracion']) ? trim($_POST['duracion']) : '';
    $fecha_publicacion = isset($_POST['fecha_publicacion']) ? trim($_POST['fecha_publicacion']) : '';

    if ($id <= 0 || empty($titulo)) {
        $response['message'] = 'Datos inválidos.';
        echo json_encode($response);
        exit;
    }

    $titulo_db = $conexion->real_escape_string($titulo);
    $descripcion_db = $conexion->real_escape_string($descripcion);
    $duracion_db = $conexion->real_escape_string($duracion);
    $fecha_db = $conexion->real_escape_string($fecha_publicacion);

    $sql = "UPDATE podcasts SET 
                titulo = '$titulo_db', 
                descripcion = '$descripcion_db', 
                duracion = '$duracion_db', 
                fecha_publicacion = '$fecha_db' 
            WHERE id = $id";

    if ($conexion->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Podcast actualizado correctamente.';
    } else {
        $response['message'] = 'Error al actualizar: ' . $conexion->error;
    }
}

echo json_encode($response);
$conexion->close();
?>
