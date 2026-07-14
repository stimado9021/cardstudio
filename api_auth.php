<?php
session_start();

require_once __DIR__ . '/includes/services/AuthService.php';

header('Content-Type: application/json');

// Configurar sesiones seguras
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$authService = new AuthService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($action === 'register') {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $result = $authService->registerCliente($email, $password);
        echo json_encode($result);

    } elseif ($action === 'login') {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'Email y contraseña requeridos']);
            exit;
        }

        if ($authService->loginCliente($email, $password)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas']);
        }

    } elseif ($action === 'logout') {
        $authService->logoutCliente();
        echo json_encode(['success' => true]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'check') {
        if ($authService->isClienteLoggedIn()) {
            echo json_encode([
                'logged_in' => true,
                'email' => $_SESSION['email'],
                'user_id' => $_SESSION['user_id']
            ]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
    }
}
?>
