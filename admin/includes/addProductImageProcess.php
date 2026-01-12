<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'A') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$producto_id = intval($_POST['producto_id'] ?? 0);

if ($producto_id <= 0) {
    header("Location: ../layout.php?page=menu&error=producto");
    exit;
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    header("Location: /ProyectoIngWeb/admin/pages/product.php?id=$producto_id&error=imagen");
    exit;
}

$imagen = $_FILES['imagen'];

$tiposPermitidos = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

$tamanoMaximo = 2 * 1024 * 1024;

/* ===== MIME real ===== */
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeReal = finfo_file($finfo, $imagen['tmp_name']);
finfo_close($finfo);

if (!isset($tiposPermitidos[$mimeReal])) {
    header("Location: /ProyectoIngWeb/admin/pages/product.php?id=$producto_id&error=formato");
    exit;
}

if ($imagen['size'] > $tamanoMaximo) {
    header("Location: /ProyectoIngWeb/admin/pages/product.php?id=$producto_id&error=pesada");
    exit;
}

/* ===== Guardar archivo ===== */
$extension = $tiposPermitidos[$mimeReal];
$nombreArchivo = uniqid('prod_') . '.' . $extension;

$rutaServidor = $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/images/' . $nombreArchivo;
$rutaBD = '/ProyectoIngWeb/images/' . $nombreArchivo;

if (!move_uploaded_file($imagen['tmp_name'], $rutaServidor)) {
    header("Location: /ProyectoIngWeb/admin/pages/product.php?id=$producto_id&error=guardar");
    exit;
}

/* ===== Insertar en BD ===== */
$stmt = $conn->prepare("
    INSERT INTO imagenes_comida (producto_id, imagen)
    VALUES (?, ?)
");
$stmt->bind_param("is", $producto_id, $rutaBD);
$stmt->execute();
$stmt->close();

header("Location: /ProyectoIngWeb/admin/pages/product.php?id=$producto_id&success=imagen");
exit;