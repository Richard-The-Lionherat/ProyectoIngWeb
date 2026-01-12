<?php
header('Content-Type: application/json');

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

/* ================= VALIDAR ID ================= */
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode([
        "status" => -2,
        "error"  => "ID inválido"
    ]);
    exit;
}

$id = (int) $_POST['id'];

try {
    /* ================= LLAMAR SP ================= */
    $stmt = $conn->prepare("CALL sp_eliminarCategoria(?, @resultado)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    /* ================= OBTENER RESULTADO ================= */
    $resQuery = $conn->query("SELECT @resultado AS resultado");
    $row = $resQuery->fetch_assoc();

    echo json_encode([
        "status" => (int) $row["resultado"]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => -1,
        "error"  => $e->getMessage()
    ]);
}