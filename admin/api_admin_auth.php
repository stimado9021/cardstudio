<?php
// ================================================================
//  admin/api_admin_auth.php
//  Gestión de sesión del administrador
// ================================================================
session_name('cardstudio_admin');
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/services/AuthService.php';

$action = $_GET['action'] ?? '';
$authService = new AuthService();

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $usuario  = trim($data['usuario'] ?? '');
    $password = $data['password'] ?? '';

    if ($authService->loginAdmin($usuario, $password)) {
        echo json_encode(['success' => true]);
    } else {
        sleep(1);
        echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas']);
    }
    exit;
}

if ($action === 'check') {
    echo json_encode(['logged_in' => $authService->isAdminLoggedIn()]);
    exit;
}

if ($action === 'logout') {
    $authService->logoutAdmin();
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['error' => 'Acción no reconocida']);
?>
