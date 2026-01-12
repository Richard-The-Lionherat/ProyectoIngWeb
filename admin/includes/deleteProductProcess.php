<?php
header('Content-Type: application/json');
session_start();

/* ===== VALIDAR SESIÓN ===== */
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'A') {
    echo json_encode([
        "status" => -1,
        "error" => "No autorizado"
    ]);
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

/* ===== VALIDAR ID ===== */
$producto_id = intval($_POST['id'] ?? 0);

if ($producto_id <= 0) {
    echo json_encode([
        "status" => 1,
        "error" => "Producto inválido"
    ]);
    exit;
}

/* ===== OBTENER IMÁGENES ===== */
$imagenes = [];

$stmtImg = $conn->prepare("
    SELECT imagen
    FROM imagenes_comida
    WHERE producto_id = ?
");
$stmtImg->bind_param("i", $producto_id);
$stmtImg->execute();
$resImg = $stmtImg->get_result();

while ($row = $resImg->fetch_assoc()) {
    $imagenes[] = $row['imagen'];
}

$stmtImg->close();

/* ===== ELIMINAR ARCHIVOS ===== */
foreach ($imagenes as $ruta) {
    $rutaFisica = $_SERVER['DOCUMENT_ROOT'] . $ruta;

    if (file_exists($rutaFisica)) {
        unlink($rutaFisica);
    }
}

/* ===== ELIMINAR PRODUCTO (SP) ===== */
$stmt = $conn->prepare("
    CALL sp_eliminarProducto(?, @resultado)
");
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$stmt->close();

/* ===== RESULTADO ===== */
$res = $conn->query("SELECT @resultado AS resultado");
$row = $res->fetch_assoc();

if ($row['resultado'] == 0) {
    echo json_encode(["status" => 0]);
} else {
    echo json_encode([
        "status" => 2,
        "error" => "El producto no existe"
    ]);
}

exit;