<?php
header('Content-Type: application/json');
include "connection.php";

$email = $_POST['email'];
$nombre = $_POST['nombre'];
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
$tipo = $_POST['tipo'];

try {
    $stmt = $conn->prepare("CALL sp_registrarUsuario(?, ?, ?, ?, @resultado)");
    if (!$stmt) {
        throw new Exception("Error al preparar la sentencia: " . $conn->error);
    }

    if (!$stmt->bind_param("ssss", $email, $nombre, $pass, $tipo)) {
        throw new Exception("Error al ligar parámetros: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar el procedimiento: " . $stmt->error);
    }

    $resQuery = $conn->query("SELECT @resultado AS resultado");
    if (!$resQuery) {
        throw new Exception("Error al leer resultado OUT: " . $conn->error);
    }

    $row = $resQuery->fetch_assoc();

    // Respuesta final
    echo json_encode([
        "status" => intval($row["resultado"])
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => -1,
        "error" => $e->getMessage()
    ]);
}
?>