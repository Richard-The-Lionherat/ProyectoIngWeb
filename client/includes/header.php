<header class="bg-black py-4">
    <div class="px-4 grid grid-cols-[80px_1fr] gap-4 items-center text-white">
        
        <!-- LOGO -->
        <div class="flex items-center">
            <a href="/ProyectoIngWeb/client/dashboard.php" class="flex items-center gap-2">
                <img src="/ProyectoIngWeb/images/isotipo32x32.png" 
                     alt="JAV-A Coffee Logo" 
                     class="w-10 h-10">
            </a>
        </div>

        <!-- NAV + USER -->
        <div class="grid grid-cols-[1fr_auto] items-center gap-8">

            <!-- NAVIGATION -->
            <nav class="flex items-center gap-8 text-white">

              <!-- NAVEGACIÓN PRINCIPAL -->
              <div class="flex gap-6">
                <a href="/ProyectoIngWeb/sobre_nosotros.php"
                  class="px-4 py-2 hover:underline hover:font-bold">
                  Sobre Nosotros
                </a>

                <a href="/ProyectoIngWeb/client/pages/menu.php"
                  class="px-4 py-2 hover:underline hover:font-bold">
                  Menú
                </a>
              </div>

              <!-- ACCIONES -->
              <div class="flex gap-4">
                <!-- Carrito -->
                <a href="/ProyectoIngWeb/client/pages/carrito.php"
                  class="border border-white px-4 py-2 rounded-lg flex items-center gap-2
                          hover:bg-white hover:text-black transition">
                  <i data-lucide="shopping-cart"></i>
                  Carrito
                </a>

                <!-- Pedidos -->
                <a href="/ProyectoIngWeb/mis_pedidos.php"
                  class="border border-white px-4 py-2 rounded-lg flex items-center gap-2
                          hover:bg-white hover:text-black transition">
                  <i data-lucide="package"></i>
                  Mis pedidos
                </a>

                <!-- Ubicación -->
                <a href="/ProyectoIngWeb/client/pages/ubicacion.php"
                  class="border border-white px-4 py-2 rounded-lg flex items-center gap-2
                          hover:bg-white hover:text-black transition">
                  <i data-lucide="map-pin"></i>
                  Punto de entrega
                </a>

                <!-- MÉTODOS DE PAGO (NUEVO) -->
                <a href="/ProyectoIngWeb/client/pages/metodosPago.php"
                  class="border border-white px-4 py-2 rounded-lg flex items-center gap-2
                          hover:bg-white hover:text-black transition">
                  <i data-lucide="hand-coins"></i>
                  Métodos de pago
                </a>
              </div>

            </nav>

            <!-- USER + LOGOUT -->
            <div class="flex items-center gap-4">
                <a href="/ProyectoIngWeb/client/pages/profile.php">
                    <p class="text-white px-4 py-2 hover:underline hover:font-bold">
                        <?= $_SESSION['nombre'] ?? 'Usuario'; ?>
                    </p>
                </a>

                <button onclick="openLogoutModal()"
                    class="border border-white bg-white text-black px-4 py-2 rounded-lg 
                           hover:bg-black hover:text-white transition">
                    Cerrar Sesión
                </button>
            </div>
        </div>

    </div>
</header>