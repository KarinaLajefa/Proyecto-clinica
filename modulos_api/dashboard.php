<?php
require_once "../config/conexion.php";

header("Content-Type: application/json");

$data = [];

$data['pacientes'] = $conn->query("SELECT COUNT(*) total FROM pacientes")
->fetch_assoc()['total'];

$data['sesiones'] = $conn->query("SELECT COUNT(*) total FROM sesiones")
->fetch_assoc()['total'];

$data['ingresos'] = $conn->query("SELECT IFNULL(SUM(monto),0) total FROM pagos")
->fetch_assoc()['total'];

$data['tratamientos'] = $conn->query("SELECT COUNT(*) total FROM tratamientos")
->fetch_assoc()['total'];

echo json_encode($data);