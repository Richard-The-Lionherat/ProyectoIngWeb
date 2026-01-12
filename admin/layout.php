<?php 
session_start();

if ($_SESSION['tipo'] !== 'A') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

$contenido = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-gray-100">

    <!-- Header -->
    <?php include 'includes/header.php'; ?>

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-sky-800 text-white p-6 space-y-4">

        <h2 class="text-2xl font-bold mb-6 flex gap-2">Admin<i data-lucide="shield-user"></i></h2>

        <nav class="space-y-2">
            <a href="?page=users" class="block hover:bg-sky-900 px-3 py-2 rounded flex gap-2">
                <i data-lucide="circle-user"></i>Usuarios</a>
            <a href="?page=ingredients" class="block hover:bg-sky-900 px-3 py-2 rounded flex gap-2">
                <i data-lucide="cooking-pot"></i>Ingredientes</a>
            <a href="?page=categories" class="block hover:bg-sky-900 px-3 py-2 rounded flex gap-2">
                <i data-lucide="hand-platter"></i>Categorias</a>
            <a href="?page=menu" class="block hover:bg-sky-900 px-3 py-2 rounded flex gap-2">
                <i data-lucide="utensils"></i>Menu</a>
        </nav>

    </aside>

    <!-- Contenido dinámico -->
    <main class="flex-1 p-8 bg-gray-50">

        <?php 
            $archivo = __DIR__ . "/pages/$contenido.php";
            if (file_exists($archivo)) {
                include $archivo;
            } else {
                echo "<h1 class='text-xl'>Página no encontrada</h1>";
            }
        ?>

    </main>

</div>

<!-- Modal de confirmación de logout -->
    <div id="logoutModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

        <div class="bg-sky-950 rounded-lg shadow-xl p-6 w-80 text-center">

            <h2 class="text-xl font-semibold mb-4 text-white">¿Cerrar sesión?</h2>
            <p class="text-gray-300 mb-6">¿Seguro que quieres salir de tu cuenta?</p>

            <div class="flex justify-around">
                <button 
                    onclick="confirmLogout()"
                    class="text-gray-200 border border-gray-200 px-4 py-2 rounded-lg hover:bg-gray-200 transition hover:text-sky-950 transition">
                    Sí
                </button>

                <button 
                    onclick="closeLogoutModal()"
                    class="border border-gray-200 bg-gray-200 text-sky-950 px-4 py-2 rounded-lg hover:bg-sky-950 transition hover:text-gray-200 transition">
                    No
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script> lucide.createIcons(); </script>

    <script>
        function openLogoutModal() {
            document.getElementById("logoutModal").classList.remove("hidden");
        }

        function closeLogoutModal() {
            document.getElementById("logoutModal").classList.add("hidden");
        }

        function confirmLogout() {
            // Redirige al logout real
            window.location.href = "/ProyectoIngWeb/includes/logout.php";
        }
    </script>

</body>
</html>