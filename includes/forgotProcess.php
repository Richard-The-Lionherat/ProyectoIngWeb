<?php
header('Content-Type: application/json');

include "connection.php";

$email = $_POST['email'] ?? '';

try {
    // 1. Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT userWEB_emailID FROM usuariosWEB WHERE userWEB_emailID = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode([
            "status" => 1,
            "error" => "Este correo no está registrado."
        ]);
        exit;
    }

    // 2. Generar contraseña temporal
    $tempPass = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&"), 0, 10);

    // 3. Convertir a hash
    $hash = password_hash($tempPass, PASSWORD_DEFAULT);

    // 4. Actualizar BD
    $stmt2 = $conn->prepare("UPDATE usuariosWEB SET userWEB_password = ? WHERE userWEB_emailID = ?");
    $stmt2->bind_param("ss", $hash, $email);
    $stmt2->execute();

    echo json_encode([
        "status" => 0,
        "tempPass" => $tempPass
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => -1,
        "error" => "Error en el servidor"
    ]);
}
?>