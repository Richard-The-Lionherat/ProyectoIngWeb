<?php
header('Content-Type: application/json');
session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/ProyectoIngWeb/includes/connection.php';

if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'C') {
    echo json_encode(["status" => -1, "error" => "No autorizado"]);
    exit;
}

$email  = $_SESSION['email'];
$accion = $_POST['accion'] ?? '';

/* ================= UBICACIÓN ACTUAL (GPS) ================= */
if ($accion === 'actual') {

    $latitud  = $_POST['lat'] ?? null;
    $longitud = $_POST['lng'] ?? null;

    if ($latitud === null || $longitud === null) {
        echo json_encode(["status" => 1, "error" => "Coordenadas inválidas"]);
        exit;
    }

    $alias = $_POST['alias'] ?? '';

    $alias = trim($alias);
    if ($alias === '') {
        $stmt = $conn->prepare("
            SELECT COUNT(*) total
            FROM ubicaciones
            WHERE ubi_emailUsuario = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];

        $alias = "Ubicación " . ($total + 1);
    }

    $colonia   = "GPS";
    $ciudad    = "GPS";
    $direccion = "Ubicación obtenida automáticamente";

    $stmt = $conn->prepare(
        "CALL sp_agregarUbicacion(?, ?, ?, ?, ?, ?, ?, @res)"
    );

    $stmt->bind_param(
        "sssssdd",
        $email,
        $alias,
        $colonia,
        $ciudad,
        $direccion,
        $latitud,
        $longitud
    );

    $stmt->execute();

    $res = $conn->query("SELECT @res AS res")->fetch_assoc();

    if ($res['res'] == 1) {
        echo json_encode([
            "status" => 1,
            "error"  => "Esta ubicación ya existe"
        ]);
        exit;
    }

    echo json_encode(["status" => 0]);
    exit;
}

/* ================= USAR UBICACIÓN ================= */
if ($accion === 'usar') {
    $id = intval($_POST['id'] ?? 0);
    setcookie('ubicacion_id', $id, time() + (60 * 60 * 24 * 30), '/');
    echo json_encode(["status" => 0]);
    exit;
}

/* ================= MARCAR PREDETERMINADA ================= */
if ($accion === 'predeterminada') {
    $id = intval($_POST['id'] ?? 0);

    $stmt = $conn->prepare(
        "CALL sp_marcarUbicacionPredeterminada(?, ?, @res)"
    );
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();

    echo json_encode(["status" => 0]);
    exit;
}

/* ================= AGREGAR MANUAL ================= */
if ($accion === 'agregar') {

    $alias = $_POST['alias'] ?? '';

    $alias = trim($alias);
    if ($alias === '') {
        $stmt = $conn->prepare("
            SELECT COUNT(*) total
            FROM ubicaciones
            WHERE ubi_emailUsuario = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];

        $alias = "Ubicación " . ($total + 1);
    }

    $colonia   = $_POST['colonia'] ?? '';
    $ciudad    = $_POST['ciudad'] ?? '';
    $direccion = $_POST['direccion'] ?? '';

    if ($colonia === '' || $ciudad === '' || $direccion === '') {
        echo json_encode(["status" => 1, "error" => "Datos incompletos"]);
        exit;
    }

    $latitud  = null;
    $longitud = null;

    $stmt = $conn->prepare(
        "CALL sp_agregarUbicacion(?, ?, ?, ?, ?, ?, ?, @res)"
    );

    $stmt->bind_param(
        "sssssdd",
        $email,
        $alias,
        $colonia,
        $ciudad,
        $direccion,
        $latitud,
        $longitud
    );

    $stmt->execute();

    $res = $conn->query("SELECT @res AS res")->fetch_assoc();

    if ($res['res'] == 1) {
        echo json_encode([
            "status" => 1,
            "error"  => "Esta dirección ya está registrada"
        ]);
        exit;
    }

    echo json_encode(["status" => 0]);
    exit;
}

/* ================= PREPARAR UBICACIÓN (REVERSE GEOCODING) ================= */
if ($accion === 'preparar') {

    $lat = $_POST['lat'] ?? null;
    $lng = $_POST['lng'] ?? null;

    if ($lat === null || $lng === null) {
        echo json_encode(["status" => 1, "error" => "Coordenadas inválidas"]);
        exit;
    }

    $url = "https://nominatim.openstreetmap.org/reverse?" . http_build_query([
        'format' => 'json',
        'lat'    => $lat,
        'lon'    => $lng,
        'zoom'   => 18,
        'addressdetails' => 1
    ]);

    $opts = [
        "http" => [
            "header" => "User-Agent: JAV-A-COFFEE/1.0\r\n"
        ]
    ];

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        echo json_encode(["status" => 2, "error" => "No se pudo obtener la dirección"]);
        exit;
    }

    $data = json_decode($response, true);
    $addr = $data['address'] ?? [];

    echo json_encode([
        "status"    => 0,
        "direccion" => $data['display_name'] ?? '',
        "colonia"   => $addr['suburb'] ?? $addr['neighbourhood'] ?? '',
        "ciudad"    => $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? ''
    ]);
    exit;
}

/* ================= ACCIÓN NO VÁLIDA ================= */
echo json_encode(["status" => -2, "error" => "Acción no válida"]);
exit;