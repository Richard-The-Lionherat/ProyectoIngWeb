<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    header("Location: /ProyectoIngWeb/pages/login.php");
    exit;
}

if (
    !isset($_SESSION['carrito']) ||
    !is_array($_SESSION['carrito']) ||
    count($_SESSION['carrito']) === 0
) {
    header("Location: /ProyectoIngWeb/client/pages/carrito.php");
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

$cantidadUbicaciones = $ubicaciones->num_rows;

$ubicacionPredeterminada = null;

if ($cantidadUbicaciones > 0) {
    $ubicaciones->data_seek(0);
    while ($u = $ubicaciones->fetch_assoc()) {
        if ($u['ubi_predeterminada']) {
            $ubicacionPredeterminada = $u;
            break;
        }
    }
    $ubicaciones->data_seek(0); // reiniciamos para usarlo después
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Check Out | Jav-a Coffe</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-emerald-800">

  <!-- Header -->
  <?php include $_SERVER['DOCUMENT_ROOT'].'/ProyectoIngWeb/client/includes/header.php'; ?>

  <main class="flex justify-center items-start min-h-screen pt-24 pb-16">
    <div class="bg-gray-200 shadow-lg rounded-xl p-8 w-full max-w-3xl space-y-8">

      <!-- TÍTULO -->
        <h1 class="text-2xl font-bold text-center text-emerald-900 flex items-center justify-center gap-2">
            <i data-lucide="clipboard-check"></i>
            Confirmar pedido
        </h1>

      <!-- DIRECCIÓN -->

      <!-- No hay ubicaciones -->
        <?php if ($cantidadUbicaciones === 0): ?>

            <section class="bg-white rounded-xl p-4 shadow text-center">
            <h2 class="font-semibold text-emerald-900 flex items-center justify-center gap-2 mb-2">
                <i data-lucide="map-pin-x-inside"></i>
                Dirección de entrega
            </h2>

            <p class="text-gray-600 mb-4">
                No tienes ninguna dirección registrada
            </p>

            <a href="ubicacion.php"
                class="inline-block bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-800 transition">
                Agregar dirección
            </a>
            </section>

        <?php endif; ?>
        
        <!-- Hay ubicaciones -->
            <?php if ($cantidadUbicaciones > 0): ?>

            <section class="bg-white rounded-xl p-4 shadow">
            <h2 class="font-semibold text-emerald-900 flex items-center gap-2 mb-4">
                <i data-lucide="map"></i>
                Dirección de entrega
            </h2>

            <select
                id="ubicacion_id"
                onchange="validarCheckout()"
                name="ubicacion_id"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg
                    focus:outline-none focus:ring focus:ring-emerald-500">

                <?php
                $tienePredeterminada = false;
                while ($u = $ubicaciones->fetch_assoc()):
                    if ($u['ubi_predeterminada']) {
                        $tienePredeterminada = true;
                    }
                ?>
                <option
                    value="<?= $u['ubi_id'] ?>"
                    <?= $u['ubi_predeterminada'] ? 'selected' : '' ?>>

                    <?= htmlspecialchars($u['ubi_alias']) ?>
                    — <?= htmlspecialchars($u['ubi_colonia']) ?>, <?= htmlspecialchars($u['ubi_ciudad']) ?>

                    <?= $u['ubi_predeterminada'] ? '(Predeterminada)' : '' ?>
                </option>
                <?php endwhile; ?>

                <?php if (!$tienePredeterminada): ?>
                <option value="" selected disabled>
                    Selecciona una ubicación
                </option>
                <?php endif; ?>

            </select>

            <a href="ubicacion.php"
                class="inline-block mt-3 text-sm text-emerald-700 hover:underline">
                Administrar direcciones
            </a>
            </section>

        <?php endif; ?>

      <!-- RESUMEN -->
        <section class="bg-white rounded-xl p-4 shadow">
            <h2 class="font-semibold text-emerald-900 flex items-center gap-2 mb-4">
                <i data-lucide="shopping-cart"></i>
                Tu pedido
            </h2>

            <!-- Producto -->
             <?php
                $total = 0;

                foreach ($_SESSION['carrito'] as $id => $cantidad):

                    $producto = $conn->query("
                        SELECT nombre, precio
                        FROM productos_bebidas
                        WHERE id = $id
                    ")->fetch_assoc();

                    if (!$producto) continue;

                    $subtotal = $producto['precio'] * $cantidad;
                    $total += $subtotal;
            ?>

            <div class="flex justify-between text-sm mb-2">
                <span>
                    <?= htmlspecialchars($producto['nombre']) ?> × <?= $cantidad ?>
                </span>
                <span>
                    $<?= number_format($subtotal, 2) ?>
                </span>
            </div>

            <?php endforeach; ?>

            <!-- Total -->

            <div class="flex justify-between font-bold text-lg border-t pt-3 mt-3">
                <span>Total</span>
                <span>$<?= number_format($total, 2) ?></span>
            </div>

        </section>

      <!-- MÉTODO DE PAGO -->
        <section class="bg-white rounded-xl p-4 shadow">
            <h2 class="font-semibold text-emerald-900 flex items-center gap-2 mb-4">
                <i data-lucide="credit-card"></i>
                Método de pago
            </h2>

            <div class="space-y-3 text-sm">

                <label class="flex items-center gap-2">
                <input type="radio" name="metodo_pago" value="Tarjeta" onchange="validarCheckout()">
                Tarjeta
                </label>

                <label class="flex items-center gap-2">
                <input type="radio" name="metodo_pago" value="Transferencia" onchange="validarCheckout()">
                Transferencia
                </label>

                <label class="flex items-center gap-2">
                <input type="radio" name="metodo_pago" value="Efectivo" onchange="validarCheckout()">
                Efectivo
                </label>

            </div>
        </section>

        <!-- Error -->
        <p id="errorUbicacion"
            class="text-red-600 text-sm mt-2">
        </p>

      <!-- CONFIRMAR -->
        <button
            id="btnConfirmar"
            disabled
            onclick="continuarCheckout()"
            class="w-full bg-emerald-600 text-white py-3 rounded-lg
                font-semibold flex items-center justify-center gap-2
                transition
                opacity-50 cursor-not-allowed
                hover:bg-emerald-600">

            <i data-lucide="check-circle"></i>
            Confirmar pedido
        </button>

        <!-- Volver -->
         <div class="text-center mt-4 text-sm text-emerald-700 hover:underline">
            <a href="carrito.php"
                class="flex items-center justify-center gap-2">
                <i data-lucide="circle-chevron-left"></i>
                Volver al carrito
            </a>
        </div>


    </div>
  </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/client/includes/logoutModal.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/ProyectoIngWeb/client/includes/footer.php'; ?>

    <script>
        lucide.createIcons();
    </script>

    <script>
        function validarCheckout() {
            const ubicacion = document.getElementById("ubicacion_id");
            const metodoPago = document.querySelector('input[name="metodo_pago"]:checked');
            const boton = document.getElementById("btnConfirmar");

            if (ubicacion && ubicacion.value !== "" && metodoPago) {
                boton.disabled = false;
                boton.classList.remove("opacity-50", "cursor-not-allowed");
                boton.classList.add("hover:bg-emerald-800");
            } else {
                boton.disabled = true;
                boton.classList.add("opacity-50", "cursor-not-allowed");
                boton.classList.remove("hover:bg-emerald-800");
            }
        }

        function continuarCheckout() {
            const ubicacion = document.getElementById("ubicacion_id").value;
            const metodoPago = document.querySelector('input[name="metodo_pago"]:checked').value;

            document.cookie = `ubicacion_id=${ubicacion}; path=/`;
            document.cookie = `metodo_pago=${metodoPago}; path=/`;

            window.location.href = "confirmar.php";
        }

    </script>

</body>

</html>