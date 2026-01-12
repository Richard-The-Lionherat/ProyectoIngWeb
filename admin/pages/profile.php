<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-emerald-800">

<main class="flex justify-center items-center min-h-screen -mt-20">

    <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-md">

        <h1 class="text-2xl font-bold mb-8 text-center">Mi Perfil</h1>

        <!-- CAMPO NOMBRE -->
        <div class="flex group mb-4">

        <!-- Caja del nombre -->
        <div class="bg-white p-4 border rounded-l-lg w-full border-black group-hover:bg-emerald-100 transition">
            <p class="text-gray-600 text-sm">Nombre</p>
            <p class="font-bold text-lg">
                <?php echo $_SESSION['nombre'] ?? 'Usuario'; ?>
            </p>
        </div>

        <!-- Botón del lápiz -->
        <a href="/ProyectoIngWeb/admin/pages/changeName.php"
            class="border border-black bg-white text-emerald-400 px-4 flex items-center justify-center 
                rounded-r-lg hover:bg-emerald-400 hover:text-black transition">
                <i data-lucide="pencil"></i>
            </a>

        </div>

        <!-- CAMPO CONTRASEÑA -->
        <div class="flex group mb-4">

            <div class="bg-white p-4 border rounded-l-lg w-full border-black group-hover:bg-emerald-100 transition">
                <p class="text-gray-600 text-sm">Contraseña</p>
                
                <?php 
                    $len = strlen($_SESSION['password_plain'] ?? "********");
                    echo "<p class='font-bold text-lg'>" . str_repeat("•", $len) . "</p>";
                ?>
            </div>

            <a href="/ProyectoIngWeb/admin/pages/changePassword.php"
               class="border border-black bg-white text-emerald-400 px-4 flex items-center justify-center 
                rounded-r-lg hover:bg-emerald-400 hover:text-black transition">
                <i data-lucide="pencil"></i>
            </a>
        </div>

        <!-- BOTÓN VOLVER -->
        <a href="/ProyectoIngWeb/admin/layout.php">
            <button class="mt-6 w-full bg-black text-white py-2 rounded-lg hover:bg-gray-900 transition">
                Volver
            </button>
        </a>

    </div>
</main>

<script>
    lucide.createIcons();
</script>

</body>
</html>