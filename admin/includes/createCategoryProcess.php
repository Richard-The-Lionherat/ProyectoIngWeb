<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'A') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

if (!isset($_POST['nombre']) || trim($_POST['nombre']) === '') {
    die("El nombre de la categoría es obligatorio.");
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== 0) {
    die("Error al subir la imagen.");
}

$nombre = trim($_POST['nombre']);

$imagen = $_FILES['imagen'];

$tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
$tamanoMaximo = 2 * 1024 * 1024; // 2MB

if (!in_array($imagen['type'], $tiposPermitidos)) {
    die("Formato de imagen no permitido.");
}

if ($imagen['size'] > $tamanoMaximo) {
    die("La imagen supera el tamaño máximo permitido.");
}

$extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
$nombreArchivo = uniqid('cat_') . '.' . $extension;

$rutaServidor = $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/images/' . $nombreArchivo;
$rutaBD = '/ProyectoIngWeb/images/' . $nombreArchivo;

if (!move_uploaded_file($imagen['tmp_name'], $rutaServidor)) {
    die("No se pudo guardar la imagen en el servidor.");
}

$stmt = $conn->prepare("CALL sp_crearCategoria(?, ?, @res)");
$stmt->bind_param("ss", $nombre, $rutaBD);
$stmt->execute();

$res = $conn->query("SELECT @res AS resultado")->fetch_assoc();

if ($res['resultado'] == 1) {
    die("Ya existe una categoría con ese nombre o imagen.");
}

header("Location: /ProyectoIngWeb/admin/layout.php?page=categories&success=1");
exit;