<div id="modalMetodoPago"
     class="hidden fixed inset-0 bg-black/50
            flex items-center justify-center z-50">

  <div class="bg-white rounded-xl shadow-xl
              p-6 w-full max-w-md">

    <!-- TÍTULO -->
    <h2 class="flex items-center gap-2
               text-xl font-bold text-emerald-900 mb-4">
      <i data-lucide="credit-card"></i>
      Agregar método de pago
    </h2>

    <!-- FORM -->
    <form id="formMetodoPago" class="space-y-4">

      <!-- ALIAS -->
      <div>
        <label class="block text-sm font-medium text-gray-700">
          Alias
        </label>
        <input
          name="alias"
          placeholder="Ej. Mi tarjeta principal"
          class="w-full px-3 py-2 border border-gray-300
                 rounded-lg focus:outline-none
                 focus:ring focus:ring-emerald-400">
      </div>

      <!-- TIPO -->
      <div>
        <label class="block text-sm font-medium text-gray-700">
          Tipo de método
        </label>
        <select
          name="tipo"
          class="w-full px-3 py-2 border border-gray-300
                 rounded-lg focus:outline-none
                 focus:ring focus:ring-emerald-400">
          <option value="">Selecciona una opción</option>
          <option value="TARJETA">Tarjeta</option>
          <option value="TRANSFERENCIA">Transferencia</option>
        </select>
      </div>

      <!-- MENSAJE -->
      <p id="mensajeMetodoPago"
         class="text-sm text-red-600"></p>

      <!-- BOTONES -->
      <div class="flex justify-end gap-3 pt-4">

        <button type="button"
                onclick="closeMetodoPagoModal()"
                class="px-4 py-2 rounded-lg
                       border border-gray-300
                       text-gray-700
                       hover:bg-gray-100 transition">
          Cancelar
        </button>

        <button type="button"
                onclick="guardarMetodoPago()"
                class="px-5 py-2 rounded-lg
                       bg-emerald-600 text-white
                       hover:bg-emerald-800 transition">
          Guardar
        </button>

      </div>

    </form>

  </div>
</div>