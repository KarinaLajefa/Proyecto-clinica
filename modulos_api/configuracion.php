<?php
require 'db.php'; 
header("Content-Type: application/json");

$metodo = $_SERVER['REQUEST_METHOD'];

// OBTENER USUARIOS
if ($metodo === 'GET') {
    try {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_p, r.nombre_rol 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                WHERE u.estado = 1";
        $stmt = $conn->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

// CREAR USUARIO
if ($metodo === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    try {
        $conn->beginTransaction();
        // Se incluye apellido_m como cadena vacía para evitar el error SQLSTATE[HY000]
        $sqlU = "INSERT INTO usuarios (nombre, apellido_p, apellido_m, correo, password, id_rol, estado) 
                 VALUES (?, ?, '', ?, ?, ?, 1)";
        $stmtU = $conn->prepare($sqlU);
        $stmtU->execute([$data->nombre, $data->apellido, $data->correo, $data->password, $data->id_rol]);
        
        $id = $conn->lastInsertId();
        if ($data->id_rol == 2) { // Fisio
            $conn->prepare("INSERT INTO fisioterapeutas (id_usuario, especialidad) VALUES (?, 'General')")->execute([$id]);
        } else if ($data->id_rol == 3) { // Recepcionista
            $conn->prepare("INSERT INTO recepcionista (id_usuario) VALUES (?)")->execute([$id]);
        }

        $conn->commit();
        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}