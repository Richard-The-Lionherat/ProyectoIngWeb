<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/ProyectoIngWeb/includes/connection.php";
?>

<!-- Título -->
<h1 class="text-3xl font-bold mb-6">Almacen de Ingredientes</h1>

<!-- Barra superior (Buscar + Crear ingrediente) -->
<div class="flex items-center justify-between mb-4">

    <!-- Buscar -->
    <input 
        type="text" 
        id="searchInput"
        placeholder="Buscar ingrediente..." 
        class="px-3 py-2 border rounded-lg w-1/3 focus:outline-none focus:ring focus:ring-sky-900"
    >

    <!-- Botón nuevo ingrediente -->
    <a href="/ProyectoIngWeb/admin/pages/createIngredient.php"
       class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
       <i data-lucide="package-plus"></i>
       Nuevo Ingrediente
    </a>
</div>

<!-- Tabla -->
<div class="overflow-x-auto">
    <table id="tablaIngredientes" class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
        <thead class="bg-sky-900 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Id del producto</th>
                <th class="py-3 px-4 text-left">Nombre</th>
                <th class="py-3 px-4 text-left">Unidad de medida</th>
                <th class="py-3 px-4 text-center">Cantidad disponible</th>
                <th class="py-3 px-4 text-center">Editar cantidad</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $query = "SELECT ING_id, ING_nombre, ING_unidadMedida, ING_cantidad FROM ingredientes";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()):
            ?>
                <tr class="border-b hover:bg-gray-100 transition">
                    <td class="py-2 px-4 id"><?php echo $row['ING_id']; ?></td>
                    <td class="py-2 px-4 nombre"><?php echo $row['ING_nombre']; ?></td>
                    <td class="py-2 px-4 medida"><?php echo $row['ING_unidadMedida']; ?></td>
                    <td class="py-2 px-4 cantidad"><?php echo $row['ING_cantidad']; ?></td>

                    <!-- Editar -->
                    <td class="py-2 px-4 text-center">
                        <button class="px-3 py-1 rounded-lg bg-yellow-400 hover:bg-yellow-500 transition editBtn"
                            data-id="<?php echo $row['ING_id']; ?>"
                            data-nombre="<?php echo $row['ING_nombre']; ?>"
                            data-medida="<?php echo $row['ING_unidadMedida']; ?>"
                            data-cantidad="<?php echo $row['ING_cantidad']; ?>"
                        >
                            <i data-lucide="pencil"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Editar Ingrediente -->
<div id="modalEditar" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white p-6 rounded-lg shadow-xl w-96 text-center">

        <h2 class="text-xl font-semibold mb-4">Editar Cantidad Manualmente</h2>

        <!-- Formulario -->
        <div class="mb-4 text-left">
            <label class="block text-sm mb-1">Cantidad Disponible:</label>
            <input type="number" step="0.001" min="0" id="editCantidad"
                   class="w-full border px-3 py-2 rounded-lg">
        </div>

        <div class="mb-4 text-left">
            <label class="block text-sm mb-1">Unidad de medida:</label>
            <input type="" id="editMedida"
                   class="w-full border px-3 py-2 rounded-lg">
        </div>

        <p id="editMensaje" class="text-red-600 mb-4 text-sm"></p>

        <div class="flex justify-around">
            <button onclick="guardarCambiosIngrediente()" 
                class="bg-sky-900 text-white px-4 py-2 rounded-lg hover:bg-sky-950">
                Guardar
            </button>

            <button onclick="cerrarModalEditar()" 
                class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">
                Cancelar
            </button>
        </div>
    </div>
</div>

<!-- Activar iconos Lucide -->
<script src="https://unpkg.com/lucide-icons/dist/umd/lucide.js"></script>
<script> lucide.createIcons(); </script>

<!-- JavaScript para búsqueda -->
<script>
const searchInput = document.getElementById("searchInput");
const tabla = document.getElementById("tablaIngredientes").getElementsByTagName("tbody")[0];

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

<script>
    
    let ingredienteActual = {
        id: "",
        medida: "",
        cantidad: ""
    };
    
    function cerrarModalEditar() {
        document.getElementById("modalEditar").classList.add("hidden");
    }

    function abrirModalEditar(btn) {
        ingredienteActual.id  = btn.dataset.id;
        ingredienteActual.medida = btn.dataset.medida;
        ingredienteActual.cantidad   = btn.dataset.cantidad;

        document.getElementById("editCantidad").value = ingredienteActual.cantidad;
        document.getElementById("editMedida").value   = ingredienteActual.medida;

        document.getElementById("modalEditar").classList.remove("hidden");
    }

    document.querySelectorAll(".editBtn").forEach(btn => {
        btn.addEventListener("click", () => abrirModalEditar(btn));
    });

    async function guardarCambiosIngrediente() {
        let nuevaCantidad = parseFloat(document.getElementById("editCantidad").value) || 0;
        const nuevaMedida   = document.getElementById("editMedida").value;

        const mensaje = document.getElementById("editMensaje");
        mensaje.innerHTML = "";

        if (nuevaMedida.trim() === "") {
            mensaje.innerHTML = "La unidad de medida no puede estar vacía.";
            return;
        }

        try {
            const response = await fetch("/ProyectoIngWeb/admin/includes/changeIngredientProcess.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: 
                    `id=${encodeURIComponent(ingredienteActual.id)}` +
                    `&cantidad=${encodeURIComponent(nuevaCantidad)}` +
                    `&medida=${encodeURIComponent(nuevaMedida)}`
            });

            const data = await response.json();

            if (data.status === 0) {
                location.reload(); // recargar tabla
            } else {
                mensaje.innerHTML = "Error: " + data.error;
            }

        } catch (error) {
            mensaje.innerHTML = "Error de conexión con el servidor.";
            console.error(error);
        }
    }
</script>