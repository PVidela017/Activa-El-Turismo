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
    $url = isset($_POST['spotify_url']) ? trim($_POST['spotify_url']) : '';

    if (empty($url)) {
        $response['message'] = 'Por favor, ingresa un enlace válido.';
        echo json_encode($response);
        exit;
    }

    // Extraer ID del podcast
    $podcast_id = '';
    if (preg_match('/open\.spotify\.com\/episode\/([a-zA-Z0-9]+)/i', $url, $match)) {
        $podcast_id = $match[1];
    } else {
        $response['message'] = 'No se pudo extraer el ID del episodio. Asegúrate de que sea un enlace válido de Spotify.';
        echo json_encode($response);
        exit;
    }

    $iframe_codigo = '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/episode/' . $podcast_id . '?utm_source=generator" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>';

    // Valores por defecto
    $titulo = 'Podcast sin título';
    $descripcion = '';
    $duracion = '00:00';
    $fecha_publicacion = date('Y-m-d');

    // Intentar obtener más detalles desde el HTML de Spotify usando CURL
    $ch = curl_init("https://open.spotify.com/episode/" . $podcast_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html) {
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $tags = $doc->getElementsByTagName('meta');
        
        foreach ($tags as $tag) {
            $prop = $tag->getAttribute('property');
            $name = $tag->getAttribute('name');
            $content = $tag->getAttribute('content');

            if ($prop === 'og:title' && !empty($content)) {
                $titulo = $content;
            }
            if ($name === 'description' && !empty($content)) {
                // Spotify añade un prefijo que a veces no es deseado, lo podemos dejar o limpiar.
                // Ejemplo: "Listen to this episode from Activa El Turismo on Spotify. En este último..."
                $clean_desc = preg_replace('/Listen to this episode from .* on Spotify\.\s*/i', '', $content);
                $descripcion = $clean_desc;
            }
            if ($prop === 'music:duration' && !empty($content)) {
                $segundos = intval($content);
                $horas = floor($segundos / 3600);
                $mins = floor(($segundos % 3600) / 60);
                $secs = $segundos % 60;
                
                if ($horas > 0) {
                    $duracion = sprintf("%d:%02d:%02d", $horas, $mins, $secs);
                } else {
                    $duracion = sprintf("%02d:%02d", $mins, $secs);
                }
            }
            if ($prop === 'music:release_date' && !empty($content)) {
                // Formato ISO 8601, ej: 2022-12-19T06:17:00Z -> 2022-12-19
                $fecha_publicacion = substr($content, 0, 10);
            }
        }
    }

    $titulo_db = $conexion->real_escape_string($titulo);
    $descripcion_db = $conexion->real_escape_string($descripcion);
    $duracion_db = $conexion->real_escape_string($duracion);
    $fecha_db = $conexion->real_escape_string($fecha_publicacion);
    $iframe_db = $conexion->real_escape_string($iframe_codigo);

    $sql = "INSERT INTO podcasts (spotify_url, titulo, descripcion, duracion, fecha_publicacion, iframe_codigo) 
            VALUES ('$url', '$titulo_db', '$descripcion_db', '$duracion_db', '$fecha_db', '$iframe_db')";

    if ($conexion->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Podcast agregado correctamente.';
    } else {
        $response['message'] = 'Error de base de datos: ' . $conexion->error;
    }
}

echo json_encode($response);
$conexion->close();
?>
