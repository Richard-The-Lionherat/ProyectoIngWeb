<?php
header('Content-Type: application/json');
session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

/* ================= VALIDAR ADMIN ================= */
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'A') {
    echo json_encode(["status" => -1, "error" => "No autorizado"]);
    exit;
}

/* ================= VALIDAR ID ================= */
$id = intval($_POST['categoria_id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["status" => 1, "error" => "Categoría inválida"]);
    exit;
}

/* ================= VALIDAR IMAGEN ================= */
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => 2, "error" => "Imagen no válida"]);
    exit;
}

$imagen = $_FILES['imagen'];

/* ================= VALIDAR EXTENSIÓN ================= */
$extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
$extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));

if (!in_array($extension, $extPermitidas)) {
    echo json_encode(["status" => 3, "error" => "Formato de imagen no permitido"]);
    exit;
}

/* ================= VALIDAR TAMAÑO (2MB) ================= */
$maxSize = 2 * 1024 * 1024; // 2MB
if ($imagen['size'] > $maxSize) {
    echo json_encode(["status" => 4, "error" => "La imagen supera el tamaño permitido"]);
    exit;
}

/* ================= GUARDAR IMAGEN ================= */
$nombreArchivo = uniqid("cat_", true) . "." . $extension;
$rutaServidor = $_SERVER['DOCUMENT_ROOT'] . "/ProyectoIngWeb/images/" . $nombreArchivo;
$rutaBD = "/ProyectoIngWeb/images/" . $nombreArchivo;

if (!move_uploaded_file($imagen['tmp_name'], $rutaServidor)) {
    echo json_encode(["status" => 5, "error" => "No se pudo guardar la imagen"]);
    exit;
}

/* ================= ACTUALIZAR BD ================= */
$stmt = $conn->prepare("CALL sp_cambiarImagenCategoria(?, ?, @resultado)");
$stmt->bind_param("is", $id, $rutaBD);
$stmt->execute();

$res = $conn->query("SELECT @resultado AS resultado")->fetch_assoc();

if ($res['resultado'] != 0) {
    echo json_encode(["status" => 6, "error" => "No se pudo actualizar la categoría"]);
    exit;
}

/* ================= ÉXITO ================= */
echo json_encode([
    "status" => 0,
    "imagen" => $rutaBD
]);