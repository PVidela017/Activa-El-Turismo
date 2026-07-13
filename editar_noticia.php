<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado. Por favor inicia sesión.']);
    exit;
}
header('Content-Type: application/json');
require 'conexion.php';

function generarSlug($cadena) {
    $cadena = mb_strtolower($cadena, 'UTF-8');
    $cadena = str_replace(
        array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'),
        array('a', 'e', 'i', 'o', 'u', 'n', 'u'),
        $cadena
    );
    $cadena = preg_replace('/[^a-z0-9]+/', '-', $cadena);
    $cadena = trim($cadena, '-');
    return $cadena;
}

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        $response['message'] = 'ID de noticia inválido.';
        echo json_encode($response);
        exit;
    }

    $fecha = isset($_POST['fecha']) ? $conexion->real_escape_string($_POST['fecha']) : '';
    $titulo = isset($_POST['titulo']) ? $conexion->real_escape_string($_POST['titulo']) : '';
    $subtitulo = isset($_POST['subtitulo']) ? $conexion->real_escape_string($_POST['subtitulo']) : '';
    $autor = isset($_POST['autor']) ? $conexion->real_escape_string($_POST['autor']) : '';
    $cuerpo = isset($_POST['cuerpo']) ? $conexion->real_escape_string($_POST['cuerpo']) : '';
    $pie_imagen = isset($_POST['pieImagen']) ? $conexion->real_escape_string($_POST['pieImagen']) : '';

    if (empty($fecha) || empty($titulo) || empty($autor) || empty($cuerpo)) {
        $response['message'] = 'Por favor, completa todos los campos requeridos.';
        echo json_encode($response);
        exit;
    }

    $slug_not = generarSlug($titulo);
    // En caso de que se haya mantenido el título, para evitar errores de duplicidad del UNIQUE slug_not,
    // podríamos verificar si el slug ya existe en otro ID y concatenarle un sufijo (o el ID), pero
    // por simplicidad añadiremos el ID al slug si hay conflicto.
    
    $sql_check_slug = "SELECT id FROM noticias WHERE slug_not = '$slug_not' AND id != $id";
    $res_check = $conexion->query($sql_check_slug);
    if ($res_check && $res_check->num_rows > 0) {
        $slug_not = $slug_not . '-' . $id;
    }
    
    // Obtener imagen actual por si se sube una nueva
    $sql_current = "SELECT imagen_path FROM noticias WHERE id = $id";
    $result_current = $conexion->query($sql_current);
    $current_img_path = '';
    if ($result_current && $row = $result_current->fetch_assoc()) {
        $current_img_path = $row['imagen_path'];
    }
    
    $imagen_path = $current_img_path; // Default a la actual

    // Manejo de nueva imagen si se subió
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
            // Eliminar imagen vieja
            if (!empty($current_img_path) && file_exists($current_img_path)) {
                unlink($current_img_path);
            }
        } else {
            $response['message'] = 'Error al subir la nueva imagen.';
            echo json_encode($response);
            exit;
        }
    }

    $sql = "UPDATE noticias SET 
                fecha = '$fecha',
                titulo = '$titulo',
                subtitulo = '$subtitulo',
                autor = '$autor',
                cuerpo = '$cuerpo',
                imagen_path = '$imagen_path',
                pie_imagen = '$pie_imagen',
                slug_not = '$slug_not'
            WHERE id = $id";

    if ($conexion->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'La noticia se ha actualizado correctamente.';
    } else {
        $response['message'] = 'Error al actualizar: ' . $conexion->error;
    }
    
    $conexion->close();
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
?>
