<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/header.php';
/* ================== AGREGAR PRODUCTO ================== */
if (isset($_POST['agregar'])) {
    $id = (int) $_POST['producto_id'];

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $_SESSION['carrito'][$id] = ($_SESSION['carrito'][$id] ?? 0) + 1;

    // Evita reenviar el formulario
    header("Location: carrito.php");
    exit;
}

/* ================== ELIMINAR PRODUCTO ================== */
if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    unset($_SESSION['carrito'][$id]);

    header("Location: carrito.php");
    exit;
}

/* ================== VACIAR CARRITO ================== */
if (isset($_GET['vaciar'])) {
    unset($_SESSION['carrito']);

    header("Location: carrito.php");
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <title>Tu Carrito de Compras | JAV-A COFFEE</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<section class="bg-[#f7f7f7] min-h-screen py-[60px]">
<div class="max-w-[900px] mx-auto px-[30px]">

<h1 class="flex items-center justify-center gap-2 text-[32px] font-bold text-[#1e3932] mb-[40px] text-center">
    <i data-lucide="shopping-cart"></i>
    Tu carrito
</h1>

<?php
/* ================== CARRITO VACÍO ================== */
if (empty($_SESSION['carrito'])) {
?>
    <p class="text-center text-gray-600 text-lg">
        El carrito está vacío
    </p>
</div>
</section>

<script>
    lucide.createIcons();
</script>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/footer.php';

    exit;
}

/* ================== CARRITO CON PRODUCTOS ================== */
$total = 0;

foreach ($_SESSION['carrito'] as $id => $cantidad) {

    /* PRODUCTO */
    $producto = $conn->query(
        "SELECT * FROM productos_bebidas WHERE id = $id"
    )->fetch_assoc();

    if (!$producto) continue;

    /* IMAGEN */
    $img = $conn->query(
        "SELECT imagen 
         FROM imagenes_comida 
         WHERE producto_id = $id
         LIMIT 1"
    )->fetch_assoc();

    /* PROMOCIÓN */
    $promo = $conn->query(
        "SELECT descuento 
         FROM promociones 
         WHERE producto_id = $id 
         AND activo = 1
         LIMIT 1"
    )->fetch_assoc();

    $precio_original = $producto['precio'];
    $precio_final = $precio_original;

    if ($promo && $promo['descuento'] > 0) {
        $precio_final = $precio_original * (1 - $promo['descuento'] / 100);
    }

    $subtotal = $precio_final * $cantidad;
    $total += $subtotal;
?>

<!-- ITEM -->
<div class="bg-white rounded-[20px] shadow-md p-[25px] mb-[25px] relative flex gap-[20px]">

    <!-- IMAGEN -->
    <div class="w-[120px] h-[120px] bg-gray-100 rounded-[16px] flex items-center justify-center overflow-hidden">
        <img src="<?= htmlspecialchars($img['imagen'] ?? 'img/no-image.jpg') ?>"
             class="h-full object-contain">
    </div>

    <!-- INFO -->
    <div class="flex-1">

        <?php if ($promo && $promo['descuento'] > 0) { ?>
            <span class="inline-block mb-[6px]
                         bg-[#c0392b] text-white text-[12px]
                         font-bold px-[10px] py-[4px] rounded-full">
                -<?= $promo['descuento'] ?>%
            </span>
        <?php } ?>

        <h3 class="text-[20px] font-semibold text-[#1e3932]">
            <?= htmlspecialchars($producto['nombre']) ?>
        </h3>

        <p class="text-gray-600">
            Precio: <strong class="text-[#00754a]">$<?= number_format($precio_final, 2) ?></strong>
        </p>

        <p class="text-gray-600">Cantidad: <?= $cantidad ?></p>

        <p class="font-bold mt-[6px]">
            Subtotal: $<?= number_format($subtotal, 2) ?>
        </p>

        <a href="carrito.php?eliminar=<?= $id ?>"
           class="inline-block mt-[10px] text-sm text-red-600 hover:underline">
            Eliminar
        </a>
    </div>
</div>

<?php } ?>

<!-- TOTAL -->
<div class="bg-white rounded-[20px] shadow-md p-[30px] text-center">
    <h2 class="text-[24px] font-bold text-[#1e3932] mb-[20px]">
        Total: $<?= number_format($total, 2) ?>
    </h2>

    <div class="flex flex-col items-center gap-4 mb-[20px]">
        <a href="checkout.php"
        class="flex items-center justify-center gap-2
                inline-block bg-[#00754a] text-white font-semibold
                px-[35px] py-[14px] rounded-full
                hover:bg-[#1e3932] transition">
            Continuar con el pedido
            <i data-lucide="coffee"></i>
        </a>

        <a href="menu.php"
        class="flex items-center justify-center gap-2
                inline-block bg-black text-white font-semibold
                px-[35px] py-[14px] rounded-full
                hover:bg-gray-900 transition">
            Seguir comprando
            <i data-lucide="shopping-basket"></i>
        </a>
    </div>

    <div class="mt-[20px]">
        <a href="carrito.php?vaciar=1"
           onclick="return confirm('¿Seguro que deseas vaciar el carrito?')"
           class="text-red-600 hover:underline">
            Vaciar carrito
        </a>
    </div>
</div>

</div>
</section>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/logoutModal.php'; ?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/footer.php'; ?>

<script>
  lucide.createIcons();
</script>