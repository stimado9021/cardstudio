<?php
// ================================================================
//  admin/api_admin_auth.php
//  Gestión de sesión del administrador (independiente de usuarios)
// ================================================================
session_name('cardstudio_admin'); // Sesión separada de la de clientes
session_start();

header('Content-Type: application/json');

// ─── Credenciales del Administrador ────────────────────────────────
// Cambia estos valores antes de ir a producción.
// La contraseña se guarda como hash para mayor seguridad.
define('ADMIN_USER', 'admin');
define('ADMIN_PASS_HASH', password_hash('cardstudio2025', PASSWORD_BCRYPT));
// ────────────────────────────────────────────────────────────────────

$action = $_GET['action'] ?? '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $usuario  = trim($data['usuario'] ?? '');
    $password = $data['password'] ?? '';

    if ($usuario === ADMIN_USER && password_verify($password, ADMIN_PASS_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user']      = $usuario;
        echo json_encode(['success' => true]);
    } else {
        // Delay para dificultar ataques de fuerza bruta
        sleep(1);
        echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas']);
    }
    exit;
}

if ($action === 'check') {
    echo json_encode(['logged_in' => isset($_SESSION['admin_logged_in'])]);
    exit;
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['error' => 'Acción no reconocida']);
?>
