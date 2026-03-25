<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../conexion.php";

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

        $conn->begin_transaction();

        try {

            // 1️⃣ CREAR USUARIO
            $password = password_hash("123456", PASSWORD_BCRYPT);

            $sqlUsuario = "INSERT INTO usuarios 
            (nombre, apellido_p, apellido_m, nombre_usuario, correo, password, id_rol)
            VALUES 
            ('$data[nombre]', '$data[apellido_p]', '$data[apellido_m]',
             '$data[correo]', '$data[correo]', '$password', 1)";

            $conn->query($sqlUsuario);
            $id_usuario = $conn->insert_id;

            // 2️⃣ CREAR PACIENTE
            $sqlPaciente = "INSERT INTO pacientes 
            (id_usuario, fecha_nacimiento, telefono, direccion, genero)
            VALUES 
            ($id_usuario, '$data[fecha_nac]', '$data[telefono]', 'N/A', 'N/A')";

            $conn->query($sqlPaciente);
            $id_paciente = $conn->insert_id;

            // 3️⃣ CREAR EXPEDIENTE
            $sqlExp = "INSERT INTO expedientes 
            (id_paciente, alergias, lesiones_previas)
            VALUES 
            ($id_paciente, '$data[alergias]', '$data[lesiones]')";

            $conn->query($sqlExp);

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

    $id_paciente = $data['id_paciente'];

    $conn->begin_transaction();

    try {

        // obtener id_usuario
        $res = $conn->query("SELECT id_usuario FROM pacientes WHERE id_paciente=$id_paciente");
        $row = $res->fetch_assoc();
        $id_usuario = $row['id_usuario'];

        // 🔹 actualizar usuario
        $sqlUsuario = "UPDATE usuarios SET
            nombre='$data[nombre]',
            apellido_p='$data[apellido_p]',
            apellido_m='$data[apellido_m]',
            correo='$data[correo]'
            WHERE id_usuario=$id_usuario";

        $conn->query($sqlUsuario);

        // 🔹 actualizar paciente
        $sqlPaciente = "UPDATE pacientes SET
            telefono='$data[telefono]',
            fecha_nacimiento='$data[fecha_nac]'
            WHERE id_paciente=$id_paciente";

        $conn->query($sqlPaciente);

        // 🔹 actualizar expediente
        $sqlExp = "UPDATE expedientes SET
            alergias='$data[alergias]'
            WHERE id_paciente=$id_paciente";

        $conn->query($sqlExp);

        $conn->commit();

        echo json_encode(["status" => "success"]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    break;
}