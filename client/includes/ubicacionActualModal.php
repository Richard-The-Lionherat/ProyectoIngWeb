<!-- MODAL UBICACIÓN ACTUAL -->
<div id="modalUbicacion"
     class="hidden fixed inset-0 z-50
            bg-black/50 backdrop-blur-sm
            flex items-center justify-center">

  <div class="bg-white rounded-xl shadow-xl
              w-full max-w-md p-6">

    <!-- TÍTULO -->
    <h2 class="flex items-center gap-2
               text-xl font-bold text-emerald-900 mb-4">
      <i data-lucide="map-pin"></i>
      Confirmar ubicación
    </h2>

    <p class="text-sm text-gray-600 mb-6">
      Verifica o ajusta los datos antes de guardar tu ubicación.
    </p>

    <!-- CAMPOS -->
    <div class="space-y-4">

      <div>
        <label class="block text-sm font-medium text-gray-700">
          Alias
        </label>
        <input
          id="alias"
          type="text"
          placeholder="Ejemplo: Casa, Trabajo, etc."
          class="mt-1 w-full px-3 py-2
                 border border-gray-300 rounded-lg
                 focus:outline-none focus:ring-2
                 focus:ring-emerald-500">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">
          Dirección
        </label>
        <input
          id="direccion"
          type="text"
          class="mt-1 w-full px-3 py-2
                 border border-gray-300 rounded-lg
                 focus:outline-none focus:ring-2
                 focus:ring-emerald-500">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">
          Colonia
        </label>
        <input
          id="colonia"
          type="text"
          class="mt-1 w-full px-3 py-2
                 border border-gray-300 rounded-lg
                 focus:outline-none focus:ring-2
                 focus:ring-emerald-500">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">
          Ciudad
        </label>
        <input
          id="ciudad"
          type="text"
          class="mt-1 w-full px-3 py-2
                 border border-gray-300 rounded-lg
                 focus:outline-none focus:ring-2
                 focus:ring-emerald-500">
      </div>

    </div>

    <!-- BOTONES -->
    <div class="flex justify-end gap-3 mt-6">

      <button
        onclick="closeUbicacionActualModal()"
        class="px-4 py-2 rounded-lg
               border border-gray-300
               text-gray-700
               hover:bg-gray-100 transition">
        Cancelar
      </button>

      <button
        onclick="guardarUbicacion()"
        class="flex items-center gap-2
               px-5 py-2 rounded-lg
               bg-emerald-600 text-white
               hover:bg-emerald-700 transition">

        <i data-lucide="save" class="w-4 h-4"></i>
        Guardar
      </button>

    </div>

  </div>
</div>

<script>
  function openUbicacionActualModal() {
        document.getElementById("modalUbicacion").classList.remove("hidden");
    }

    function closeUbicacionActualModal() {
        document.getElementById("modalUbicacion").classList.add("hidden");
    }
  
  async function guardarUbicacion() {
    const body = new URLSearchParams({
      accion: "agregar",
      alias: alias.value,
      direccion: direccion.value,
      colonia: colonia.value,
      ciudad: ciudad.value,
      lat: latActual,
      lng: lngActual
    });

    const res = await fetch("/ProyectoIngWeb/client/includes/ubicacionProcess.php", {
      method: "POST",
      body
    });

    const data = await res.json();

    if (data.status === 0) {
      location.reload(); // o recargar lista vía AJAX
    }
  }

</script>