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
    
    // 1. Validar primero que vengan los datos obligatorios
    if (!$data || !isset($data->id_fisio) || !isset($data->hora) || !isset($data->fecha)) {
        echo json_encode([
            "status" => "error", 
            "message" => "Datos incompletos enviados al servidor"
        ]);
        exit;
    }

    // Definir las variables limpias desde el objeto $data
    $id_fisio = $data->id_fisio;
    $hora     = $data->hora;
    $fecha    = $data->fecha; 

    try {
        // 2. Obtener horario del fisioterapeuta
        $stmtHorario = $conn->prepare(
            "SELECT hora_inicio, hora_fin 
             FROM usuarios 
             WHERE id_usuario = ?"
         );

        $stmtHorario->execute([$id_fisio]);
        $horario = $stmtHorario->fetch(PDO::FETCH_ASSOC);

        // Verificar si existe el fisioterapeuta
        if (!$horario) {
            echo json_encode([
                "status" => "error", 
                "message" => "No se encontró el fisioterapeuta"
            ]);
            exit;
        }

        // Convertir horas para compararlas
        $horaSesion = strtotime($hora);
        $horaInicio = strtotime($horario['hora_inicio']);
        $horaFin    = strtotime($horario['hora_fin']);

        // 3. Validar si la sesión está dentro del horario laboral del fisio
        if ($horaSesion < $horaInicio || $horaSesion > $horaFin) {
            echo json_encode([
                "status" => "error", 
                "message" => "La sesión está fuera del horario del fisioterapeuta"
            ]);
            exit;
        } 

        // 4. Validar si la sesión ya está duplicada (mismo fisio, misma fecha, misma hora)
        $stmtDuplicado = $conn->prepare(
            "SELECT COUNT(*) FROM sesiones WHERE id_fisio = ? AND fecha = ? AND hora = ?"
        );
        $stmtDuplicado->execute([$id_fisio, $fecha, $hora]);

        if ($stmtDuplicado->fetchColumn() > 0) {
            echo json_encode([
                "status" => "error", 
                "message" => "Ya existe una sesión agendada para ese horario con este fisioterapeuta"
            ]);
            exit;
        }

        // 5. Si pasó todas las reglas anteriores, insertar la sesión
        $sql = "INSERT INTO sesiones (id_tratamiento, id_fisio, fecha, hora, estado, observaciones) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data->id_tratamiento, 
            $id_fisio, 
            $fecha, 
            $hora, 
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