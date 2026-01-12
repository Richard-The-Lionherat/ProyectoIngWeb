<?php
header('Content-Type: application/json');
session_start();
include "connection.php";

$email = $_POST['email'] ?? '';

try {
    // No permitir que un admin se borre a sí mismo
    if ($email === $_SESSION['email']) {
        echo json_encode(["status" => 2, "error" => "No puedes eliminar tu propio usuario."]);
        exit;
    }

    $stmt = $conn->prepare("CALL sp_eliminarUsuario(?, @resultado)");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resQuery = $conn->query("SELECT @resultado AS resultado");
    $row = $resQuery->fetch_assoc();

    echo json_encode(["status" => intval($row["resultado"])]);

} catch (Exception $e) {
    echo json_encode([
        "status" => -1,
        "error" => $e->getMessage()
    ]);
}
?>