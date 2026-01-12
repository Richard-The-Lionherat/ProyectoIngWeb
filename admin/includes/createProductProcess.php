<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'A') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$nombre        = trim($_POST['nombre'] ?? '');
$categoria_id  = intval($_POST['categoria_id'] ?? 0);
$descripcion   = trim($_POST['descripcion'] ?? '');
$precio        = floatval($_POST['precio'] ?? 0);
$unidad_medida        = trim($_POST['unidad_medida'] ?? '');
$cantidad      = floatval($_POST['cantidad'] ?? -1);

if ($nombre === '') {
    header("Location: ../pages/createProduct.php?error=nombre");
    exit;
}

if ($categoria_id <= 0) {
    header("Location: ../pages/createProduct.php?error=categoria");
    exit;
}

if ($descripcion === '') {
    header("Location: ../pages/createProduct.php?error=descripcion");
    exit;
}

if ($precio <= 0) {
    header("Location: ../pages/createProduct.php?error=precio");
    exit;
}

if ($unidad === '') {
    header("Location: ../pages/createProduct.php?error=unidad");
    exit;
}

if ($cantidad < 0) {
    header("Location: ../pages/createProduct.php?error=cantidad");
    exit;
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../pages/createProduct.php?error=imagen");
    exit;
}

$imagen = $_FILES['imagen'];

$tiposPermitidos = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

$tamanoMaximo = 2 * 1024 * 1024; // 2MB

if ($imagen['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../pages/createProduct.php?error=imagen");
    exit;
}

/* ===== Validar MIME real ===== */
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeReal = finfo_file($finfo, $imagen['tmp_name']);
finfo_close($finfo);

if (!isset($tiposPermitidos[$mimeReal])) {
    header("Location: ../pages/createProduct.php?error=formato");
    exit;
}

/* ===== Tamaño ===== */
if ($imagen['size'] > $tamanoMaximo) {
    header("Location: ../pages/createProduct.php?error=pesada");
    exit;
}

/* ===== Nombre seguro ===== */
$extension = $tiposPermitidos[$mimeReal];
$nombreArchivo = uniqid('prod_') . '.' . $extension;

$rutaServidor = $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/images/' . $nombreArchivo;
$rutaBD = '/ProyectoIngWeb/images/' . $nombreArchivo;

/* ===== Guardar ===== */
if (!move_uploaded_file($imagen['tmp_name'], $rutaServidor)) {
    header("Location: ../pages/createProduct.php?error=guardar");
    exit;
}

$stmt = $conn->prepare("
    CALL sp_agregarProducto(
        ?, ?, ?, ?, ?, ?, ?, @resultado
    )
");

$stmt->bind_param(
    "issdsis",
    $categoria_id,
    $nombre,
    $descripcion,
    $precio,
    $unidad_medida,
    $cantidad,
    $rutaBD
);

$stmt->execute();
$stmt->close();

$res = $conn->query("SELECT @resultado AS resultado");
$row = $res->fetch_assoc();

if ($row['resultado'] == 0) {
    header("Location: ../layout.php?page=menu&success=1");
} else {
    header("Location: ../pages/createProduct.php?error=bd");
}
exit;