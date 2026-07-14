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

    if ($id <= 0) {
        $response['message'] = 'ID inválido.';
        echo json_encode($response);
        exit;
    }

    $sql = "DELETE FROM videos WHERE id = $id";

    if ($conexion->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Video eliminado correctamente.';
    } else {
        $response['message'] = 'Error al eliminar: ' . $conexion->error;
    }
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
$conexion->close();
?>
