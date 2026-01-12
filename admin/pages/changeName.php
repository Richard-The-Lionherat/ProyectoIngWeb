<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Jav-a Coffe | Inicia Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-emerald-800">

    <!-- Conexión con MySQL -->
   <?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php'; ?>
    
    <!-- Contenido principal -->
    <main class="flex justify-center items-center min-h-screen -mt-20">
        <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Cambia tu nombre</h1>

            <!-- Nombre actual -->
            <label for="nombreNow" class="block text-sm font-medium">Tu nombre actual:</label>
            <p id="nombreNow"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">
                <?php echo $_SESSION['nombre'] ?? 'Usuario'; ?>
            </p>

            <!-- Nuevo nombre -->
            <label for="nombreNew" class="block text-sm font-medium">Ingresa tu nuevo nombre</label>
            <input type="text" id="nombreNew"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

            <!-- Campo para texto de verificación -->
            <p class="block text-sm font-medium text-red-500" id="exito"></p>

            <!-- Botón principal -->
            <button class="w-full bg-emerald-400 text-white py-2 rounded-lg hover:bg-emerald-950 transition" onclick="cambiarNombre()">
                Guardar los cambios
            </button>

            <!-- Cancelar -->
            <p class="mt-6 text-center text-sm">
                <a href="profile.php"><button
                        class="ml-2 px-3 py-1 border border-emerald-900 font-bold text-emerald-900 bg-transparent rounded-lg hover:bg-emerald-100 transition text-sm">
                        Cancelar</button></a>
            </p>
        </div>
    </main>

    <!-- Modal de confirmación de cambio de nombre -->
    <div id="cambiarNombreModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

        <div class="bg-black rounded-lg shadow-xl p-6 w-80 text-center">

            <h2 class="text-xl font-semibold mb-4 text-white">Listo</h2>
            <p class="text-gray-300 mb-6">Tu nombre se ha actualizado correctamente.</p>

            <div class="flex justify-around">
                <button 
                    onclick="salirActualizar()"
                    class="border border-white bg-white text-black px-4 py-2 rounded-lg hover:bg-black transition hover:text-white transition">
                    Ok
                </button>
            </div>
        </div>
    </div>

<script>
    function salirActualizar() {
        // Redirige al logout para refrescar los datos
        window.location.href = "/ProyectoIngWeb/includes/logout.php";
    }

    async function cambiarNombre() {
        const email = <?php echo json_encode($_SESSION['email'] ?? 'Usuario'); ?>;
        const newName = document.getElementById("nombreNew").value;
        const mensaje = document.getElementById("exito");

        if (newName.trim() === "") {
            mensaje.innerHTML = "No puedes dejar tu nombre vacío.";
            return false;
        }

        try {
            const response = await fetch("../../includes/changeNameProcess.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `email=${encodeURIComponent(email)}&newName=${encodeURIComponent(newName)}`
            });

            const data = await response.json();

            if (data.status === 0) {
                document.getElementById("cambiarNombreModal").classList.remove("hidden");
            } else {
                mensaje.innerHTML = "Error: " + data.error;
            }

        } catch (error) {
            console.error(error);
            mensaje.innerHTML = "No se pudo contactar al servidor.";
        }
    }
</script>

</body>

</html>