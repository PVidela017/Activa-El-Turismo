<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado. Por favor inicia sesión.']);
    exit;
}
header('Content-Type: application/json');
require 'conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        $response['message'] = 'ID de destino inválido.';
        echo json_encode($response);
        exit;
    }

    $sql_img = "SELECT imagen_path FROM destinos WHERE id = $id";
    $result_img = $conexion->query($sql_img);
    if ($result_img && $row = $result_img->fetch_assoc()) {
        $img_path = $row['imagen_path'];
        if (!empty($img_path) && file_exists($img_path)) {
            unlink($img_path);
        }
    }

    $sql = "DELETE FROM destinos WHERE id = $id";

    if ($conexion->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'El destino ha sido eliminado.';
    } else {
        $response['message'] = 'Error al eliminar: ' . $conexion->error;
    }

    $conexion->close();
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
?>
