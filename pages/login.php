<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Jav-a Coffe | Inicia Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-emerald-800">

    <!-- Header -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/header.php'; ?>

    <!-- Conexión con MySQL -->
   <?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php'; ?>
    
    <!-- Contenido principal -->
    <main class="flex justify-center items-center min-h-screen -mt-20">
        <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Inicia Sesión</h1>

            <!-- Correo (Usuario) -->
            <label for="user" class="block text-sm font-medium">Ingresa tu email</label>
            <input type="text" id="user"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

            <!-- Contraseña -->
            <label for="pass" class="block text-sm font-medium">Ingresa tu contraseña</label>
            <input type="password" id="pass"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

            <!-- Campo para texto de verificación -->
            <p class="block text-sm font-medium text-red-500" id="exito"></p>

            <!-- Olvidé mi contraseña (Todavía no sé como hacerlo funcionar) -->
            <div class="flex justify-between items-center mb-6">
                <a href="/ProyectoIngWeb/pages/forgotPassword.php" class="text-sm text-emerald-900 hover:underline font-bold">Olvidé mi contraseña</a>
            </div>

            <!-- Botón principal -->
            <button class="w-full bg-emerald-400 text-white py-2 rounded-lg hover:bg-emerald-950 transition" onclick="verifyLogin()">
                Iniciar Sesión
            </button>

            <!-- Enlace a Crear Usuario -->
            <p class="mt-6 text-center text-sm">¿Eres nuevo?
                <a href="createuser.php"><button
                        class="ml-2 px-3 py-1 border border-emerald-900 font-bold text-emerald-900 bg-transparent rounded-lg hover:bg-emerald-100 transition text-sm">Únete
                        ahora</button></a>
            </p>
        </div>
    </main>

<script>
async function verifyLogin() {

    const email = document.getElementById("user").value;
    const pass = document.getElementById("pass").value;
    const mensaje = document.getElementById("exito");

    const response = await fetch("../includes/loginProcess.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(pass)}`
    });

    const data = await response.json();

    console.log(data);

    if (data.status === 0) {
        // El Login ya fue correcto y redirige con el tipo de usuario
        mensaje.classList.remove("text-red-600");
        mensaje.classList.add("text-green-600");
        mensaje.innerHTML = "Usuario aprobado.";
        if (data.tipo === "A") {
            window.location.href = "/ProyectoIngWeb/admin/layout.php";
        } else if (data.tipo === "C") {
            window.location.href = "/ProyectoIngWeb/client/dashboard.php";
        } else {
            window.location.href = "/ProyectoIngWeb/employee/dashboard.php";
        }
    }
    else if (data.status === 1) {
        mensaje.innerHTML = "No existe un usuario registrado con ese correo.";
    }
    else if (data.status === 2) {
        mensaje.innerHTML = "Contraseña incorrecta.";
    }
    else {
        mensaje.innerHTML = "Error inesperado en el servidor.";
    }
};
</script>

</body>

</html>