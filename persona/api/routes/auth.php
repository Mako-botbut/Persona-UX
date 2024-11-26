<?php
session_start(); // Para manejar sesiones

// Incluir el archivo de la clase Database
require_once "../config/database.php";

// Crear una instancia de la clase Database y obtener la conexión
$database = new Database();
$conn = $database->getConnection();  // Esta es la conexión que usarás para las consultas

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    if (!$email || !$password) {
        echo json_encode(["error" => "Email y contraseña son obligatorios."]);
        exit;
    }

    // Preparar la consulta
    $query = "SELECT id, password, rol FROM usuario WHERE email = :email";
    $stmt = $conn->prepare($query);
    
    // Vincular parámetros y ejecutar la consulta
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Almacenar datos en la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_rol'] = $user['rol'];
            echo json_encode(["success" => "Inicio de sesión exitoso."]);
        } else {
            echo json_encode(["error" => "Contraseña incorrecta."]);
        }
    } else {
        echo json_encode(["error" => "El usuario no existe."]);
    }
} else {
    echo json_encode(["error" => "Método no permitido."]);
}
?>