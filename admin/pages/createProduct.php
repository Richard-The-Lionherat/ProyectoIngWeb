<?php 
session_start();

if ($_SESSION['tipo'] !== 'A') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$categorias = $conn->query("
    SELECT id, nombre 
    FROM categorias 
    ORDER BY nombre
");
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
  <main class="flex justify-center min-h-screen pt-8 pb-8">
    <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold mb-6 text-center">Agregar un producto al menú</h1>

        <form id="formProducto" 
            method="POST" 
            action="/ProyectoIngWeb/admin/includes/createProductProcess.php"
            enctype="multipart/form-data"
            class="space-y-4">

            <!-- Nombre -->
            <label class="block text-sm font-medium">Nombre del producto</label>
            <input type="text"
                id="name"
                name="nombre"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg
                        focus:outline-none focus:ring focus:ring-black mb-4"
                required>

            <!-- Elegir categoría -->
            <label for="categoria_id" class="block text-sm font-medium">
                Categoría del producto
            </label>

            <select
                id="categoria_id"
                name="categoria_id"
                required
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg
                    focus:outline-none focus:ring focus:ring-black mb-4 bg-white"
            >
                <option value="">Selecciona una categoría</option>

                <?php while ($cat = $categorias->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Descripción del producto -->
            <label for="descripcion" class="block text-sm font-medium">
                Descripción del producto
            </label>

            <textarea
                id="descripcion"
                name="descripcion"
                rows="4"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg
                    focus:outline-none focus:ring focus:ring-black mb-4
                    resize-y"
                placeholder="Describe el producto (ingredientes, sabor, notas, etc.)"
                required
            ></textarea>

            <!-- Precio -->
            <label for="price" class="block text-sm font-medium mb-1">
                Precio
            </label>

            <div class="relative">
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    id="price"
                    name="precio"
                    class="w-full pr-10 px-3 py-2 border border-gray-300 rounded-lg
                        focus:outline-none focus:ring focus:ring-black"
                    required
                >

                <span class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                    $
                </span>
            </div>

            <!-- Unidad de medida -->
            <label for="meassure" class="block text-sm font-medium">Forma de medir el producto (unidades)</label>
            <input type="text" id="unidad_medida" name="unidad_medida"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4"
                required>
            
            <!-- Cantidad -->
            <label for="quantity" class="block text-sm font-medium">Cantidad de producto</label>
            <input type="number" step="0.001" min="0" id="quantity" name="cantidad"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4"
                required>

            <!-- Imagen -->
            <label class="block text-sm font-medium">Imagen del producto</label>

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
      <a href="/ProyectoIngWeb/admin/layout.php?page=menu">
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