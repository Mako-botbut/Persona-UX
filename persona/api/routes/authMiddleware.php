<?php
// Evitar el acceso directo al archivo
if (!defined('INCLUDE_AUTH')) {
    die('Acceso no permitido');
}

session_start(); // Asegúrate de que la sesión esté activa

function requireRole($role) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "No has iniciado sesión."]);
        http_response_code(401);
        exit;
    }

    if ($_SESSION['user_rol'] !== $role) {
        echo json_encode(["error" => "No tienes permiso para realizar esta acción."]);
        http_response_code(403);
        exit;
    }
}
