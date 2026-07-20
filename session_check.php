<?php
session_start();
header('Content-Type: application/json');

$loggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

echo json_encode([
    'loggedIn' => $loggedIn,
    'nombre'   => $loggedIn ? $_SESSION['admin_nombre'] : null,
]);
