<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-emerald-800">

<main class="flex justify-center items-center min-h-screen">
    <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-md">

        <h1 class="text-2xl font-bold mb-6 text-center">Recuperar contraseña</h1>

        <label for="email" class="block text-sm font-medium">Ingresa tu correo</label>
        <input type="email" id="email"
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

        <p id="mensaje" class="text-red-600 text-sm mb-2"></p>

        <button onclick="recuperar()"
            class="w-full bg-emerald-400 text-white py-2 rounded-lg hover:bg-emerald-950 transition">
            Recuperar contraseña
        </button>
    </div>
</main>

<!-- Modal -->
<div id="modalTempPass"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white rounded-lg shadow-xl p-6 w-80 text-center">
        <h2 class="text-xl font-semibold mb-4">Contraseña temporal</h2>
        <p class="mb-4">Usa esta contraseña para iniciar sesión:</p>

        <p id="tempPass" class="font-mono text-lg text-emerald-700 mb-6"></p>

        <button onclick="cerrarModal()"
            class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800">
            Entendido
        </button>
    </div>
</div>

<script>
async function recuperar() {
    const email = document.getElementById("email").value;
    const mensaje = document.getElementById("mensaje");

    if (email.trim() === "") {
        mensaje.innerHTML = "Ingresa un correo.";
        return;
    }

    try {
        const response = await fetch("../includes/forgotProcess.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `email=${encodeURIComponent(email)}`
        });

        const data = await response.json();

        if (data.status === 0) {
            document.getElementById("tempPass").innerText = data.tempPass;
            document.getElementById("modalTempPass").classList.remove("hidden");
            mensaje.innerHTML = "";
        } else {
            mensaje.innerHTML = data.error;
        }

    } catch (error) {
        console.error(error);
        mensaje.innerHTML = "Error inesperado.";
    }
}

function cerrarModal() {
    window.location.href = "/ProyectoIngWeb/pages/login.php";
}
</script>

</body>
</html>