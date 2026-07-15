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
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $tipo = (isset($_POST['tipo']) && $_POST['tipo'] === 'restaurante') ? 'restaurante' : 'turistico';
    $lat = isset($_POST['lat']) ? trim($_POST['lat']) : '';
    $lng = isset($_POST['lng']) ? trim($_POST['lng']) : '';
    $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $dato_curioso = isset($_POST['dato_curioso']) ? trim($_POST['dato_curioso']) : '';

    // Validación básica
    if (empty($nombre) || empty($descripcion) || $lat === '' || $lng === '' || !is_numeric($lat) || !is_numeric($lng)) {
        $response['message'] = 'Por favor, completa el nombre, la descripción y coordenadas válidas (latitud/longitud).';
        echo json_encode($response);
        exit;
    }

    $imagen_path = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $directorio_destino = 'img/';
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }
        $nombre_archivo = basename($_FILES['imagen']['name']);
        $nombre_archivo_limpio = preg_replace("/[^a-zA-Z0-9.]/", "_", $nombre_archivo);
        $nombre_final = time() . '_' . $nombre_archivo_limpio;
        $ruta_destino = $directorio_destino . $nombre_final;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            $imagen_path = $ruta_destino;
        } else {
            $response['message'] = 'Error al subir la imagen.';
            echo json_encode($response);
            exit;
        }
    }

    $stmt = $conexion->prepare("INSERT INTO destinos (nombre, tipo, lat, lng, direccion, descripcion, dato_curioso, imagen_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddssss", $nombre, $tipo, $lat, $lng, $direccion, $descripcion, $dato_curioso, $imagen_path);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'El destino se ha agregado correctamente.';
    } else {
        $response['message'] = 'Error al guardar en la base de datos: ' . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
?>
