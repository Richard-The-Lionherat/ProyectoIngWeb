<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Jav-a Coffe | Únete ahora</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-emerald-800">
  <!-- Header -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/header.php'; ?>

  <!-- Contenido principal -->
  <main class="flex justify-center items-center min-h-screen -mt-12">
    <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold mb-6 text-center">Crea tu cuenta</h1>

      <!-- Nombre -->
      <label for="name" class="block text-sm font-medium">Nombre completo</label>
      <input type="text" id="name"
        class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

      <!-- Email -->
      <label for="email" class="block text-sm font-medium">Correo</label>
      <input type="email" id="email"
        class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

      <!-- Contraseña -->
      <label for="password" class="block text-sm font-medium">Contraseña</label>
      <input type="password" id="password"
        class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-4">

      <!-- Confirmar contraseña -->
      <label for="confirm" class="block text-sm font-medium">Confirmar contraseña</label>
      <input type="password" id="confirm"
        class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-black mb-6">

      <!-- Campo para texto de verificación -->
      <p class="block text-sm font-medium text-red-500" id="exito"></p>

      <!-- Botón principal -->
      <button class="w-full bg-emerald-400 text-white py-2 rounded-lg hover:bg-emerald-950 transition" onclick="verifySignup()">
        Registrarse
      </button>

      <!-- Enlace a Login -->
      <p class="mt-6 text-center text-sm">¿Ya tienes una cuenta?
        <a href="login.php"><button
            class="ml-2 px-3 py-1 border border-emerald-900 font-bold text-emerald-900 bg-transparent rounded-lg hover:bg-emerald-100 transition text-sm">
            Inicia sesión
          </button></a>
      </p>
    </div>
  </main>

<script>
function verifySignup() {

    const emailInput = document.getElementById("email");
    const email = emailInput.value;

    const pass = document.getElementById("password").value;
    const confirm = document.getElementById("confirm").value;
    const mensaje = document.getElementById("exito");

    mensaje.innerHTML = "";

    // Validación de email no vacío
    if (email.trim() === "") {
        mensaje.innerHTML = "Ingresa un correo electrónico válido.";
        return false;
    }

    // Validación de email
    if (!emailInput.checkValidity()) {
        mensaje.innerHTML = "Ingresa un correo electrónico válido.";
        return false;
    }

    // Validacion de contraseñas iguales
    if (pass !== confirm) {
        mensaje.innerHTML = "Las contraseñas no coinciden.";
        return false;
    }

    // Validación de longitud mínima
    if (pass.length < 8) {
        mensaje.innerHTML = "La contraseña debe tener mínimo 8 caracteres.";
        return false;
    }

    // Validar mayúscula, minúscula y número
    const tieneMayus = /[A-Z]/.test(pass);
    const tieneMinus = /[a-z]/.test(pass);
    const tieneNumero = /\d/.test(pass);

    if (!tieneMayus || !tieneMinus || !tieneNumero) {
        mensaje.innerHTML = "La contraseña debe incluir mínimo una mayúscula, una minúscula y un número.";
        return false;
    }

    // Si todo está correcto
    mensaje.innerHTML = "Procesando...";
    registrarUsuario();
    return true;
}

async function registrarUsuario() {
    const email = document.getElementById("email").value;
    const nombre = document.getElementById("name").value;
    const password = document.getElementById("password").value;
    const tipo = "C";
    const mensaje = document.getElementById("exito");

    try {
        const response = await fetch("../includes/createClient.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `email=${encodeURIComponent(email)}&nombre=${encodeURIComponent(nombre)}&password=${encodeURIComponent(password)}&tipo=${encodeURIComponent(tipo)}`
        });

        const data = await response.json();

        if (data.status === 0) {
            // Registro exitoso
            mensaje.classList.remove("text-red-600");
            mensaje.classList.add("text-green-600");
            mensaje.innerHTML = "Usuario registrado correctamente.";
            window.location.href = "/ProyectoIngWeb/pages/login.php";
        }
        else if (data.status === 1) {
            // Correo duplicado
            mensaje.innerHTML = "El correo ya está registrado.";
        }
        else {
            mensaje.innerHTML = "Error inesperado en el servidor.";

        }

    } catch (error) {
        console.error("Error:", error);
        mensaje.innerHTML = "Error inesperado en el servidor.";
    }
}

</script>

</body>

</html>