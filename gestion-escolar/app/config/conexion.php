<?php

$host = "mysql";
$user = "root";
$password = "root";
$database = "escuela";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error de conexión a la base de datos"
    ]);
    exit;
}

$conn->set_charset("utf8");
?>