<?php
header("Content-Type: application/json");
require '../modulos_api/db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        try {
            // 1. Obtener la agenda de sesiones
            $sqlAgenda = "SELECT s.id_sesion, s.fecha, s.hora, s.estado, s.observaciones, t.tipo_terapia, u.nombre, u.apellido_p 
                    FROM sesiones s 
                    INNER JOIN tratamientos t ON s.id_tratamiento = t.id_tratamiento
                    INNER JOIN pacientes p ON t.id_paciente = p.id_paciente
                    INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                    ORDER BY s.fecha DESC, s.hora DESC";
            $stmtAgenda = $conn->query($sqlAgenda);
            $agenda = $stmtAgenda->fetchAll(PDO::FETCH_ASSOC);

            // 2. Obtener lista de TRATAMIENTOS activos
            $sqlTrat = "SELECT t.id_tratamiento, t.tipo_terapia, u.nombre, u.apellido_p 
                        FROM tratamientos t
                        INNER JOIN pacientes p ON t.id_paciente = p.id_paciente
                        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario";
            $tratamientos = $conn->query($sqlTrat)->fetchAll(PDO::FETCH_ASSOC);

            // 3. Obtener lista de FISIOTERAPEUTAS (CORREGIDO)
            // Usamos 'f' para fisioterapeutas y unimos correctamente con usuarios
            $sqlFisios = "SELECT f.id_fisio, u.nombre
                          FROM fisioterapeutas f
                          INNER JOIN usuarios u ON f.id_usuario = u.id_usuario"; 
            
            $fisioterapeutas = $conn->query($sqlFisios)->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "data" => [
                    "agenda" => $agenda,
                    "catalogos" => [
                        "tratamientos" => $tratamientos,
                        "fisioterapeutas" => $fisioterapeutas
                    ]
                ]
            ]);
        } catch (Exception $e) {
            // Esto enviará el error real a la consola si algo falla
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        try {
            $sql = "INSERT INTO sesiones (id_tratamiento, id_fisio, fecha, hora, estado, observaciones) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $data->id_tratamiento, 
                $data->id_fisio, 
                $data->fecha, 
                $data->hora, 
                $data->estado, 
                $data->observaciones
            ]);
            echo json_encode(["status" => "success", "message" => "Sesión programada"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        try {
            $stmt = $conn->prepare("DELETE FROM sesiones WHERE id_sesion = ?");
            $stmt->execute([$data->id_sesion]);
            echo json_encode(["status" => "success"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;
}
?>