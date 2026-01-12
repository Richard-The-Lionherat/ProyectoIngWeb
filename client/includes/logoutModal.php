<!-- Modal de confirmación de logout -->
<div id="logoutModal" 
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-black rounded-lg shadow-xl p-6 w-80 text-center">

        <h2 class="text-xl font-semibold mb-4 text-white">¿Cerrar sesión?</h2>
        <p class="text-gray-300 mb-6">¿Seguro que quieres salir de tu cuenta?</p>

        <div class="flex justify-around">
            <button 
                onclick="confirmLogout()"
                class="text-white border border-white px-4 py-2 rounded-lg hover:bg-white transition hover:text-black transition">
                Sí
            </button>

            <button 
                onclick="closeLogoutModal()"
                class="border border-white bg-white text-black px-4 py-2 rounded-lg hover:bg-black transition hover:text-white transition">
                No
            </button>
        </div>
    </div>
</div>

<script>
    function openLogoutModal() {
        document.getElementById("logoutModal").classList.remove("hidden");
    }

    function closeLogoutModal() {
        document.getElementById("logoutModal").classList.add("hidden");
    }

    function confirmLogout() {
        // Redirige al logout real
        window.location.href = "/ProyectoIngWeb/includes/logout.php";
    }
</script>