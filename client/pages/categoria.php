<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

/* ================== VALIDAR ID ================== */
$categoria_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($categoria_id <= 0) {
    header("Location: index.php");
    exit;
}

/* ================== CATEGORÍA ================== */
$categoriaRes = $conn->query(
    "SELECT nombre FROM categorias WHERE id = $categoria_id"
);

if ($categoriaRes->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$categoria = $categoriaRes->fetch_assoc();

/* ================== PRODUCTOS ================== */
$productos = $conn->query(
    "SELECT * 
     FROM productos_bebidas 
     WHERE categoria_id = $categoria_id"
);

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MENU | JAV-A COFFEE</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<!-- CONTENEDOR -->
<section class="max-w-[1300px] mx-auto p-[40px]">

    <!-- TÍTULO -->
    <h1 class="text-[36px] font-bold text-[#1e3932] mb-[40px] text-center">
        <?= htmlspecialchars($categoria['nombre']) ?>
    </h1>

    <!-- GRID -->
    <div class="grid grid-cols-[repeat(auto-fit,minmax(260px,1fr))] gap-[35px] justify-items-center">

        <?php while ($prod = $productos->fetch_assoc()) {

            /* IMAGEN PRINCIPAL */
            $img = $conn->query(
                "SELECT imagen 
                 FROM imagenes_comida 
                 WHERE producto_id = {$prod['id']}
                 LIMIT 1"
            )->fetch_assoc();

            /* PROMOCIÓN ACTIVA */
            $promo = $conn->query(
                "SELECT descuento 
                 FROM promociones 
                 WHERE producto_id = {$prod['id']}
                 AND activo = 1
                 LIMIT 1"
            )->fetch_assoc();

            /* PRECIO CON DESCUENTO */
            $precio_final = $prod['precio'];
            if ($promo && $promo['descuento'] > 0) {
                $precio_final = $prod['precio'] * (1 - $promo['descuento'] / 100);
            }
        ?>

        <!-- CARD -->
        <div class="bg-white rounded-[22px] overflow-hidden w-full max-w-[320px]
                    text-center relative
                    shadow-[0_12px_30px_rgba(0,0,0,0.12)]
                    transition-all duration-300
                    hover:-translate-y-[8px]
                    hover:shadow-[0_18px_40px_rgba(0,0,0,0.18)]">

            <!-- BADGE PROMOCIÓN -->
            <?php if ($promo && $promo['descuento'] > 0) { ?>
                <span class="absolute top-[12px] right-[12px]
                             px-[12px] py-[6px] rounded-[20px]
                             text-[12px] font-bold text-white
                             bg-[#c0392b]">
                    -<?= $promo['descuento'] ?>%
                </span>
            <?php } ?>

            <!-- IMAGEN -->
            <img src="<?= htmlspecialchars($img['imagen'] ?? 'img/no-image.jpg') ?>"
                 alt="<?= htmlspecialchars($prod['nombre']) ?>"
                 class="w-full h-[220px] object-cover">

            <!-- NOMBRE -->
            <h3 class="mt-[16px] mb-[6px] text-[20px] font-semibold text-[#1e3932]">
                <?= htmlspecialchars($prod['nombre']) ?>
            </h3>

            <!-- PRECIO -->
            <p class="text-[16px] font-bold text-[#00754a]">
                $<?= number_format($precio_final, 2) ?>
            </p>

            <!-- BOTÓN -->
            <a href="producto.php?id=<?= $prod['id'] ?>"
               class="inline-block bg-[#00754a] text-white font-bold
                      px-[26px] py-[12px] rounded-[30px]
                      my-[16px] hover:bg-[#1e3932] transition-colors">
                Ver detalle
            </a>
        </div>

        <?php } ?>
    </div>
</section>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/logoutModal.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/footer.php'; ?>

<!-- Activar iconos Lucide -->
<script>
  lucide.createIcons();
</script>