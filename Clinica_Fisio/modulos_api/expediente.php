<?php
// modulos_api/expedientes.php
require 'db.php';
header("Content-Type: application/json");

$metodo = $_SERVER['REQUEST_METHOD'];

// CASO 1: Si piden la lista de expedientes (GET)
if ($metodo == 'GET') {
    try {
        $sql = "SELECT * FROM expedientes ORDER BY id_paciente DESC";
        $stmt = $conn->query($sql);
        $expedientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(["status" => "success", "data" => $expedientes]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al cargar: " . $e->getMessage()]);
    }
}

// CASO 2: Si envían un nuevo expediente para guardar (POST)
elseif ($metodo == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->nombre_completo) && !empty($data->telefono)) {
        try {
            $sql = "INSERT INTO expedientes (nombre_completo, telefono, motivo) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$data->nombre_completo, $data->telefono, $data->motivo]);
            
            echo json_encode(["status" => "success", "message" => "Expediente guardado correctamente."]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error al guardar: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "El nombre y teléfono son obligatorios."]);
    }
}
?>