<?php
header('Content-Type: application/json');
require 'conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        $response['message'] = 'ID de noticia inválido.';
        echo json_encode($response);
        exit;
    }

    // Primero obtener la ruta de la imagen para borrarla si existe
    $sql_img = "SELECT imagen_path FROM noticias WHERE id = $id";
    $result_img = $conexion->query($sql_img);
    if ($result_img && $row = $result_img->fetch_assoc()) {
        $img_path = $row['imagen_path'];
        if (!empty($img_path) && file_exists($img_path)) {
            unlink($img_path);
        }
    }

    $sql = "DELETE FROM noticias WHERE id = $id";
    
    if ($conexion->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'La noticia ha sido eliminada.';
    } else {
        $response['message'] = 'Error al eliminar: ' . $conexion->error;
    }
    
    $conexion->close();
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
?>
