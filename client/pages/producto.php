<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

/* valida nuestro id*/
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

/* cosulta producot*/
$resultado = $conn->query(
    "SELECT * FROM productos_bebidas WHERE id = $id"
);

if ($resultado->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$producto = $resultado->fetch_assoc();

$imagenes = $conn->query(
    "SELECT imagen FROM imagenes_comida WHERE producto_id = $id"
);

/* promo*/
$promo = $conn->query(
    "SELECT descuento 
     FROM promociones 
     WHERE producto_id = $id 
     AND activo = 1
     LIMIT 1"
)->fetch_assoc();

/* precio fin*/
$precio_final = $producto['precio'];
if ($promo && $promo['descuento'] > 0) {
    $precio_final = $producto['precio'] * (1 - $promo['descuento'] / 100);
}


include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/header.php';
?>


<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($producto['nombre']) ?> | JAV-A COFFEE</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>


<section class="bg-[#f7f7f7] py-[60px]">
<div class="max-w-[1100px] mx-auto px-[40px]">

   
    <h1 class="text-[36px] font-bold text-[#1e3932] mb-[20px]">
        <?= htmlspecialchars($producto['nombre']) ?>
    </h1>

    <!-- fotitos-->
    <div class="flex flex-wrap gap-[20px] my-[30px]">
        <?php while ($img = $imagenes->fetch_assoc()) { ?>
            <div class="w-[220px] h-[220px] rounded-[18px] overflow-hidden bg-white shadow-md">
                <img src="<?= htmlspecialchars($img['imagen']) ?>" 
                     alt="<?= htmlspecialchars($producto['nombre']) ?>"
                     class="w-full h-full object-cover">
            </div>
        <?php } ?>
    </div>

    <!-- decripcion -->
    <p class="text-[16px] text-[#555] mb-[15px]">
        <?= nl2br(htmlspecialchars($producto['descripcion'])) ?>
    </p>

    <!-- precio-->
    <p class="text-[20px] font-bold text-[#00754a] mb-[8px]">
        $<?= number_format($precio_final, 2) ?>
        <?php if ($promo) { ?>
            <span class="ml-[10px] text-sm bg-red-500 text-white px-[10px] py-[4px] rounded-full">
                -<?= $promo['descuento'] ?>%
            </span>
        <?php } ?>
    </p>

   
    <?php if (!is_null($producto['onzas'])) { ?>
        <p class="text-[16px] mb-[8px]">
            <strong>Tamaño:</strong> <?= (int)$producto['onzas'] ?> oz
        </p>
    <?php } ?>

   
    <?php if (!is_null($producto['estrellas'])) { ?>
        <p class="text-[16px] mb-[20px] flex items-center">
            <strong class="mr-2">Calificación:</strong>

            <span class="flex items-center gap-1">
                <?php
                $estrellas = (int)$producto['estrellas'];
                $max = 5;

                for ($i = 0; $i < $max; $i++) {
                    if ($i < $estrellas) {
                        echo '<i data-lucide="star" class="w-5 h-5 text-yellow-500 fill-yellow-500"></i>';
                    } else {
                        echo '<i data-lucide="star" class="w-5 h-5 text-gray-300"></i>';
                    }
                }
                ?>
            </span>
        </p>
    <?php } ?>



   
    <form method="post" action="carrito.php">
        <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">

        <button type="submit" name="agregar"
            class="flex items-center gap-2
                bg-[#00754a] text-white font-bold
                px-[34px] py-[14px] rounded-full
                hover:bg-[#1e3932] transition">
            Agregar al carrito
            <i data-lucide="shopping-cart"></i>
        </button>

    </form>

</div>
</section>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/logoutModal.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/footer.php'; ?>

<script>
  lucide.createIcons();
</script>