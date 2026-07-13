<?php
header('Content-Type: application/json');
require 'conexion.php';

$sql = "SELECT id_cat, nombre_cat FROM categorias ORDER BY nombre_cat ASC";
$resultado = $conexion->query($sql);

$categorias = array();
if ($resultado && $resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $categorias[] = $fila;
    }
}
echo json_encode($categorias);
$conexion->close();
?>
