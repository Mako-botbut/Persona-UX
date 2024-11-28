<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_URI'] === '/api/usuarios') {
    include_once "./routes/usuario.php";
} else {
    echo json_encode(["message" => "Endpoint no encontrado."]);
}

?>
