<?php
header('Content-Type: application/json');
session_start();

include "connection.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

try {
    // Obtener hash y tipo del usuario
    $stmt = $conn->prepare("CALL sp_obtenerPasswordUsuario(?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    if (!$userData) {
        echo json_encode([
            "status" => 1, // usuario no encontrado
        ]);
        exit;
    }

    $hash = $userData['userWEB_password'];
    $tipo = $userData['userWEB_tipo'];
    $nombre = $userData['userWEB_nombre'];

    // Validar contraseña
    if (!password_verify($password, $hash)) {
        echo json_encode([
            "status" => 2, // contraseña incorrecta
        ]);
        exit;
    }

    // LOGIN CORRECTO — crear sesión
    $_SESSION['email'] = $email;
    $_SESSION['tipo'] = $tipo;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['password_plain'] = $password;

    echo json_encode([
        "status" => 0, // correcto
        "tipo"   => $tipo,
        "nombre" => $nombre
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => -1,
        "error" => $e->getMessage()
    ]);
}
?>