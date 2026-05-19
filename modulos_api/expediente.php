<?php
// modulos_api/expedientes.php
require 'db.php';
header("Content-Type: application/json");

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'GET') {
    try {
        // 🚨 CONSULTA CORREGIDA: Sin la coma flotante y pidiendo los campos clave
        $sql = "SELECT e.id_expediente, e.id_paciente, e.antecedentes, e.alergias, e.lesiones_previas, e.notas_generales, 
                       u.nombre, u.apellido_p, p.telefono 
                FROM expedientes e
                LEFT JOIN pacientes p ON e.id_paciente = p.id_paciente
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                ORDER BY e.id_expediente DESC";

        // Adaptado 100% a la sintaxis MySQLi de tu proyecto
        $result = $conn->query($sql);
        $expedientes = [];

        while ($row = $result->fetch_assoc()) {
            $expedientes[] = $row;
        }
        
        echo json_encode(["status" => "success", "data" => $expedientes]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al cargar: " . $e->getMessage()]);
    }
}

elseif ($metodo == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id_paciente)) {
        try {
            $sql = "INSERT INTO expedientes (id_paciente, antecedentes, alergias, lesiones_previas, notas_generales) 
                    VALUES (?, ?, ?, ?, ?)";
            
            // Adaptado a consultas preparadas seguras de MySQLi (bind_param)
            $stmt = $conn->prepare($sql);
            
            // Ajustamos los valores por si vienen vacíos desde el frontend
            $id_paciente       = intval($data->id_paciente);
            $antecedentes      = $data->antecedentes ?? '';
            $alergias          = $data->alergias ?? '';
            $lesiones_previas  = $data->lesiones_previas ?? '';
            $notas_generales   = $data->notas_generales ?? '';

            $stmt->bind_param("issss", $id_paciente, $antecedentes, $alergias, $lesiones_previas, $notas_generales);
            $stmt->execute();
            $stmt->close();
            
            echo json_encode(["status" => "success", "message" => "Expediente guardado correctamente."]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error al guardar: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "El ID del paciente es obligatorio."]);
    }
}
?>