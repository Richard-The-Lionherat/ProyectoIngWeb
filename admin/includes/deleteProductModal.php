<div id="modalEliminar" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white p-6 rounded-lg shadow-xl w-80 text-center">

        <h2 class="text-xl font-semibold mb-6">Eliminar producto</h2>
        <p class="text-gray-700 mb-2">
            ¿Seguro que deseas eliminar este producto?
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
        const response = await fetch("/ProyectoIngWeb/admin/includes/deleteProductProcess.php", {
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
            alert("ID que se quiere eliminar no válido.");
        }
        else if (data.status === 2) {
            alert("El producto no existe.");
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
</script>