<?php
header("Content-Type: application/json");
require 'db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        try {
            // Unimos pagos con pacientes, usuarios y métodos de pago
            $sql = "SELECT p.id_pago, p.monto, p.fecha_pago, p.estado, m.nombre_metodo, u.nombre, u.apellido_p 
                    FROM pagos p 
                    INNER JOIN pacientes pac ON p.id_paciente = pac.id_paciente
                    INNER JOIN usuarios u ON pac.id_usuario = u.id_usuario
                    INNER JOIN metodos_pago m ON p.id_metodo = m.id_metodo
                    ORDER BY p.fecha_pago DESC, p.id_pago DESC";
            $stmt = $conn->query($sql);
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
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
            $stmt->execute([$data->id_paciente, $data->id_tratamiento, $data->id_metodo, $data->monto, $data->fecha_pago, $data->estado]);
            echo json_encode(["status" => "success", "message" => "Pago registrado"]);
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