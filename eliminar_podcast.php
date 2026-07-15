<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado.']);
    exit;
}
header('Content-Type: application/json');
require 'conexion.php';

$response = array('success' => false, 'message' => '');

// Se recibe el ID por POST o GET, preferiblemente POST
$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? intval($data['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($id <= 0) {
    $response['message'] = 'ID de podcast inválido.';
    echo json_encode($response);
    exit;
}

$sql = "DELETE FROM podcasts WHERE id = $id";

if ($conexion->query($sql) === TRUE) {
    $response['success'] = true;
    $response['message'] = 'Podcast eliminado correctamente.';
} else {
    $response['message'] = 'Error al eliminar: ' . $conexion->error;
}

echo json_encode($response);
$conexion->close();
?>
