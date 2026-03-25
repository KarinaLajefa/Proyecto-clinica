<?php
header("Content-Type: application/json");
require 'config/conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        try {
            // Unimos sesiones con tratamientos, pacientes y usuarios para mostrar el nombre del paciente
            $sql = "SELECT s.id_sesion, s.fecha, s.hora, s.estado, s.observaciones, t.tipo_terapia, u.nombre, u.apellido_p 
                    FROM sesiones s 
                    INNER JOIN tratamientos t ON s.id_tratamiento = t.id_tratamiento
                    INNER JOIN pacientes p ON t.id_paciente = p.id_paciente
                    INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                    ORDER BY s.fecha DESC, s.hora DESC";
            $stmt = $conn->query($sql);
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        try {
            $sql = "INSERT INTO sesiones (id_tratamiento, id_fisio, fecha, hora, estado, observaciones) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            // Nota: En una app final, id_tratamiento e id_fisio vendrían de un <select>
            $stmt->execute([$data->id_tratamiento, $data->id_fisio, $data->fecha, $data->hora, $data->estado, $data->observaciones]);
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