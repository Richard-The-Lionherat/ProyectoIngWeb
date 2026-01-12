<?php 
session_start();

if ($_SESSION['tipo'] !== 'A') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Panel de Administración | Ingredientes</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-emerald-800">

  <!-- Contenido principal -->
  <main class="flex justify-center items-center min-h-screen -mt-12">
    <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold mb-6 text-center">Agregar un ingrediente al inventario</h1>

      <!-- Nombre -->
      <label for="name" class="block text-sm font-medium">Nombre del ingrediente</label>
      <input type="text" id="name"
        class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

      <!-- Unidad de medida -->
      <label for="meassure" class="block text-sm font-medium">Unidad de medida</label>
      <input type="text" id="meassure"
        class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

      <!-- Cantidad disponible -->
      <label for="quantity" class="block text-sm font-medium">Cantidad disponible ahora</label>
      <input type="number" step="0.001" min="0" id="quantity"
        class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

      <!-- Campo para texto de verificación -->
      <p class="block text-sm font-medium text-red-500" id="exito"></p>

      <!-- Botón principal -->
      <button class="w-full bg-emerald-400 text-white py-2 rounded-lg hover:bg-emerald-950 transition" onclick="agregarIngrediente()">
        Agregar ingrediente
      </button>

      <!-- Campo para texto de verificación -->
      <p class="block text-sm font-medium text-red-600" id="advertencia">
        *ADVERTENCIA: Una vez agregado el ingrediente, NO se puede eliminar!
      </p>

      <!-- BOTÓN VOLVER -->
      <a href="/ProyectoIngWeb/admin/layout.php?page=ingredients">
        <button class="mt-6 w-full bg-black text-white py-2 rounded-lg hover:bg-gray-900 transition">
              Volver
          </button>
      </a>
    </div>
  </main>

  <script>
    async function agregarIngrediente() {
        const nombre = document.getElementById("name").value.trim();
        const medida = document.getElementById("meassure").value.trim();
        let cantidad = document.getElementById("quantity").value;
        const mensaje = document.getElementById("exito");

        mensaje.innerHTML = "";
        mensaje.classList.remove("text-green-600", "text-red-600");

        // Validaciones
        if (nombre === "") {
            mensaje.classList.add("text-red-600");
            mensaje.innerHTML = "El nombre del ingrediente no puede estar vacío.";
            return;
        }

        if (medida === "") {
            mensaje.classList.add("text-red-600");
            mensaje.innerHTML = "La unidad de medida no puede estar vacía.";
            return;
        }

        // Si cantidad está vacía → 0
        if (cantidad === "" || cantidad === null) {
            cantidad = 0;
        }

        if (isNaN(cantidad) || Number(cantidad) < 0) {
            mensaje.classList.add("text-red-600");
            mensaje.innerHTML = "La cantidad debe ser un número válido mayor o igual a 0.";
            return;
        }

        try {
            const response = await fetch("../includes/createIngredientProcess.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body:
                    `nombre=${encodeURIComponent(nombre)}` +
                    `&medida=${encodeURIComponent(medida)}` +
                    `&cantidad=${encodeURIComponent(cantidad)}`
            });

            const data = await response.json();

            if (data.status === 0) {
                mensaje.classList.add("text-green-600");
                mensaje.innerHTML = "Ingrediente agregado correctamente.";

                // Limpiar campos
                document.getElementById("name").value = "";
                document.getElementById("meassure").value = "";
                document.getElementById("quantity").value = "";
            }
            else if (data.status === 1) {
                mensaje.classList.add("text-red-600");
                mensaje.innerHTML = "El ingrediente ya está registrado.";
            }
            else if (data.status === 2) {
                mensaje.classList.add("text-red-600");
                mensaje.innerHTML = "Datos incompletos.";
            }
            else {
                mensaje.classList.add("text-red-600");
                mensaje.innerHTML = "Error inesperado en el servidor.";
            }

        } catch (error) {
            console.error(error);
            mensaje.classList.add("text-red-600");
            mensaje.innerHTML = "No se pudo contactar al servidor.";
        }
    }
  </script>

</body>

</html>