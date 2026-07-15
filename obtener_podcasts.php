<?php
require 'conexion.php';
header('Content-Type: application/json');

$sql = "SELECT * FROM podcasts ORDER BY fecha_agregado DESC";
$result = $conexion->query($sql);

$podcasts = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $podcasts[] = $row;
    }
}

echo json_encode($podcasts);
$conexion->close();
?>
