<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-emerald-800">

<main class="flex justify-center items-center min-h-screen">
    <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-md">

        <h1 class="text-2xl font-bold mb-6 text-center">Cambiar contraseña</h1>

        <label class="block text-sm font-medium">Contraseña actual</label>
        <input type="password" id="currentPass"
            class="mt-1 w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring mb-4">

        <label class="block text-sm font-medium">Nueva contraseña</label>
        <input type="password" id="newPass"
            class="mt-1 w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring mb-4">

        <label class="block text-sm font-medium">Confirmar nueva contraseña</label>
        <input type="password" id="confirmPass"
            class="mt-1 w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring mb-4">

        <p id="msg" class="text-red-600 text-sm mb-2"></p>

        <button onclick="actualizarPassword()"
            class="w-full bg-emerald-400 text-white py-2 rounded-lg hover:bg-emerald-950 transition">
            Guardar cambios
        </button>

        <p class="mt-6 text-center">
            <a href="profile.php"
               class="text-emerald-900 hover:underline text-sm">Cancelar</a>
        </p>

    </div>
</main>

<!-- Modal -->
<div id="modalPass"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl text-center w-80">
        <h2 class="text-xl font-semibold mb-4">Contraseña actualizada</h2>
        <p class="mb-4">Tu contraseña ha sido cambiada correctamente.</p>

        <button onclick="redirigirLogin()"
            class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800">
            Continuar
        </button>
    </div>
</div>

<script>
async function actualizarPassword() {
    const currentPass = document.getElementById("currentPass").value;
    const newPass = document.getElementById("newPass").value;
    const confirmPass = document.getElementById("confirmPass").value;
    const msg = document.getElementById("msg");

    if (!currentPass || !newPass || !confirmPass) {
        msg.innerHTML = "Todos los campos son obligatorios.";
        return;
    }

    if (newPass !== confirmPass) {
        msg.innerHTML = "Las contraseñas no coinciden.";
        return;
    }

    const email = <?php echo json_encode($_SESSION['email']); ?>;

    try {
        const response = await fetch("../../includes/changePasswordProcess.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `email=${encodeURIComponent(email)}&current=${encodeURIComponent(currentPass)}&newPass=${encodeURIComponent(newPass)}`
        });

        const data = await response.json();

        if (data.status === 0) {
            document.getElementById("modalPass").classList.remove("hidden");
        } else {
            msg.innerHTML = data.error;
        }

    } catch (error) {
        console.error(error);
        msg.innerHTML = "Error de servidor.";
    }
}

function redirigirLogin() {
    window.location.href = "/ProyectoIngWeb/includes/logout.php";
}
</script>

</body>
</html>