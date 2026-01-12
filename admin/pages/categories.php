<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/ProyectoIngWeb/includes/connection.php";
?>

<!-- Título -->
<h1 class="text-3xl font-bold mb-6">Categorias del Menú</h1>

<!-- Barra superior (Buscar + Crear categoría) -->
<div class="flex items-center justify-between mb-4">

    <!-- Buscar -->
    <input 
        type="text" 
        id="searchInput"
        placeholder="Buscar categoría..."
        class="px-3 py-2 border rounded-lg w-1/3 focus:outline-none focus:ring focus:ring-sky-900"
    >

    <!-- Botón nueva categoría -->
    <a href="/ProyectoIngWeb/admin/pages/createCategory.php"
       class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
       <i data-lucide="circle-plus"></i>
       Nueva Categoría
    </a>
</div>

<!-- Tabla -->
<div class="overflow-x-auto">
    <table id="tablaCategorias" class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
        <thead class="bg-sky-900 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Id de la categoría</th>
                <th class="py-3 px-4 text-left">Nombre</th>
                <th class="py-3 px-4 text-left">Imagen asociada</th>
                <th class="py-3 px-4 text-center">Cambiar imagen</th>
                <th class="py-3 px-4 text-center">Eliminar categoría</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $query = "SELECT id, nombre, imagen FROM categorias";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()):
            ?>
                <tr class="border-b hover:bg-gray-100 transition">
                    <td class="py-2 px-4 id"><?php echo $row['id']; ?></td>
                    <td class="py-2 px-4 nombre"><?php echo $row['nombre']; ?></td>
                    <td class="py-2 px-4 text-center medida">
                        <img 
                            src="<?= htmlspecialchars($row['imagen']) ?>" 
                            alt="<?= htmlspecialchars($row['nombre']) ?>"
                            class="w-16 h-16 object-cover rounded-lg border"
                        >
                    </td>

                    <!-- Cambiar imagen -->
                    <td class="py-2 px-4 text-center">
                        <button
                            onclick="abrirModalEditar(
                                <?= $row['id']; ?>,
                                '<?= $row['imagen']; ?>',
                                '<?= htmlspecialchars($row['nombre'], ENT_QUOTES) ?>'
                            )"
                            class="px-3 py-1 rounded-lg bg-yellow-400 hover:bg-yellow-500 transition">

                            <i data-lucide="file-image"></i>
                        </button>
                    </td>

                    <!-- Eliminar categoria -->
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

<!-- Modal eliminar categoria -->
<div id="modalEliminar" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white p-6 rounded-lg shadow-xl w-80 text-center">

        <h2 class="text-xl font-semibold mb-4">Eliminar categoría</h2>
        <p class="text-gray-700 mb-2">
            ¿Seguro que deseas eliminar esta categoría?
        </p>

        <p class="block text-sm font-medium text-red-600 mb-6">
            *ADVERTENCIA: Al eliminar una categoría, se eliminan TODOS los platillos del menú asociados a la categoría!
        </p>

        <div class="flex justify-around">
            <button onclick="confirmarEliminar()" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Sí, eliminar
            </button>

            <button onclick="cerrarModal()" 
                class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                Cancelar
            </button>
        </div>

    </div>
</div>

