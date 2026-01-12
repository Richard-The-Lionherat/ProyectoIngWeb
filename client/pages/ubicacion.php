<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$email = $conn->real_escape_string($_SESSION['email']);

$ubicaciones = $conn->query("
    SELECT *
    FROM ubicaciones
    WHERE ubi_emailUsuario = '$email'
    ORDER BY ubi_predeterminada DESC, ubi_creada DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Punto de entrega | JAV-A COFFEE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-emerald-800">

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/header.php'; ?>

<main class="flex justify-center items-start min-h-screen pt-24 pb-16">
  <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-3xl">

    <!-- TÍTULO -->
    <h1 class="flex items-center justify-center gap-2
        text-2xl font-bold mb-6 text-emerald-900">
        <i data-lucide="map-pinned"></i>Punto de entrega
    </h1>

    <!-- ================= LISTADO ================= -->
    <div id="listaUbicaciones" class="space-y-4 mb-12">

    <?php if ($ubicaciones->num_rows === 0): ?>
        <p class="text-center text-gray-600">
        No tienes ubicaciones guardadas
        </p>
    <?php endif; ?>

    <?php while ($u = $ubicaciones->fetch_assoc()): ?>

        <div class="bg-white rounded-xl p-4
                    flex justify-between items-start gap-4
                    shadow-sm hover:bg-emerald-50 transition">

        <div>
            <!-- Alias -->
            <p class="font-bold text-lg text-emerald-900">
            <?= htmlspecialchars($u['ubi_alias'] ?? 'Ubicación') ?>
            </p>

            <!-- Dirección -->
            <p class="text-sm text-gray-700">
            <?= htmlspecialchars($u['ubi_direccion']) ?>
            </p>

            <p class="text-xs text-gray-500">
            <?= htmlspecialchars($u['ubi_colonia']) ?>, <?= htmlspecialchars($u['ubi_ciudad']) ?>
            </p>

            <?php if ($u['ubi_predeterminada']): ?>
            <span class="inline-block mt-2 text-xs
                        flex items-center gap-2
                        bg-emerald-100 text-emerald-700
                        px-3 py-1 rounded-full">
                <i data-lucide="map-pin-check-inside" class="w-4 h-4"></i>
                Ubicacion predeterminada
            </span>
            <?php endif; ?>
        </div>

        <div class="flex flex-col gap-2 text-sm">
            <?php if (!$u['ubi_predeterminada']): ?>
            <button
                onclick="establecerPredeterminada(<?= $u['ubi_id'] ?>)"
                class="flex items-center gap-2
                    px-4 py-2 rounded-lg
                    bg-gray-200 hover:bg-emerald-100
                    text-emerald-900 transition">

                <i data-lucide="map-pin-check" class="w-4 h-4"></i>
                Establecer como predeterminada
            </button>

            <?php endif; ?>

            <button
                onclick="abrirEliminarUbicacion(<?= $u['ubi_id'] ?>)"
                class="flex items-center gap-2
                    px-4 py-2 rounded-lg
                    bg-red-600 hover:bg-red-700
                    text-white transition">

                <i data-lucide="map-pin-off" class="w-4 h-4"></i>
                Eliminar
            </button>

        </div>

        </div>

    <?php endwhile; ?>

    </div>

    <hr class="border-t border-gray-300 my-10">

    <!-- ================= AGREGAR ================= -->
    <h2 class="flex items-center gap-2
        text-xl font-bold mb-6 text-emerald-900">
    <i data-lucide="map-pin-plus"></i>
    Agregar nueva ubicación
    </h2>

    <!-- Botón GPS -->
    <div class="flex justify-center mb-6">
    <button
        onclick="usarUbicacionActual()"
        class="flex items-center gap-2
            bg-emerald-600 hover:bg-emerald-800
            text-white px-6 py-3 rounded-lg
            transition shadow">
        <i data-lucide="map-pin-plus-inside"></i>
        Usar mi ubicación actual
    </button>
    </div>

    <form id="formUbicacionManual" class="space-y-4">

    <input
        name="alias"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg"
        placeholder="Alias (Casa, Trabajo, etc.)">

    <input
        name="colonia"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg"
        placeholder="Colonia"
        required>

    <input
        name="ciudad"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg"
        placeholder="Ciudad"
        required>

    <textarea
        name="direccion"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg"
        placeholder="Dirección completa"
        required></textarea>

    <p class="block text-sm font-medium text-red-500" id="exito"></p>

    <button
        type="button"
        onclick="guardarUbicacionManual()"
        class="w-full bg-emerald-400 text-white py-2 rounded-lg
                hover:bg-emerald-950 transition">
        Guardar ubicación
    </button>

    </form>

    <!-- Menu Salir -->
    <div class="flex justify-center gap-4 mt-4">

    <a href="/ProyectoIngWeb/client/pages/carrito.php"
        class="flex items-center justify-center
                h-12 w-12
                border border-emerald-900
                text-emerald-900 rounded-lg
                hover:bg-emerald-100 transition">
        <i data-lucide="shopping-cart" class="w-6 h-6"></i>
    </a>

    <a href="/ProyectoIngWeb/client/dashboard.php"
        class="flex items-center justify-center
                h-12 px-6
                border border-emerald-900
                font-bold text-emerald-900 rounded-lg
                hover:bg-emerald-100 transition gap-2">
        <i data-lucide="circle-chevron-left"></i>
        Volver al inicio
    </a>

    </div>


  </div>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/ubicacionActualModal.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/eliminarUbicacionModal.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/logoutModal.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/footer.php'; ?>

<!-- Activar iconos Lucide -->
<script>
  lucide.createIcons();
</script>

<script>
    async function usarUbicacionActual() {

        if (!navigator.geolocation) {
            alert("Tu navegador no soporta ubicación.");
            return;
        }

        navigator.geolocation.getCurrentPosition(async pos => {

            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            const res = await fetch(
            "/ProyectoIngWeb/client/includes/ubicacionProcess.php",
            {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `accion=preparar&lat=${lat}&lng=${lng}`
            }
            );

            const data = await res.json();

            // Alias vacío (placeholder visible)
            document.getElementById("alias").value = "";

            // Datos automáticos (seguros)
            document.getElementById("direccion").value = data.direccion ?? "";
            document.getElementById("colonia").value   = data.colonia ?? "";
            document.getElementById("ciudad").value    = data.ciudad ?? "";

            // Guardar coords globales
            window.latActual = lat;
            window.lngActual = lng;

            openUbicacionActualModal();
        },
        () => alert("No se pudo obtener tu ubicación.")
        );
    }

    async function actualizarListaUbicaciones() {
        const res = await fetch(
            "/ProyectoIngWeb/client/includes/ubicacionesList.php"
        );
        const html = await res.text();

        document.getElementById("listaUbicaciones").innerHTML = html;

        lucide.createIcons(); // MUY importante
    }

    async function establecerPredeterminada(ubiId) {
        try {
            const res = await fetch(
                "/ProyectoIngWeb/client/includes/ubiPredeterminadaProcess.php",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `ubi_id=${encodeURIComponent(ubiId)}`
                }
            );

            const data = await res.json();

            if (data.status === 0) {
                location.reload();
            } else {
                alert("No se pudo establecer como predeterminada.");
            }

        } catch (err) {
            console.error(err);
            alert("Error al contactar al servidor.");
        }
    }

    async function guardarUbicacionManual() {
        const form = document.getElementById("formUbicacionManual");
        const mensaje = document.getElementById("exito");

        const formData = new FormData(form);
        formData.append("accion", "agregar");

        const alias = formData.get("alias")?.trim();
        if (!alias) {
            formData.set("alias", ""); // deja que PHP/SP genere "Ubicación 1"
        }

        try {
            const res = await fetch(
                "/ProyectoIngWeb/client/includes/ubicacionProcess.php",
                {
                    method: "POST",
                    body: new URLSearchParams(formData)
                }
            );

            const data = await res.json();

            if (data.status === 0) {
                location.reload();
            } else {
                mensaje.textContent = data.error || "Error al guardar";
            }

        } catch (e) {
            mensaje.textContent = "Error al contactar al servidor";
        }
    }

</script>

</body>

</html>