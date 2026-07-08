<?php

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/helpers/response.php";

$method = $_SERVER["REQUEST_METHOD"];

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri = trim($uri, "/");

if ($uri === "") {
    response("success", "API de Gestión Escolar funcionando", ["version" => "1.0"]);
}

// GET /alumnos
if ($uri === "alumnos" && $method === "GET") {
    $sql = "SELECT id, nombre, matricula, carrera FROM alumnos";
    $resultado = $conn->query($sql);

    if (!$resultado) {
        response("error", "Error al consultar los alumnos", null, 500);
    }

    $alumnos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $alumnos[] = $fila;
    }

    response("success", "Alumnos obtenidos correctamente", $alumnos, 200);
}

// GET /alumnos/{id}
if (preg_match('#^alumnos/(\d+)$#', $uri, $matches) && $method === "GET") {
    $id = (int) $matches[1];

    $sql = "SELECT id, nombre, matricula, carrera FROM alumnos WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        response("error", "Error al preparar la consulta", null, 500);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        response("error", "El alumno con el ID especificado no existe", null, 404);
    }

    $alumno = $resultado->fetch_assoc();

    response("success", "Alumno obtenido correctamente", $alumno, 200);
}

// POST /alumnos
if ($uri === "alumnos" && $method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (
        empty($input["nombre"]) ||
        empty($input["matricula"]) ||
        empty($input["carrera"])
    ) {
        response("error", "Todos los campos son obligatorios", null, 400);
    }

    $nombre = $conn->real_escape_string($input["nombre"]);
    $matricula = $conn->real_escape_string($input["matricula"]);
    $carrera = $conn->real_escape_string($input["carrera"]);

    $sql = "INSERT INTO alumnos (nombre, matricula, carrera)
            VALUES ('$nombre', '$matricula', '$carrera')";

    if ($conn->query($sql)) {
        response(
            "success",
            "Alumno registrado correctamente",
            [
                "id" => $conn->insert_id,
                "nombre" => $nombre,
                "matricula" => $matricula,
                "carrera" => $carrera
            ],
            201
        );
    }

    if ($conn->errno == 1062) {
        response("error", "La matrícula ya se encuentra registrada", null, 400);
    }

    response("error", "Error al registrar alumno", null, 500);
}

// GET /materias
if ($uri === "materias" && $method === "GET") {
    $sql = "SELECT id, nombre FROM materias";
    $resultado = $conn->query($sql);

    if (!$resultado) {
        response("error", "Error al procesar la solicitud de materias", null, 400);
    }

    $materias = [];

    while ($fila = $resultado->fetch_assoc()) {
        $materias[] = $fila;
    }

    response("success", "Materias obtenidas correctamente", $materias, 200);
}

// GET /alumnos/{id}/info
if (preg_match('#^alumnos/(\d+)/info$#', $uri, $matches) && $method === "GET") {
    $id = (int) $matches[1];

    $sql = "SELECT id, nombre, matricula, carrera FROM alumnos WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        response("error", "Error al preparar la consulta", null, 500);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        response("error", "El alumno con el ID especificado no existe", null, 404);
    }

    $alumno = $resultado->fetch_assoc();

    // API pública: Numbers API
$apiUrl = "https://httpbin.org/json";
    $apiResponse = @file_get_contents($apiUrl);

    if ($apiResponse === false) {
        response("error", "No fue posible consultar la API pública", null, 500);
    }

    $datosApi = json_decode($apiResponse, true);

    if (!$datosApi || !isset($datosApi["slideshow"]["title"])) {
    response("error", "La API pública no devolvió datos válidos", null, 500);
}

$apiPublica = [
    "api" => "HTTPBin",
    "titulo" => $datosApi["slideshow"]["title"],
    "autor" => $datosApi["slideshow"]["author"] ?? "No disponible"
];

    response(
        "success",
        "Datos internos y externos obtenidos correctamente",
        [
            "alumno" => $alumno,
            "api_publica" => $apiPublica
        ],
        200
    );
}

// PUT /calificaciones
if ($uri === "calificaciones" && $method === "PUT") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (
        empty($input["alumno_id"]) ||
        empty($input["materia_id"]) ||
        !isset($input["calificacion"])
    ) {
        response("error", "Faltan campos obligatorios", null, 400);
    }

    $alumno_id = (int) $input["alumno_id"];
    $materia_id = (int) $input["materia_id"];
    $calificacion = (float) $input["calificacion"];

    if ($calificacion < 0 || $calificacion > 10) {
        response("error", "La calificación debe estar entre 0 y 10", null, 400);
    }

    $sqlAlumno = "SELECT id FROM alumnos WHERE id = ?";
    $stmtAlumno = $conn->prepare($sqlAlumno);
    $stmtAlumno->bind_param("i", $alumno_id);
    $stmtAlumno->execute();
    $resAlumno = $stmtAlumno->get_result();

    if ($resAlumno->num_rows === 0) {
        response("error", "El ID del alumno no coincide con nuestros registros", null, 404);
    }

    $sqlMateria = "SELECT id FROM materias WHERE id = ?";
    $stmtMateria = $conn->prepare($sqlMateria);
    $stmtMateria->bind_param("i", $materia_id);
    $stmtMateria->execute();
    $resMateria = $stmtMateria->get_result();

    if ($resMateria->num_rows === 0) {
        response("error", "El ID de la materia no coincide con nuestros registros", null, 404);
    }

    $sqlBuscar = "SELECT id FROM calificaciones WHERE alumno_id = ? AND materia_id = ?";
    $stmtBuscar = $conn->prepare($sqlBuscar);
    $stmtBuscar->bind_param("ii", $alumno_id, $materia_id);
    $stmtBuscar->execute();
    $resBuscar = $stmtBuscar->get_result();

    if ($resBuscar->num_rows > 0) {
        $sql = "UPDATE calificaciones 
                SET calificacion = ? 
                WHERE alumno_id = ? AND materia_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dii", $calificacion, $alumno_id, $materia_id);
    } else {
        $sql = "INSERT INTO calificaciones (alumno_id, materia_id, calificacion)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iid", $alumno_id, $materia_id, $calificacion);
    }

    if ($stmt->execute()) {
        response(
            "success",
            "Calificación actualizada correctamente",
            [
                "alumno_id" => $alumno_id,
                "materia_id" => $materia_id,
                "nueva_calificacion" => $calificacion
            ],
            200
        );
    }

    response("error", "Error al actualizar la calificación", null, 500);
}

// Ruta no encontrada
response("error", "Endpoint no encontrado", null, 404);