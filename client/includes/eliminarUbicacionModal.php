<div id="eliminarUbicacionModal"
     class="fixed inset-0 bg-black bg-opacity-50
            hidden flex items-center justify-center z-50">

  <div class="bg-white rounded-xl p-6 w-96 text-center shadow-lg">

    <h2 class="text-xl font-bold text-red-600 mb-4">
      Eliminar ubicación
    </h2>

    <p class="text-gray-700 mb-6">
      ¿Seguro que deseas eliminar esta ubicación?<br>
      <span class="text-sm text-gray-500">
        Esta acción no se puede deshacer.
      </span>
    </p>

    <div class="flex justify-center gap-4">
      <button
        onclick="cerrarEliminarUbicacion()"
        class="px-4 py-2 rounded-lg
               border border-gray-300
               hover:bg-gray-100 transition">
        Cancelar
      </button>

      <button
        onclick="confirmarEliminarUbicacion()"
        class="px-4 py-2 rounded-lg
               bg-red-600 hover:bg-red-700
               text-white transition">
        Eliminar
      </button>
    </div>

  </div>
</div>

<script>
let ubicacionAEliminar = null;

function abrirEliminarUbicacion(id) {
    ubicacionAEliminar = id;
    document
      .getElementById("eliminarUbicacionModal")
      .classList.remove("hidden");
}

function cerrarEliminarUbicacion() {
    ubicacionAEliminar = null;
    document
      .getElementById("eliminarUbicacionModal")
      .classList.add("hidden");
}

async function confirmarEliminarUbicacion() {
    if (!ubicacionAEliminar) return;

    try {
        const res = await fetch(
            "/ProyectoIngWeb/client/includes/eliminarUbicacionProcess.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `ubi_id=${encodeURIComponent(ubicacionAEliminar)}`
            }
        );

        const data = await res.json();

        if (data.status === 0) {
            location.reload();
        } else {
            alert("No se pudo eliminar la ubicación.");
        }

    } catch (err) {
        console.error(err);
        alert("Error al contactar al servidor.");
    }
}
</script>