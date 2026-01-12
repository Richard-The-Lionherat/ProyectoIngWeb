<?php
header('Content-Type: application/json');
session_start();

include "connection.php";

$email = $_POST['email'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$tipo = $_POST['tipo'] ?? '';

try {
    // Cambiar nombre
    $stmt1 = $conn->prepare("CALL sp_cambiarNombre(?, ?)");
    $stmt1->bind_param("ss", $email, $nombre);
    $stmt1->execute();

    // Cambiar tipo
    $stmt2 = $conn->prepare("CALL sp_cambiarTipo(?, ?)");
    $stmt2->bind_param("ss", $email, $tipo);
    $stmt2->execute();

    echo json_encode(["status" => 0]);

} catch (Exception $e) {
    echo json_encode([
        "status" => -1,
        "error"  => $e->getMessage()
    ]);
}
?>