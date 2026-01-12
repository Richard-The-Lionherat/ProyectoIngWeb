<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'A') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$id            = intval($_POST['id'] ?? 0);
$nombre        = trim($_POST['nombre'] ?? '');
$categoria_id  = intval($_POST['categoria_id'] ?? 0);
$descripcion   = trim($_POST['descripcion'] ?? '');
$precio        = floatval($_POST['precio'] ?? 0);
$unidad        = trim($_POST['unidad_medida'] ?? '');
$cantidad      = floatval($_POST['cantidad'] ?? -1);

if ($id <= 0) {
    header("Location: ../pages/product.php?id=$id&error=id");
    exit;
}

if ($nombre === '' || $categoria_id <= 0 || $precio <= 0) {
    header("Location: ../pages/product.php?id=$id&error=datos");
    exit;
}

if ($unidad === '' || $cantidad < 0) {
    header("Location: ../pages/product.php?id=$id&error=stock");
    exit;
}

$stmt = $conn->prepare("
    CALL sp_cambiarProducto(
        ?, ?, ?, ?, ?, ?, ?, @resultado
    )
");

$stmt->bind_param(
    "iissdsi",
    $id,
    $categoria_id,
    $nombre,
    $descripcion,
    $precio,
    $unidad,
    $cantidad
);

$stmt->execute();
$stmt->close();

$res = $conn->query("SELECT @resultado AS resultado");
$row = $res->fetch_assoc();

if ($row['resultado'] == 0) {
    header("Location: ../pages/product.php?id=$id&success=1");
} else {
    header("Location: ../pages/product.php?id=$id&error=bd");
}
exit;