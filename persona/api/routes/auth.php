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
    try {
        $query = "SELECT id, password, rol FROM usuario WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
    
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_rol'] = $user['rol'];
                echo json_encode(["success" => "Inicio de sesión exitoso."]);
            } else {
                http_response_code(401); // Código de no autorizado
                echo json_encode(["error" => "Contraseña incorrecta."]);
            }
        } else {
            http_response_code(404); // Código de no encontrado
            echo json_encode(["error" => "El usuario no existe."]);
        }
    } catch (PDOException $e) {
        http_response_code(500); // Código de error interno del servidor
        echo json_encode(["error" => "Error en el servidor: " . $e->getMessage()]);
    }
}
?>