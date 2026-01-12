<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

$email = $_SESSION['email'];

$ubicaciones = $conn->query("
    SELECT *
    FROM ubicaciones
    WHERE ubi_emailUsuario = '$email'
    ORDER BY ubi_predeterminada DESC, ubi_creada DESC
");
?>

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
        <p class="font-bold text-lg text-emerald-900">
            <?= htmlspecialchars($u['ubi_alias'] ?? 'Ubicación') ?>
        </p>

        <p class="text-sm text-gray-700">
            <?= htmlspecialchars($u['ubi_direccion']) ?>
        </p>

        <p class="text-xs text-gray-500">
            <?= htmlspecialchars($u['ubi_colonia']) ?>,
            <?= htmlspecialchars($u['ubi_ciudad']) ?>
        </p>

        <?php if ($u['ubi_predeterminada']): ?>
            <span class="inline-block mt-2 text-xs
                         flex items-center gap-2
                         bg-emerald-100 text-emerald-700
                         px-3 py-1 rounded-full">
                <i data-lucide="map-pin-check-inside" class="w-4 h-4"></i>
                Ubicación predeterminada
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