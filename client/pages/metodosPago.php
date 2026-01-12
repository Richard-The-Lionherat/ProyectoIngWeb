<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$email = $conn->real_escape_string($_SESSION['email']);

$metodos = $conn->query("
    SELECT *
    FROM metodos_pago
    WHERE mp_emailUsuario = '$email'
      AND mp_activo = 1
    ORDER BY mp_predeterminado DESC, mp_creado DESC
");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Métodos de pago | JAV-A COFFEE</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-emerald-800">

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/header.php'; ?>

<main class="flex justify-center items-start min-h-screen pt-24 pb-16">
    <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-3xl">

        <!-- TÍTULO -->
        <h1 class="flex items-center justify-center gap-2
                   text-2xl font-bold mb-6 text-emerald-900">
            <i data-lucide="credit-card"></i>
            Métodos de pago
        </h1>

        <!-- MENSAJE SI NO HAY MÉTODOS -->
        <?php if ($metodos->num_rows === 0): ?>
            <p class="text-center text-gray-600 mb-6">
                Aún no tienes métodos de pago registrados
            </p>
        <?php endif; ?>

        <!-- LISTADO -->
        <?php if ($metodos->num_rows > 0): ?>
            <div class="space-y-4 mb-8">

            <?php while ($m = $metodos->fetch_assoc()): ?>

                <div class="bg-white rounded-xl p-4
                            flex justify-between items-center
                            shadow-sm">

                    <!-- INFO -->
                    <div class="flex items-center gap-3">
                        <i data-lucide="credit-card" class="w-6 h-6 text-emerald-700"></i>

                        <div>
                            <p class="font-semibold text-gray-800">
                                <?= htmlspecialchars($m['mp_alias']) ?>
                            </p>

                            <?php if ($m['mp_tipo'] === 'TARJETA'): ?>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($m['mp_marca']) ?>
                                    · **** <?= htmlspecialchars($m['mp_ultimos4']) ?>
                                    · Expira <?= sprintf('%02d', $m['mp_exp_mes']) ?>/<?= $m['mp_exp_anio'] ?>
                                </p>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($m['mp_tipo']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BADGES -->
                    <?php if ($m['mp_predeterminado']): ?>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">
                            Predeterminado
                        </span>
                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

            </div>
        <?php endif; ?>

        <!-- BOTÓN AGREGAR -->
        <div class="flex justify-center">
            <button
                class="flex items-center gap-2
                       bg-emerald-600 hover:bg-emerald-800
                       text-white px-6 py-3 rounded-lg
                       transition shadow">

                <i data-lucide="plus-circle"></i>
                Agregar método de pago
            </button>
        </div>

        <!-- Volver -->
        <div class="flex justify-center gap-4 mt-4">

            <a href="/ProyectoIngWeb/client/pages/carrito.php"
                class="flex items-center justify-center
                        h-12 w-12
                        border border-emerald-900
                        text-emerald-900 rounded-lg
                        hover:bg-emerald-100 transition">
                <i data-lucide="shopping-cart"></i>
            </a>

            <a href="/ProyectoIngWeb/client/dashboard.php"
                class="flex items-center justify-center
                        h-12 px-6
                        border border-emerald-900
                        font-bold text-emerald-900 rounded-lg
                        hover:bg-emerald-100 transition gap-2">
                <i data-lucide="circle-chevron-left"></i>
                Volver al inicio
            </a>

        </div>

    </div>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/metodoPagoModal.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/logoutModal.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/footer.php'; ?>

<script>
    lucide.createIcons();
</script>

</body>
</html>