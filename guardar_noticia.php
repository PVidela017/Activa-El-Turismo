<?php
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
    $fecha = isset($_POST['fecha']) ? $conexion->real_escape_string($_POST['fecha']) : '';
    $titulo = isset($_POST['titulo']) ? $conexion->real_escape_string($_POST['titulo']) : '';
    $subtitulo = isset($_POST['subtitulo']) ? $conexion->real_escape_string($_POST['subtitulo']) : '';
    $autor = isset($_POST['autor']) ? $conexion->real_escape_string($_POST['autor']) : '';
    $cuerpo = isset($_POST['cuerpo']) ? $conexion->real_escape_string($_POST['cuerpo']) : '';
    $pie_imagen = isset($_POST['pieImagen']) ? $conexion->real_escape_string($_POST['pieImagen']) : '';
    
    $imagen_path = '';

    // Manejo de la subida de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $directorio_destino = 'img/';
        
        // Crear directorio si no existe (no debería pasar, pero por si acaso)
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        $nombre_archivo = basename($_FILES['imagen']['name']);
        // Limpiar el nombre de archivo de caracteres extraños y añadir un timestamp para evitar sobreescritura
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

    // Validación básica
    if (empty($fecha) || empty($titulo) || empty($autor) || empty($cuerpo)) {
        $response['message'] = 'Por favor, completa todos los campos requeridos (Fecha, Título, Autor, Cuerpo).';
        echo json_encode($response);
        exit;
    }

    $id_cat = isset($_POST['id_cat']) ? intval($_POST['id_cat']) : 1; // Por defecto a 'Sin categoría'
    $slug_not = generarSlug($titulo); // Función para generar slug

    // Insertar en la base de datos
    $sql = "INSERT INTO noticias (fecha, titulo, subtitulo, autor, cuerpo, imagen_path, pie_imagen, id_cat, slug_not) 
            VALUES ('$fecha', '$titulo', '$subtitulo', '$autor', '$cuerpo', '$imagen_path', '$pie_imagen', $id_cat, '$slug_not')";

    if ($conexion->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'La noticia se ha publicado correctamente.';
    } else {
        $response['message'] = 'Error al guardar en la base de datos: ' . $conexion->error;
    }
    
    $conexion->close();
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
?>
