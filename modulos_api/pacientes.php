<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../config/conexion.php";

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // 🔹 OBTENER PACIENTES
    case 'GET':
        $sql = "SELECT 
                    p.id_paciente,
                    u.nombre,
                    u.apellido_p,
                    u.apellido_m,
                    u.correo,
                    p.telefono,
                    e.alergias
                FROM pacientes p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN expedientes e ON p.id_paciente = e.id_paciente";

        $result = $conn->query($sql);

        echo json_encode([
            "status" => "success",
            "data" => $result->fetch_all(MYSQLI_ASSOC)
        ]);
        break;

    // 🔹 INSERTAR
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validar que los datos mínimos existan en el arreglo
        if (!$data || !isset($data['correo']) || !isset($data['nombre'])) {
            echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
            exit;
        }

        $correo = $data['correo'];

        // VALIDAR CORREO DUPLICADO 
        $stmtCorreo = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = ?");
        $stmtCorreo->bind_param("s", $correo);
        $stmtCorreo->execute();
        $resCorreo = $stmtCorreo->get_result();
        $count = $resCorreo->fetch_row()[0];
        $stmtCorreo->close();

        if ($count > 0) {
            echo json_encode([
                "status" => "error", // Cambiado a 'status' para mantener tu consistencia
                "message" => "El correo ya está registrado"
            ]);
            exit;
        }

        $nombre    = $data['nombre'] ?? '';
        $apellido  = $data['apellido_p'] ?? '';
        $fecha_nac = $data['fecha_nac'] ?? '';

        // 🚨 VALIDAR SI YA EXISTE UN PACIENTE CON EL MISMO NOMBRE, APELLIDO Y FECHA DE NACIMIENTO
        // Unimos 'usuarios' (u) con 'pacientes' (p) usando 'id_usuario'
        $stmtPacienteDuplicado = $conn->prepare(
            "SELECT COUNT(*) 
             FROM usuarios u 
             INNER JOIN pacientes p ON u.id_usuario = p.id_usuario 
             WHERE u.nombre = ? AND u.apellido_p = ? AND p.fecha_nacimiento = ?"
        );
        
        $stmtPacienteDuplicado->bind_param("sss", $nombre, $apellido, $fecha_nac);
        $stmtPacienteDuplicado->execute();
        $resDuplicado = $stmtPacienteDuplicado->get_result();
        $existePaciente = $resDuplicado->fetch_row()[0];
        $stmtPacienteDuplicado->close();

        if ($existePaciente > 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Atención: Ya existe un paciente registrado con este mismo nombre y fecha de nacimiento."
            ]);
            exit;
        }

        $numero_expediente = 'EXP-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // Iniciar transacción de forma segura
        $conn->begin_transaction();

        try {
            // 1️⃣ CREAR USUARIO (Consultas preparadas para evitar Inyección SQL)
            $password = password_hash("123456", PASSWORD_BCRYPT);
            $id_rol = 1;

            $sqlUsuario = "INSERT INTO usuarios (nombre, apellido_p, apellido_m, nombre_usuario, correo, password, id_rol) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtUser = $conn->prepare($sqlUsuario);
            $stmtUser->bind_param("ssssssi", $data['nombre'], $data['apellido_p'], $data['apellido_m'], $correo, $correo, $password, $id_rol);
            $stmtUser->execute();
            
            $id_usuario = $conn->insert_id;
            $stmtUser->close();

            // 2️⃣ CREAR PACIENTE
            $direccion_defecto = 'Guadalajara';
            $genero_defecto = 'N/A';

            $sqlPaciente = "INSERT INTO pacientes (id_usuario, fecha_nacimiento, telefono, direccion, genero) VALUES (?, ?, ?, ?, ?, )";
            $stmtPac = $conn->prepare($sqlPaciente);
            $stmtPac->bind_param("issss", $id_usuario, $data['fecha_nac'], $data['telefono'], $direccion_defecto, $genero_defecto);
            $stmtPac->execute();

            
            $id_paciente = $conn->insert_id;

            $stmtPac->close();

            // 3️⃣ CREAR EXPEDIENTE
            $sqlExp = "INSERT INTO expedientes (id_paciente, alergias, lesiones_previas) VALUES (?, ?, ?)";
            $stmtExp = $conn->prepare($sqlExp);
            $stmtExp->bind_param("iss", $id_paciente, $data['alergias'], $data['lesiones']);
            $stmtExp->execute();
            $stmtExp->close();

            // Guardar cambios si todo salió bien
            $conn->commit();
            echo json_encode(["status" => "success"]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    // 🔹 ELIMINAR
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id_paciente'];

        // obtenemos id_usuario
        $res = $conn->query("SELECT id_usuario FROM pacientes WHERE id_paciente=$id");
        $row = $res->fetch_assoc();
        $id_usuario = $row['id_usuario'];

        // borrar usuario (borra todo por cascade)
        $conn->query("DELETE FROM usuarios WHERE id_usuario=$id_usuario");

        echo json_encode(["status" => "success"]);
        break;


        // 🔹 ACTUALIZAR
case 'PUT':

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id_paciente'])) {
        echo json_encode(["status"=>"error","message"=>"ID requerido"]);
        exit;
    }

    $id_paciente = $data['id_paciente'];

    $conn->begin_transaction();

    try {

        // obtener id_usuario
        $res = $conn->query("SELECT id_usuario FROM pacientes WHERE id_paciente=$id_paciente");
        $row = $res->fetch_assoc();
        $id_usuario = $row['id_usuario'];

        // actualizar usuario
        $conn->query("UPDATE usuarios SET
            nombre='{$data['nombre']}',
            apellido_p='{$data['apellido_p']}',
            apellido_m='{$data['apellido_m']}',
            correo='{$data['correo']}'
            WHERE id_usuario=$id_usuario");

        // actualizar paciente
        $conn->query("UPDATE pacientes SET
            telefono='{$data['telefono']}',
            fecha_nacimiento='{$data['fecha_nac']}'
            WHERE id_paciente=$id_paciente");

        // actualizar expediente
        $conn->query("UPDATE expedientes SET
            alergias='{$data['alergias']}'
            WHERE id_paciente=$id_paciente");

        $conn->commit();

        echo json_encode(["status"=>"success"]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
    }

    break;
}