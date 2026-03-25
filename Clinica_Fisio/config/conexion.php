<?php
// api/conexion.php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "admin1306";      // pon tu contraseña MySQL
$DB_NAME = "clinica_fisioterapia";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["ok"=>false,"msg"=>"Error DB: ".$conn->connect_error]));
}
$conn->set_charset("utf8mb4");
?>

   