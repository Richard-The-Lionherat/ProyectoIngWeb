<?php
session_start();
include "includes/conexion.php";

/* ================= VALIDAR PEDIDO ================= */
if (!isset($_SESSION['pedido_id'])) {
    header("Location: index.php");
    exit;
}

$pedido_id = (int) $_SESSION['pedido_id'];

/* ================= OBTENER PEDIDO ================= */
$pedido = $conn->query(
    "SELECT * FROM pedidos WHERE id = $pedido_id"
)->fetch_assoc();

if (!$pedido) {
    echo "<p class='text-center text-gray-600 mt-10'>Pedido no encontrado</p>";
    exit;
}

/* ================= HEADER ================= */
include "includes/header.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido confirmado | JAV-A COFFEE</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<section class="bg-[#f7f7f7] min-h-screen py-[80px]">
<div class="max-w-[700px] mx-auto bg-white rounded-2xl shadow-md p-[40px] text-center">

    <!-- TÍTULO -->
    <h1 class="text-[32px] font-bold text-[#1e3932] mb-[10px]">
        ☕ ¡Gracias por tu compra!
    </h1>

    <p class="text-gray-600 mb-[20px]">
        Tu pedido fue registrado correctamente
    </p>

    <!-- FOLIO -->
    <?php if (!empty($pedido['folio'])) { ?>
        <p class="mb-[25px] text-[16px]">
            <strong>Folio del pedido:</strong>
            <span class="inline-block bg-gray-100 text-gray-800
                         px-[14px] py-[6px] rounded-full font-semibold">
                <?= htmlspecialchars($pedido['folio']) ?>
            </span>
        </p>
    <?php } ?>

    <hr class="mb-[30px]">

    <!-- TOTAL -->
    <p class="text-[22px] font-bold text-[#00754a] mb-[20px]">
        Total pagado: $<?= number_format($pedido['total'], 2) ?>
    </p>

    <!-- MÉTODO DE PAGO -->
    <p class="mb-[10px]">
        <strong>Método de pago:</strong>
        <?= htmlspecialchars($pedido['metodo_pago']) ?>
    </p>

    <!-- ESTADO -->
    <p class="mb-[10px]">
        <strong>Estado del pago:</strong>
        <span class="inline-block bg-green-100 text-green-700
                     px-[12px] py-[4px] rounded-full text-sm font-semibold">
            <?= htmlspecialchars($pedido['estado_pago']) ?>
        </span>
    </p>

    <!-- REFERENCIA -->
    <?php if (!empty($pedido['referencia'])) { ?>
        <p class="mb-[10px]">
            <strong>Referencia:</strong>
            <?= htmlspecialchars($pedido['referencia']) ?>
        </p>
    <?php } ?>

    <!-- NOTAS -->
    <?php if (!empty($pedido['notas'])) { ?>
        <hr class="my-[20px]">
        <p class="text-gray-700">
            <strong>Notas del pedido:</strong><br>
            <?= nl2br(htmlspecialchars($pedido['notas'])) ?>
        </p>
    <?php } ?>

    <hr class="my-[30px]">

    <!-- BOTONES -->
    <div class="flex flex-col gap-[15px] items-center">

        <a href="ticket.php?id=<?= $pedido_id ?>"
           class="inline-block bg-gray-200 text-gray-800
                  px-[30px] py-[12px] rounded-full
                  hover:bg-gray-300 transition">
            📄 Descargar ticket
        </a>

        <a href="index.php"
           class="inline-block bg-[#00754a] text-white font-semibold
                  px-[30px] py-[12px] rounded-full
                  hover:bg-[#1e3932] transition">
            Volver al menú
        </a>

    </div>

</div>
</section>

<?php include "includes/footer.php"; ?>