<?php
$host = 'localhost';
$dbname = 'clinica_fisioterapia';
$username = 'root';
$password = 'admin1306'; // Aquí está tu contraseña exacta de la captura

try {
    // Creamos la conexión usando PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Le decimos a PDO que nos avise si hay errores
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    // Si falla la conexión, mostramos el error
    die(json_encode(["status" => "error", "message" => "Error DB: " . $e->getMessage()]));
}
?>