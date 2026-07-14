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
    $url = isset($_POST['youtube_url']) ? trim($_POST['youtube_url']) : '';

    if (empty($url)) {
        $response['message'] = 'Por favor, ingresa un enlace válido.';
        echo json_encode($response);
        exit;
    }

    // Extraer ID del video
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
    $video_id = isset($match[1]) ? $match[1] : '';

    if (empty($video_id)) {
        $response['message'] = 'No se pudo extraer el ID del video. Verifica el enlace.';
        echo json_encode($response);
        exit;
    }

    $iframe_codigo = '<iframe width="100%" height="320" src="https://www.youtube.com/embed/' . $video_id . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>';

    // Obtener info básica vía oEmbed (muy fiable)
    $oembed_url = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=" . $video_id . "&format=json";
    $oembed_json = @file_get_contents($oembed_url);
    $oembed_data = $oembed_json ? json_decode($oembed_json, true) : [];
    
    $titulo = isset($oembed_data['title']) ? $oembed_data['title'] : 'Video sin título';
    $descripcion = '';
    $duracion = '00:00';
    $fecha_publicacion = date('Y-m-d'); // Fecha actual por defecto

    // Intentar obtener más detalles desde el HTML de YouTube usando CURL
    $ch = curl_init("https://www.youtube.com/watch?v=" . $video_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html) {
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $tags = $doc->getElementsByTagName('meta');
        
        foreach ($tags as $tag) {
            if ($tag->getAttribute('name') === 'title' && empty($oembed_data['title'])) {
                $titulo = $tag->getAttribute('content');
            }
            if ($tag->getAttribute('name') === 'description') {
                $descripcion = $tag->getAttribute('content');
            }
            if ($tag->getAttribute('itemprop') === 'duration') {
                $dur_raw = $tag->getAttribute('content'); // Ejemplo: PT39M10S
                try {
                    $di = new DateInterval($dur_raw);
                    $formato = $di->h > 0 ? '%h:%I:%S' : '%I:%S';
                    $duracion = $di->format($formato);
                } catch (Exception $e) {
                    $duracion = '00:00';
                }
            }
            if ($tag->getAttribute('itemprop') === 'datePublished') {
                $fecha_publicacion = $tag->getAttribute('content');
            }
        }
    }

    $titulo_db = $conexion->real_escape_string($titulo);
    $descripcion_db = $conexion->real_escape_string($descripcion);
    $duracion_db = $conexion->real_escape_string($duracion);
    $fecha_db = $conexion->real_escape_string($fecha_publicacion);
    $iframe_db = $conexion->real_escape_string($iframe_codigo);

    $sql = "INSERT INTO videos (youtube_url, titulo, descripcion, duracion, fecha_publicacion, iframe_codigo) 
            VALUES ('$url', '$titulo_db', '$descripcion_db', '$duracion_db', '$fecha_db', '$iframe_db')";

    if ($conexion->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Video agregado correctamente.';
    } else {
        $response['message'] = 'Error de base de datos: ' . $conexion->error;
    }
}

echo json_encode($response);
$conexion->close();
?>
