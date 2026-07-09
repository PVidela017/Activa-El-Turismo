<?php
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "activa_el_turismo";

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("La conexión ha fallado: " . $conexion->connect_error);
}

// Establecer el conjunto de caracteres a utf8
$conexion->set_charset("utf8");
?>
