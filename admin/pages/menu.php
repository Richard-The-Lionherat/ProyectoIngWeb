<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/ProyectoIngWeb/includes/connection.php";
?>

<!-- Título -->
<h1 class="text-3xl font-bold mb-6">Menú de la cafetería</h1>

<!-- Barra superior (Buscar + Crear categoría) -->
<div class="flex items-center justify-between mb-4">

    <!-- Buscar -->
    <input 
        type="text" 
        id="searchInput"
        placeholder="Buscar producto..."
        class="px-3 py-2 border rounded-lg w-1/3 focus:outline-none focus:ring focus:ring-sky-900"
    >

    <!-- Filtro por categoría -->
    <select 
        id="filterCategoria"
        class="px-3 py-2 border rounded-lg
            focus:outline-none focus:ring focus:ring-sky-900
            bg-white text-gray-800"
    >
        <option value="">Todas las categorías</option>

        <?php
        $categorias = $conn->query("SELECT id, nombre FROM categorias ORDER BY nombre");
        while ($cat = $categorias->fetch_assoc()):
        ?>
            <option value="<?= $cat['id'] ?>">
                <?= htmlspecialchars($cat['nombre']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <!-- Botón nueva categoría -->
    <a href="/ProyectoIngWeb/admin/pages/createProduct.php"
       class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
       <i data-lucide="utensils-crossed"></i>
       Nuevo producto
    </a>
</div>

<!-- Tabla -->
<div class="overflow-x-auto">
    <table id="tablaMenu" class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
        <thead class="bg-sky-900 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Id del producto</th>
                <th class="py-3 px-4 text-left">Nombre</th>
                <th class="py-3 px-4 text-left">Categoría</th>
                <th class="py-3 px-4 text-center">Eliminar producto</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $query = "
                SELECT 
                    p.id,
                    p.nombre AS producto,
                    p.categoria_id,
                    c.nombre AS categoria
                FROM productos_bebidas p
                JOIN categorias c ON p.categoria_id = c.id
            ";

            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()):
            ?>
                <tr class="border-b hover:bg-gray-100 transition"
                    data-categoria-id="<?= $row['categoria_id'] ?>"
                >
                    <td class="py-2 px-4 id"><?php echo $row['id']; ?></td>
                    <td class="py-2 px-4 font-medium text-sky-900 hover:underline">
                        <a href="/ProyectoIngWeb/admin/pages/product.php?id=<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['producto']) ?>
                        </a>
                    </td>
                    <td class="py-2 px-4 categoria"><?php echo $row['categoria']; ?></td>

                    <!-- Eliminar producto -->
                    <td class="py-2 px-4 text-center">
                        <button class="px-3 py-1 rounded-lg bg-red-500 text-white hover:bg-red-600 transition deleteBtn"
                            data-id="<?php echo $row['id']; ?>">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/admin/includes/deleteProductModal.php'; ?>

<script>
const searchInput = document.getElementById("searchInput");
const filterCategoria = document.getElementById("filterCategoria");
const tabla = document
    .getElementById("tablaMenu")
    .getElementsByTagName("tbody")[0];

function actualizarTabla() {
    const busqueda = searchInput.value.toLowerCase();
    const filtroCategoria = filterCategoria.value;

    for (let row of tabla.rows) {
        const nombre = row.querySelector(".producto").textContent.toLowerCase();
        const categoriaId = row.dataset.categoriaId;

        const coincideBusqueda = nombre.includes(busqueda);

        const coincideCategoria =
            !filtroCategoria || categoriaId === filtroCategoria;

        row.style.display =
            coincideBusqueda && coincideCategoria ? "" : "none";
    }
}

// Eventos
searchInput.addEventListener("input", actualizarTabla);
filterCategoria.addEventListener("change", actualizarTabla);

// Asignar evento a todos los botones de eliminar
document.querySelectorAll(".deleteBtn").forEach(btn => {
    btn.addEventListener("click", () => {
        abrirModal(btn.dataset.id);
    });
});
</script>