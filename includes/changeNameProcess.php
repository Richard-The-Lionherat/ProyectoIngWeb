<?php
header('Content-Type: application/json');
session_start();

include "connection.php";

$email = $_POST['email'] ?? '';
$newName = $_POST['newName'] ?? '';

try {
    $stmt = $conn->prepare("CALL sp_cambiarNombre(? , ?)");
    if (!$stmt) {
        throw new Exception("Error al preparar la sentencia: " . $conn->error);
    }
    if (!$stmt->bind_param("ss", $email, $newName)) {
        throw new Exception("Error al ligar parámetros: " . $stmt->error);
    }
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar el procedimiento: " . $stmt->error);
    }
    
    echo json_encode([
        "status" => 0,
        "message" => "Nombre actualizado correctamente"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => -1,
        "error" => $e->getMessage()
    ]);
}
?>