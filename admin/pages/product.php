<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'A') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: ../layout.php?page=menu");
    exit;
}

$producto = $conn->query("
    SELECT 
        p.id,
        p.nombre,
        p.descripcion,
        p.precio,
        p.unidad_medida,
        p.cantidad,
        p.categoria_id,
        c.nombre AS categoria
    FROM productos_bebidas p
    JOIN categorias c ON p.categoria_id = c.id
    WHERE p.id = $id
")->fetch_assoc();

if (!$producto) {
    header("Location: ../layout.php?page=menu");
    exit;
}

$categorias = $conn->query("
    SELECT id, nombre 
    FROM categorias 
    ORDER BY nombre
");

$imagenes = $conn->query("
    SELECT id, imagen 
    FROM imagenes_comida 
    WHERE producto_id = $id
");

$promo = $conn->query("
    SELECT * 
    FROM promociones 
    WHERE producto_id = $id AND activo = 1
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-emerald-800">

<main class="max-w-5xl mx-auto py-10 px-6 space-y-8">

<section class="bg-white rounded-xl p-6 shadow">
    <h2 class="text-xl font-bold mb-4">Datos del producto</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 flex items-center gap-2
                    bg-green-100 text-green-700
                    px-4 py-3 rounded-lg text-sm font-medium">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            Cambios guardados correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="mb-4 flex items-center gap-2
                    bg-red-100 text-red-700
                    px-4 py-3 rounded-lg text-sm font-medium">
            <i data-lucide="x-circle" class="w-5 h-5"></i>

            <?php
            switch ($_GET['error']) {
                case 'datos':
                    echo 'Faltan datos obligatorios.';
                    break;
                case 'stock':
                    echo 'La unidad de medida o la cantidad no son válidas.';
                    break;
                case 'bd':
                    echo 'Error al guardar los cambios en la base de datos.';
                    break;
                default:
                    echo 'Ocurrió un error inesperado.';
            }
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="../includes/updateProductProcess.php" class="space-y-4">
        <input type="hidden" name="id" value="<?= $producto['id'] ?>">

        <label class="block">
            <span class="text-sm font-medium">Nombre</span>
            <input name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>"
                   class="w-full border rounded-lg px-3 py-2">
        </label>

        <label class="block">
            <span class="text-sm font-medium">Categoría</span>
            <select name="categoria_id" class="w-full border rounded-lg px-3 py-2">
                <?php while ($cat = $categorias->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= $cat['id'] == $producto['categoria_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label>

        <label class="block">
            <span class="text-sm font-medium">Descripción</span>
            <textarea name="descripcion" rows="4"
                      class="w-full border rounded-lg px-3 py-2"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
        </label>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="precio" class="block text-sm font-medium mb-1">
                    Precio
                </label>

                <div class="relative">
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        id="precio"
                        name="precio"
                        value="<?= htmlspecialchars($producto['precio']) ?>"
                        class="w-full pr-10 px-3 py-2 border border-gray-300 rounded-lg
                            focus:outline-none focus:ring focus:ring-black"
                        required
                    >

                    <span class="absolute inset-y-0 right-3 flex items-center text-gray-500 pointer-events-none">
                        $
                    </span>
                </div>
            </div>

            <div>
                <label for="unidad_medida" class="block text-sm font-medium mb-1">
                    Unidad de medida
                </label>

                <input
                    type="text"
                    id="unidad_medida"
                    name="unidad_medida"
                    value="<?= htmlspecialchars($producto['unidad_medida']) ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg
                        focus:outline-none focus:ring focus:ring-black"
                    required
                >
            </div>
        </div>

        <label>
            <span class="text-sm font-medium">Cantidad</span>
            <input type="number" step="0.001" name="cantidad"
                   value="<?= $producto['cantidad'] ?>"
                   class="w-full border rounded-lg px-3 py-2">
        </label>

        <button class="bg-emerald-600 text-white px-6 py-2 rounded-lg">
            Guardar cambios
        </button>
    </form>
</section>

<section class="bg-white rounded-xl p-6 shadow mt-8">

    <h2 class="text-xl font-bold mb-4 text-emerald-900">
        Imágenes del producto
    </h2>

    <!-- GRID DE IMÁGENES -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <?php while ($img = $imagenes->fetch_assoc()): ?>
            <div class="relative group w-32 h-32">

                <!-- IMAGEN -->
                <img
                    src="<?= $img['imagen'] ?>"
                    alt="Imagen del producto"
                    class="w-full h-full object-cover rounded-lg
                           transition
                           group-hover:brightness-75"
                >

                <!-- BOTÓN ELIMINAR -->
                <button
                    type="button"
                    onclick="abrirModalEliminarImagen(<?= $img['id'] ?>)"
                    class="absolute top-2 right-2
                           bg-red-600 text-white
                           p-2 rounded-full
                           opacity-0
                           group-hover:opacity-100
                           transition"
                    title="Eliminar imagen"
                >
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>

            </div>
        <?php endwhile; ?>
    </div>

    <!-- FORMULARIO AGREGAR IMAGEN -->
    <form
        action="/ProyectoIngWeb/admin/includes/addProductImageProcess.php"
        method="POST"
        enctype="multipart/form-data"
        class="mt-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">

        <input type="hidden" name="producto_id" value="<?= $id ?>">

        <input
            type="file"
            name="imagen"
            accept="image/*"
            required
            class="block text-sm text-gray-700
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-lg file:border-0
                   file:text-sm file:font-semibold
                   file:bg-emerald-400 file:text-white
                   hover:file:bg-emerald-600
                   cursor-pointer">

        <button
            type="submit"
            class="bg-emerald-600 text-white
                   px-4 py-2 rounded-lg
                   hover:bg-emerald-800 transition
                   flex items-center gap-2">

            <i data-lucide="plus-circle"></i>
            Agregar imagen
        </button>
    </form>

</section>

<section class="bg-white rounded-xl p-6 shadow">
    <h2 class="text-xl font-bold mb-4">Promoción</h2>

    <?php if ($promo): ?>
        <p>Descuento: <?= $promo['descuento'] ?>%</p>
        <button class="bg-red-600 text-white px-4 py-2 rounded">Eliminar promoción</button>
    <?php else: ?>
        <button class="bg-emerald-600 text-white px-4 py-2 rounded">Agregar promoción</button>
    <?php endif; ?>
</section>

<section class="flex justify-between">
    <a href="../layout.php?page=menu" class="text-white underline flex gap-2">
        <i data-lucide="arrow-left-from-line"></i>
        Volver al menú
    </a>

    <button class="bg-red-700 text-white px-6 py-2 rounded">
        Eliminar producto
    </button>
</section>

</main>

<!-- MODAL CONFIRMAR ELIMINAR IMAGEN -->
<div id="modalEliminarImagen"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl p-6 w-full max-w-sm shadow-xl">
        <h3 class="text-lg font-bold mb-3 text-red-700 flex items-center gap-2">
            <i data-lucide="trash-2"></i>
            Eliminar imagen
        </h3>

        <p class="text-gray-700 mb-6">
            ¿Seguro que deseas eliminar esta imagen?
            <br>
            <span class="text-sm text-gray-500">
                Esta acción no se puede deshacer.
            </span>
        </p>

        <div class="flex justify-end gap-3">
            <button
                onclick="cerrarModalEliminarImagen()"
                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">
                Cancelar
            </button>

            <button
                onclick="confirmarEliminarImagen()"
                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                Eliminar
            </button>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>

<script>
let imagenAEliminar = null;

function abrirModalEliminarImagen(idImagen) {
    imagenAEliminar = idImagen;
    document.getElementById("modalEliminarImagen").classList.remove("hidden");
    document.getElementById("modalEliminarImagen").classList.add("flex");
}

function cerrarModalEliminarImagen() {
    imagenAEliminar = null;
    document.getElementById("modalEliminarImagen").classList.add("hidden");
    document.getElementById("modalEliminarImagen").classList.remove("flex");
}

async function confirmarEliminarImagen() {
    if (!imagenAEliminar) return;

    try {
        const res = await fetch(
            "/ProyectoIngWeb/admin/includes/deleteProductImageProcess.php",
            {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `imagen_id=${imagenAEliminar}`
            }
        );

        const data = await res.json();

        if (data.status === 0) {
            location.reload();
        } else {
            alert(data.error || "No se pudo eliminar la imagen");
        }

    } catch (err) {
        alert("Error al comunicarse con el servidor");
    }
}
</script>

</body>
</html>