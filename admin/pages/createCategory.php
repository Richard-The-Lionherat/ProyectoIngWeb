<?php 
session_start();

if ($_SESSION['tipo'] !== 'A') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Panel de Administración | Categorias</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-emerald-800">

  <!-- Contenido principal -->
  <main class="flex justify-center items-center min-h-screen -mt-12">
    <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold mb-6 text-center">Agregar una categoría al menú</h1>

        <form id="formCategoria" 
            method="POST" 
            action="/ProyectoIngWeb/admin/includes/createCategoryProcess.php"
            enctype="multipart/form-data"
            class="space-y-4">

            <!-- Nombre -->
            <label class="block text-sm font-medium">Nombre de la categoría:</label>
            <input type="text"
                id="name"
                name="nombre"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg
                        focus:outline-none focus:ring focus:ring-black mb-4"
                required>

            <!-- Imagen -->
            <label class="block text-sm font-medium">Imagen de la categoría</label>

            <input type="file"
                id="imagen"
                name="imagen"
                accept="image/*"
                class="block w-full text-sm text-gray-700
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-emerald-400 file:text-white
                        hover:file:bg-emerald-600
                        cursor-pointer"
                required>

            <div id="previewContainer" class="mt-4 hidden">
                <p class="text-sm text-gray-600 mb-2">Vista previa:</p>
                <img id="previewImagen"
                    class="w-full max-h-48 object-contain rounded-lg border bg-white">
            </div>

            <div id="infoImagen" class="mt-2 hidden">
                <p id="tamanoImagen" class="text-xs text-gray-500"></p>
                <p id="warningImagen"
                class="text-xs text-yellow-700 font-medium hidden
                        flex items-center gap-1">
                    <i data-lucide="triangle-alert" class="w-4 h-4"></i>
                    <span></span>
                </p>
            </div>

            <!-- Campo para texto de verificación -->
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-4 text-sm text-green-700 bg-green-100 px-4 py-2 rounded-lg text-center">
                    Categoría creada correctamente
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'duplicado'): ?>
                <div class="mb-4 text-sm text-red-700 bg-red-100 px-4 py-2 rounded-lg text-center">
                    Ya existe una categoría con ese nombre o imagen
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['warning']) && $_GET['warning'] === 'pesada'): ?>
                <div class="mb-4 text-sm text-yellow-700 bg-yellow-100 px-4 py-2 rounded-lg
                            flex items-center justify-center gap-2">
                    <i data-lucide="triangle-alert" class="w-4 h-4"></i>
                    La imagen es grande, pero se puede usar
                </div>
            <?php endif; ?>
            
            <!-- Botón principal -->
            <button type="submit"
                    class="w-full bg-emerald-400 text-white py-2 rounded-lg
                        hover:bg-emerald-950 transition">
                Agregar categoría
            </button>

        </form>

      <!-- BOTÓN VOLVER -->
      <a href="/ProyectoIngWeb/admin/layout.php?page=categories">
        <button class="mt-6 w-full bg-black text-white py-2 rounded-lg hover:bg-gray-900 transition">
              Volver
          </button>
      </a>
    </div>
  </main>

    <script> lucide.createIcons(); </script>

    <script>
        document.getElementById("imagen").addEventListener("change", function (event) {

            const file = event.target.files[0];

            const previewContainer = document.getElementById("previewContainer");
            const previewImagen = document.getElementById("previewImagen");

            const infoImagen = document.getElementById("infoImagen");
            const tamanoImagen = document.getElementById("tamanoImagen");
            const warningImagen = document.getElementById("warningImagen");

            if (!file) {
                previewContainer.classList.add("hidden");
                infoImagen.classList.add("hidden");
                previewImagen.src = "";
                return;
            }

            if (!file.type.startsWith("image/")) {
                previewContainer.classList.add("hidden");
                infoImagen.classList.add("hidden");
                previewImagen.src = "";
                return;
            }

            /* ========= TAMAÑO ========= */
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            tamanoImagen.textContent = `Tamaño del archivo: ${sizeMB} MB`;

            /* ========= WARNING ========= */
            const LIMITE_MB = 2;

            if (sizeMB > LIMITE_MB) {
                warningImagen.querySelector("span").textContent =
                    "La imagen supera los 2 MB. Podría tardar más en cargarse.";
                warningImagen.classList.remove("hidden");
            } else {
                warningImagen.classList.add("hidden");
            }

            /* ========= PREVIEW ========= */
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImagen.src = e.target.result;
                previewContainer.classList.remove("hidden");
                infoImagen.classList.remove("hidden");
            };

            reader.readAsDataURL(file);
        });
    </script>

</body>

</html>