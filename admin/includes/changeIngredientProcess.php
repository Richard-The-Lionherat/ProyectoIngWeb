<?php
header('Content-Type: application/json');
session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

/* ================= OBTENER DATOS ================= */
$id       = intval($_POST['id'] ?? 0);
$cantidad = $_POST['cantidad'] ?? null;
$medida   = trim($_POST['medida'] ?? '');

/* ================= VALIDACIONES ================= */

// ID inválido
if ($id <= 0) {
    echo json_encode([
        "status" => 1,
        "error"  => "ID de ingrediente inválido"
    ]);
    exit;
}

// Cantidad: si viene vacía → 0
if ($cantidad === null || $cantidad === '') {
    $cantidad = 0;
}

// Validar cantidad numérica
if (!is_numeric($cantidad) || $cantidad < 0) {
    echo json_encode([
        "status" => 2,
        "error"  => "Cantidad inválida"
    ]);
    exit;
}

$cantidad = floatval($cantidad);

// Medida inválida
if ($medida === '') {
    echo json_encode([
        "status" => 3,
        "error"  => "La unidad de medida no puede estar vacía"
    ]);
    exit;
}

/* ================= EJECUCIÓN ================= */
try {

    /* Cambiar cantidad */
    $stmt1 = $conn->prepare(
        "CALL sp_cambiarCantidad(?, ?, @resCantidad)"
    );
    $stmt1->bind_param("id", $id, $cantidad);
    $stmt1->execute();
    $stmt1->close();

    /* Cambiar medida */
    $stmt2 = $conn->prepare(
        "CALL sp_cambiarMedida(?, ?, @resMedida)"
    );
    $stmt2->bind_param("is", $id, $medida);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode([
        "status" => 0,
        "message" => "Ingrediente actualizado correctamente"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => -1,
        "error"  => "Error en el servidor"
    ]);
}
?>