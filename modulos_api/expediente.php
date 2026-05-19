<?php
// modulos_api/expedientes.php
require 'db.php';
header("Content-Type: application/json");

$metodo = $_SERVER['REQUEST_METHOD'];


if ($metodo == 'GET') {
    try {
        //Traemos los datos de la tabla expedientes
        $sql = "SELECT e.*, u.nombre, u.apellido_p, p.telefono FROM expedientes e
                LEFT JOIN pacientes p ON e.id_paciente = p.id_paciente
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                ORDER BY e.id_expediente DESC";

        $stmt = $conn->query($sql);
        $expedientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(["status" => "success", "data" => $expedientes]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al cargar: " . $e->getMessage()]);
    }
}

elseif ($metodo == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id_paciente)) {
        try {
            $sql = "INSERT INTO expedientes (id_paciente, antecedentes, alergias, lesiones_previas, notas_generales) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$data->id_paciente, $data->antecedentes, $data->alergias, $data->lesiones_previas, $data->notas_generales]);
            
            echo json_encode(["status" => "success", "message" => "Expediente guardado correctamente."]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error al guardar: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "El nombre y teléfono son obligatorios."]);
    }
}
?>