<!-- Modal Editar Categoria -->
<div id="modalEditar" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white p-6 rounded-lg shadow-xl w-96">

        <h2 class="text-xl font-semibold mb-4 text-center">
            Cambiar imagen de la categoría
        </h2>

        <!-- FORM -->
        <form id="formEditarImagen" method="POST" enctype="multipart/form-data">

            <!-- ID de la categoría -->
            <input type="hidden" name="categoria_id" id="categoria_id">

            <!-- Imagen actual -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Imagen actual
                </label>

                <img id="imagenActual"
                     src=""
                     alt="Imagen actual"
                     class="w-full h-40 object-cover rounded-lg border">
            </div>

            <!-- Nueva imagen -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Nueva imagen
                </label>

                <input
                    type="file"
                    name="imagen"
                    accept="image/*"
                    required
                    class="block w-full text-sm text-gray-700
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-lg file:border-0
                           file:text-sm file:font-semibold
                           file:bg-emerald-400 file:text-white
                           hover:file:bg-emerald-600
                           cursor-pointer">
            </div>

            <!-- Mensaje -->
            <p id="editMensaje" class="text-sm mb-4 text-red-600"></p>

            <!-- Botones -->
            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="cerrarModalEditar()"
                        class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>

                <button type="submit"
                        class="bg-sky-900 text-white px-4 py-2 rounded-lg hover:bg-sky-950">
                    Guardar
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Activar iconos Lucide -->
<script src="https://unpkg.com/lucide-icons/dist/umd/lucide.js"></script>
<script> lucide.createIcons(); </script>

<!-- JavaScript para búsqueda -->
<script>
const searchInput = document.getElementById("searchInput");
const tabla = document.getElementById("tablaCategorias").getElementsByTagName("tbody")[0];

function actualizarTabla() {
    const busqueda = searchInput.value.toLowerCase();

    for (let row of tabla.rows) {
        const nombre = row.querySelector(".nombre").textContent.toLowerCase();

        const coincideBusqueda =
            nombre.includes(busqueda);

        row.style.display = (coincideBusqueda) ? "" : "none";
    }
}

searchInput.addEventListener("input", actualizarTabla);
</script>


<!-- JavaScript para eliminar categoría -->
<script>
let idAEliminar = null;

function cerrarModal() {
    document.getElementById("modalEliminar").classList.add("hidden");
}

function abrirModal(id) {
    idAEliminar = id;
    document.getElementById("modalEliminar").classList.remove("hidden");
}

async function confirmarEliminar() {
    try {
        const response = await fetch("/ProyectoIngWeb/admin/includes/deleteCategoryProcess.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${encodeURIComponent(idAEliminar)}`
        });

        const data = await response.json();

        if (data.status === 0) {
            // Recargar tabla
            location.reload();
        }
        else if (data.status === 1) {
            alert("La categoría no existe.");
        }
        else if (data.status === -2) {
            alert("ID que se quiere eliminar no válido.");
        }
        else {
            alert("Error inesperado en el servidor.");
        }

    } catch (error) {
        console.error(error);
        alert("Error de conexión con el servidor.");
    }

    cerrarModal();
}

// Asignar evento a todos los botones de eliminar
document.querySelectorAll(".deleteBtn").forEach(btn => {
    btn.addEventListener("click", () => {
        abrirModal(btn.dataset.id);
    });
});
</script>

<!-- JavaScript para editar imagen -->
<script>    
    function cerrarModalEditar() {
        document.getElementById("modalEditar").classList.add("hidden");
    }

    function abrirModalEditar(id, imagenRuta) {
        document.getElementById("categoria_id").value = id;
        document.getElementById("imagenActual").src = imagenRuta;
        document.getElementById("editMensaje").innerHTML = "";
        document.getElementById("modalEditar").classList.remove("hidden");
    }

    //AJAX para guardar los cambios
    document.addEventListener("DOMContentLoaded", () => {

        const formEditar = document.getElementById("formEditarImagen");
        if (!formEditar) return;

        formEditar.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(formEditar);

            try {
                const res = await fetch(
                    "/ProyectoIngWeb/admin/includes/changeCategoryImageProcess.php",
                    {
                        method: "POST",
                        body: formData
                    }
                );

                const data = await res.json();

                if (data.status === 0) {
                    location.reload();
                } else {
                    document.getElementById("editMensaje").textContent = data.error;
                }

            } catch (err) {
                document.getElementById("editMensaje").textContent =
                    "Error al comunicarse con el servidor.";
            }
        });

    });
</script>