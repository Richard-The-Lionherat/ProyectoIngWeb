<?php
header('Content-Type: application/json');
include "connection.php";

$email = $_POST['email'] ?? '';
$current = $_POST['current'] ?? '';
$newPass = $_POST['newPass'] ?? '';

try {
    // 1. Obtener hash actual
    $stmt = $conn->prepare("SELECT userWEB_password FROM usuariosWEB WHERE userWEB_emailID = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(["status" => 1, "error" => "Usuario no encontrado."]);
        exit;
    }

    $row = $res->fetch_assoc();
    $hashActual = $row["userWEB_password"];

    // 2. Validar contraseña actual
    if (!password_verify($current, $hashActual)) {
        echo json_encode(["status" => 2, "error" => "La contraseña actual es incorrecta."]);
        exit;
    }

    // 3. Crear nuevo hash
    $nuevoHash = password_hash($newPass, PASSWORD_DEFAULT);

    // 4. Actualizar BD
    $stmt2 = $conn->prepare("UPDATE usuariosWEB SET userWEB_password = ? WHERE userWEB_emailID = ?");
    $stmt2->bind_param("ss", $nuevoHash, $email);
    $stmt2->execute();

    echo json_encode(["status" => 0]);

} catch (Exception $e) {
    echo json_encode(["status" => -1, "error" => "Error interno."]);
}
?>