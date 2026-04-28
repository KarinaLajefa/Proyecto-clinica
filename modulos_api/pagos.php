<?php
header("Content-Type: application/json");
require 'db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        try {
            // 1. Obtenemos el historial de pagos para la tabla inferior
            $sqlPagos = "SELECT 
                        p.id_pago, p.monto, p.fecha_pago, p.estado, 
                        p.id_paciente, p.id_tratamiento, p.id_metodo,
                        m.nombre_metodo, u.nombre, u.apellido_p 
                    FROM pagos p 
                    INNER JOIN pacientes pac ON p.id_paciente = pac.id_paciente
                    INNER JOIN usuarios u ON pac.id_usuario = u.id_usuario
                    INNER JOIN metodos_pago m ON p.id_metodo = m.id_metodo
                    ORDER BY p.fecha_pago DESC";
            
            $stmtPagos = $conn->query($sqlPagos);
            $pagos = $stmtPagos->fetchAll(PDO::FETCH_ASSOC);

            // 2. Obtenemos los Catálogos para llenar los SELECTS del formulario
            // Pacientes
            $pacientes = $conn->query("SELECT p.id_paciente, u.nombre, u.apellido_p FROM pacientes p INNER JOIN usuarios u ON p.id_usuario = u.id_usuario")->fetchAll(PDO::FETCH_ASSOC);
            
            // Tratamientos
            $tratamientos = $conn->query("SELECT id_tratamiento, tipo_terapia FROM tratamientos")->fetchAll(PDO::FETCH_ASSOC);
            
            // Métodos de Pago
            $metodos = $conn->query("SELECT id_metodo, nombre_metodo FROM metodos_pago")->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "data" => [
                    "historial" => $pagos,
                    "catalogos" => [
                        "pacientes" => $pacientes,
                        "tratamientos" => $tratamientos,
                        "metodos" => $metodos
                    ]
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        try {
            $sql = "INSERT INTO pagos (id_paciente, id_tratamiento, id_metodo, monto, fecha_pago, estado) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $data->id_paciente, 
                $data->id_tratamiento, 
                $data->id_metodo, 
                $data->monto, 
                $data->fecha_pago, 
                $data->estado
            ]);
            echo json_encode(["status" => "success", "message" => "Pago registrado"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        try {
            // Actualizado a PDO (Eliminamos bind_param para evitar errores de compatibilidad)
            $sql = "UPDATE pagos SET 
                    id_paciente = ?, id_tratamiento = ?, id_metodo = ?, 
                    monto = ?, fecha_pago = ?, estado = ? 
                    WHERE id_pago = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $data->id_paciente, 
                $data->id_tratamiento, 
                $data->id_metodo, 
                $data->monto, 
                $data->fecha_pago, 
                $data->estado, 
                $data->id_pago
            ]);
            echo json_encode(["status" => "success", "message" => "Pago actualizado"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        try {
            $stmt = $conn->prepare("DELETE FROM pagos WHERE id_pago = ?");
            $stmt->execute([$data->id_pago]);
            echo json_encode(["status" => "success"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;
}
?>