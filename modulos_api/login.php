<?php
session_start();
require 'db.php';
header("Content-Type: application/json");

// Recibimos los datos enviados desde el formulario (Fetch API)
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->correo) && !empty($data->password)) {
    try {
        // Buscamos al usuario por correo e incluimos su rol
        $sql = "SELECT u.*, r.nombre_rol 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                WHERE u.correo = ? AND u.estado = 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$data->correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificamos si el usuario existe y si la contraseña coincide (usando password_verify)
        if ($usuario && $data->password === $usuario['password']) {
            
            // ¡Login exitoso! Creamos las variables de sesión
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre_completo'] = $usuario['nombre'] . ' ' . $usuario['apellido_p'];
            $_SESSION['correo'] = $usuario['correo'];
            $_SESSION['rol'] = $usuario['nombre_rol'];
            
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Correo o contraseña incorrectos."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error del servidor: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Por favor, ingresa correo y contraseña."]);
}
?>