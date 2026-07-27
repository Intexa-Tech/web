<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/enviar_correo.php';

date_default_timezone_set('America/Costa_Rica');

$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json; charset=utf-8');

/*
|--------------------------------------------------------------------------
| Solo aceptar POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Método no permitido."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Obtener datos
|--------------------------------------------------------------------------
*/

$nombre = trim($_POST['name'] ?? '');
$correo = trim($_POST['email'] ?? '');
$asunto = trim($_POST['subject'] ?? '');
$mensaje = trim($_POST['message'] ?? '');

/*
|--------------------------------------------------------------------------
| Validaciones
|--------------------------------------------------------------------------
*/

if (
    empty($nombre) ||
    empty($correo) ||
    empty($mensaje)
) {

    echo json_encode([
        "success" => false,
        "message" => "Complete todos los campos obligatorios."
    ]);

    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "El correo electrónico no es válido."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Guardar en la base de datos
|--------------------------------------------------------------------------
*/

$sql = "INSERT INTO contacto
(
    nombre,
    correo,
    asunto,
    mensaje
)
VALUES
(
    ?,
    ?,
    ?,
    ?
)";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Error preparando la consulta."
    ]);

    exit;
}

$stmt->bind_param(
    "ssss",
    $nombre,
    $correo,
    $asunto,
    $mensaje
);

if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "message" => "No fue posible guardar la información."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Enviar correos
|--------------------------------------------------------------------------
*/

$correoCliente = enviarCorreoCliente(
    $nombre,
    $correo
);

$correoAdmin = enviarCorreoAdmin(
    $nombre,
    $correo,
    $asunto,
    $mensaje
);

/*
|--------------------------------------------------------------------------
| Respuesta
|--------------------------------------------------------------------------
*/

if ($correoCliente && $correoAdmin) {

    echo json_encode([
        "success" => true,
        "message" => "La información fue enviada correctamente."
    ]);

} elseif ($correoCliente) {

    echo json_encode([
        "success" => true,
        "message" => "Información guardada. El correo al administrador no pudo enviarse."
    ]);

} elseif ($correoAdmin) {

    echo json_encode([
        "success" => true,
        "message" => "Información guardada. El correo al cliente no pudo enviarse."
    ]);

} else {

    echo json_encode([
        "success" => true,
        "message" => "Información guardada correctamente, pero no fue posible enviar los correos."
    ]);

}

$stmt->close();
$conn->close();