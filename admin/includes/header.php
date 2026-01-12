<header class="bg-sky-950 py-4">
    <div class="px-2 grid grid-cols-[80px_1fr] gap-16 items-center text-white">
      <!-- Logo -->
      <div>
        <a href="/ProyectoIngWeb/admin/layout.php">
          <img src="\ProyectoIngWeb\images\imagotipoHorizontal365x81.png" 
           alt="JAV-A Coffee Logo" 
           class="w-px-144 h-px-32">
        </a>
      </div>
      <div class="flex items-center justify-end gap-4">

        <!-- Nombre y Cerrar Sesión -->
        <div class="flex gap-4">
          <a href="/ProyectoIngWeb/admin/pages/profile.php"><p class="text-white px-4 py-2 hover:underline hover:font-bold">
            <?php echo $_SESSION['nombre'] ?? 'Usuario'; ?>
          </p></a>
          <button 
            onclick="openLogoutModal()"
            class="border border-gray-200 bg-gray-200 text-black px-4 py-2 rounded-lg hover:bg-sky-950 transition hover:text-white">
            Cerrar Sesión
          </button>
        </div>
      </div>
    </div>
  </header>