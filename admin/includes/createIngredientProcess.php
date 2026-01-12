<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$nombre = $_POST['nombre'];
$medida = $_POST['medida'];
$cantidad = floatval($_POST['cantidad']);

try {
    $stmt = $conn->prepare("CALL sp_agregarIngrediente(?, ?, ?, @resultado)");
    if (!$stmt) {
        throw new Exception("Error al preparar la sentencia: " . $conn->error);
    }

    if (!$stmt->bind_param("ssd", $nombre, $medida, $cantidad)) {
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