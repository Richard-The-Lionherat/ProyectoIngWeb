<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'A') {
    echo json_encode(["status" => -1, "error" => "No autorizado"]);
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$imagen_id = intval($_POST['imagen_id'] ?? 0);

if ($imagen_id <= 0) {
    echo json_encode(["status" => 1, "error" => "Imagen inválida"]);
    exit;
}

/* ===== Obtener ruta antes de borrar ===== */
$stmtImg = $conn->prepare("
    SELECT imagen 
    FROM imagenes_comida 
    WHERE id = ?
");
$stmtImg->bind_param("i", $imagen_id);
$stmtImg->execute();
$resImg = $stmtImg->get_result();
$img = $resImg->fetch_assoc();
$stmtImg->close();

if (!$img) {
    echo json_encode(["status" => 1, "error" => "Imagen no encontrada"]);
    exit;
}

$rutaBD = $img['imagen'];
$rutaServidor = $_SERVER['DOCUMENT_ROOT'] . $rutaBD;

/* ===== Llamar SP ===== */
$stmt = $conn->prepare("
    CALL sp_eliminarImagenProducto(?, @resultado)
");
$stmt->bind_param("i", $imagen_id);
$stmt->execute();
$stmt->close();

$res = $conn->query("SELECT @resultado AS resultado");
$row = $res->fetch_assoc();

switch ($row['resultado']) {
    case 0:
        // Borrar archivo físico (si existe)
        if (file_exists($rutaServidor)) {
            unlink($rutaServidor);
        }

        echo json_encode(["status" => 0]);
        break;

    case 2:
        echo json_encode([
            "status" => 2,
            "error" => "El producto debe tener al menos una imagen"
        ]);
        break;

    default:
        echo json_encode([
            "status" => 1,
            "error" => "No se pudo eliminar la imagen"
        ]);
}