<?php
header("Content-Type: application/json; charset=UTF-8");
include_once "../config/database.php";

$database = new Database();
$db = $database->getConnection();

// Obtener todos los usuarios activos
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare("SELECT u.id, u.nombre, u.edad, u.ocupacion, u.ubicacion, 
        dp.metas, dp.motivaciones, dp.frustraciones, dp.contacto
        FROM usuario u
        LEFT JOIN detalles_persona dp ON u.id = dp.id_usuario
        WHERE u.estado = 'activo'"); // Filtrar solo usuarios activos
    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
}

// Agregar un nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Insertar en la tabla usuario
    $stmt = $db->prepare("INSERT INTO usuario (nombre, edad, ocupacion, ubicacion) 
        VALUES (:nombre, :edad, :ocupacion, :ubicacion)");
    $stmt->bindParam(":nombre", $data['nombre']);
    $stmt->bindParam(":edad", $data['edad']);
    $stmt->bindParam(":ocupacion", $data['ocupacion']);
    $stmt->bindParam(":ubicacion", $data['ubicacion']);

    if ($stmt->execute()) {
        $userId = $db->lastInsertId();

        // Insertar detalles en la tabla detalles_persona
        $stmtDetails = $db->prepare("INSERT INTO detalles_persona 
            (id_usuario, metas, motivaciones, frustraciones, contacto) 
            VALUES (:id_usuario, :metas, :motivaciones, :frustraciones, :contacto)");
        $stmtDetails->bindParam(":id_usuario", $userId);
        $stmtDetails->bindParam(":metas", $data['metas']);
        $stmtDetails->bindParam(":motivaciones", $data['motivaciones']);
        $stmtDetails->bindParam(":frustraciones", $data['frustraciones']);
        $stmtDetails->bindParam(":contacto", $data['contacto']);

        if ($stmtDetails->execute()) {
            echo json_encode(["message" => "Usuario agregado exitosamente."]);
        } else {
            echo json_encode(["message" => "Error al agregar detalles del usuario."]);
        }
    } else {
        echo json_encode(["message" => "Error al agregar el usuario."]);
    }
}

// Actualizar un usuario existente
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        isset($data['id']) && isset($data['nombre']) && isset($data['edad']) && 
        isset($data['ocupacion']) && isset($data['ubicacion']) &&
        isset($data['metas']) && isset($data['motivaciones']) && 
        isset($data['frustraciones']) && isset($data['contacto'])
    ) {
        $id = $data['id'];

        // Actualizar datos en la tabla usuario
        $stmt = $db->prepare("UPDATE usuario SET nombre = :nombre, edad = :edad, ocupacion = :ocupacion, ubicacion = :ubicacion WHERE id = :id");
        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":edad", $data['edad']);
        $stmt->bindParam(":ocupacion", $data['ocupacion']);
        $stmt->bindParam(":ubicacion", $data['ubicacion']);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            // Actualizar datos en la tabla detalles_persona
            $stmtDetails = $db->prepare("UPDATE detalles_persona SET metas = :metas, motivaciones = :motivaciones, frustraciones = :frustraciones, contacto = :contacto WHERE id_usuario = :id_usuario");
            $stmtDetails->bindParam(":metas", $data['metas']);
            $stmtDetails->bindParam(":motivaciones", $data['motivaciones']);
            $stmtDetails->bindParam(":frustraciones", $data['frustraciones']);
            $stmtDetails->bindParam(":contacto", $data['contacto']);
            $stmtDetails->bindParam(":id_usuario", $id);

            if ($stmtDetails->execute()) {
                echo json_encode(["success" => true, "message" => "Usuario actualizado correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar detalles del usuario."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar usuario."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Datos incompletos para actualizar."]);
    }
}

// Eliminar un usuario existente
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Leer el cuerpo de la solicitud como JSON
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if ($id) {
        // Preparar la consulta para marcar como inactivo
        $query = "UPDATE usuario SET estado = 'inactivo' WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Usuario marcado como inactivo"]);
        } else {
            echo json_encode(["error" => "Error al marcar usuario como inactivo"]);
        }
    } else {
        echo json_encode(["error" => "ID de usuario no proporcionado"]);
    }
}

// Reactivar un usuario existente
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    // Leer el cuerpo de la solicitud como JSON
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if ($id) {
        // Preparar la consulta para marcar como activo
        $query = "UPDATE usuario SET estado = 'activo' WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Usuario reactivado con éxito"]);
        } else {
            echo json_encode(["error" => "Error al reactivar el usuario"]);
        }
    } else {
        echo json_encode(["error" => "ID de usuario no proporcionado"]);
    }
}
?>