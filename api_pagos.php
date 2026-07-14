<?php
session_start();

require_once __DIR__ . '/includes/services/PagoService.php';

header('Content-Type: application/json');

// CORS: permitir solo el dominio del sitio
$allowed_origins = ['http://localhost', 'http://localhost:8080', 'https://cardstudio.com'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$pagoService = new PagoService();

// -------------------------------------------------------
// Verificar si el usuario ya pagó por un diseño
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

    echo json_encode(['has_paid' => $pagoService->hasPaid($user_id, $diseno_id)]);
    exit;
}

// -------------------------------------------------------
// Capturar y verificar el pago real de PayPal
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

    $result = $pagoService->capturePayment($order_id, $diseno_id, $user_id);
    echo json_encode($result);
    exit;
}

echo json_encode(['error' => 'Acción no reconocida']);
?>
