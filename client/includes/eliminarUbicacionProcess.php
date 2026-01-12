<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    echo json_encode([
        "status" => -1,
        "error" => "No autorizado"
    ]);
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$email = $_SESSION['email'];
$ubiId = intval($_POST['ubi_id'] ?? 0);

if ($ubiId <= 0) {
    echo json_encode([
        "status" => 1,
        "error" => "ID inválido"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare(
        "CALL sp_eliminarUbicacion(?, ?)"
    );

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("si", $email, $ubiId);
    $stmt->execute();

    echo json_encode(["status" => 0]);

} catch (Exception $e) {
    echo json_encode([
        "status" => -2,
        "error" => $e->getMessage()
    ]);
}