<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}
?>

<?php
include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$categorias = $conn->query("SELECT * FROM categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="m-0 font-sans bg-[#f6f6f6] text-[#333]">

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/header.php'; ?>

<!-- CONTENEDOR -->
<div class="max-w-[1300px] mx-auto p-[40px]">

    <!-- HERO -->
    <section class="text-center mb-[60px]">
        <h1 class="flex items-center justify-center gap-2 text-[42px] font-bold text-[#1e3932] mb-[12px]">
            <i data-lucide="heart" class="text-purple-900 fill-purple-900"></i>
            Hecho para tu momento
            <i data-lucide="heart" class="text-purple-900 fill-purple-900"></i>
        </h1>

        <p class="text-[18px] text-[#555]">
            Cada taza cuenta una historia en <strong>JAV-A COFFEE</strong>
        </p>
    </section>

    <!-- GRID -->
    <div class="grid grid-cols-[repeat(auto-fit,minmax(260px,1fr))] gap-[35px] justify-items-center">

        <?php while ($cat = $categorias->fetch_assoc()) { ?>
            
            <!-- CARD -->
            <div class="bg-white rounded-[22px] overflow-hidden w-full max-w-[320px]
                        text-center relative
                        shadow-[0_12px_30px_rgba(0,0,0,0.12)]
                        transition-all duration-300 ease-in-out
                        hover:-translate-y-[8px]
                        hover:shadow-[0_18px_40px_rgba(0,0,0,0.18)]">

                <img src="<?= $cat['imagen'] ?>" 
                     alt="<?= $cat['nombre'] ?>"
                     class="w-full h-[220px] object-cover">

                <h3 class="mt-[16px] mb-[10px] text-[20px] font-semibold text-[#1e3932]">
                    <?= $cat['nombre'] ?>
                </h3>

                <a href="categoria.php?id=<?= $cat['id'] ?>"
                   class="inline-block bg-[#00754a] text-white font-bold
                          px-[26px] py-[12px] rounded-[30px]
                          my-[16px] hover:bg-[#1e3932] transition-colors">
                    Ver productos
                </a>
            </div>

        <?php } ?>

    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/logoutModal.php'; ?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/footer.php'; ?>

<!-- Activar iconos Lucide -->
<script>
  lucide.createIcons();
</script>

</body>
</html>