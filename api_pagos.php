<?php
session_start();
require_once 'admin/includes/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

$action = isset($_GET['action']) ? $_GET['action'] : '';

// -------------------------------------------------------
// ENDPOINT: Verificar si el usuario ya pagó por un diseño
// -------------------------------------------------------
if ($action === 'check') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['has_paid' => false]);
        exit;
    }
    $user_id   = (int)$_SESSION['user_id'];
    $diseno_id = (int)($_GET['diseno_id'] ?? 0);

    if ($diseno_id === 0) {
        echo json_encode(['has_paid' => false]);
        exit;
    }

    $query  = "SELECT id_compra FROM compras 
               WHERE id_usuario = $user_id 
                 AND id_diseno  = $diseno_id 
                 AND estado_pago = 'completado'
               LIMIT 1";
    $result = mysqli_query($conn, $query);

    // Si la query falla (tabla no existe, etc.) devolvemos false por seguridad
    if ($result === false) {
        echo json_encode(['has_paid' => false, 'db_error' => mysqli_error($conn)]);
        exit;
    }

    echo json_encode(['has_paid' => mysqli_num_rows($result) > 0]);
    exit;
}

// -------------------------------------------------------
// ENDPOINT: Capturar y verificar el pago real de PayPal
// -------------------------------------------------------
if ($action === 'capture' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'No autorizado']);
        exit;
    }

    $data      = json_decode(file_get_contents('php://input'), true);
    $order_id  = $data['orderID']   ?? '';
    $diseno_id = (int)($data['diseno_id'] ?? 0);
    $user_id   = (int)$_SESSION['user_id'];

    if (!$order_id || $diseno_id === 0) {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }

    // ── Credenciales PayPal ─────────────────────────────────────────────────
    // Las claves de PayPal ahora se cargan desde config.php
    // (el cual ya fue incluido al principio del archivo mediante db.php)
    // ────────────────────────────────────────────────────────────────────────

    $base_url = (PAYPAL_MODE === 'live')
        ? 'https://api-m.paypal.com'
        : 'https://api-m.sandbox.paypal.com';

    // 1. Obtener Access Token de PayPal
    $ch = curl_init("$base_url/v1/oauth2/token");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_USERPWD        => PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET,
        CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $token_response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    $access_token = $token_response['access_token'] ?? null;
    if (!$access_token) {
        echo json_encode(['success' => false, 'error' => 'No se pudo autenticar con PayPal']);
        exit;
    }

    // 2. Capturar la orden (captura real del dinero)
    $ch = curl_init("$base_url/v2/checkout/orders/$order_id/capture");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            "Content-Type: application/json",
            "Authorization: Bearer $access_token",
        ],
        CURLOPT_POSTFIELDS => '{}',
    ]);
    $capture_response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    // 3. Verificar que el pago fue COMPLETADO
    if (($capture_response['status'] ?? '') === 'COMPLETED') {
        // El pago es real y confirmado. Guardar en base de datos.
        $query = "INSERT INTO compras (id_usuario, id_diseno, estado_pago)
                  VALUES ($user_id, $diseno_id, 'completado')
                  ON DUPLICATE KEY UPDATE estado_pago = 'completado'";
        mysqli_query($conn, $query);

        echo json_encode(['success' => true]);
    } else {
        $status = $capture_response['status'] ?? 'UNKNOWN';
        echo json_encode(['success' => false, 'error' => "Estado de pago inesperado: $status"]);
    }
    exit;
}

echo json_encode(['error' => 'Acción no reconocida']);
?>
