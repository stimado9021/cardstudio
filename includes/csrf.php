<?php
// includes/csrf.php
// Helper para protección CSRF

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generar token CSRF y guardarlo en la sesión
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Obtener el campo HTML hidden con el token CSRF
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Verificar que el token CSRF enviado coincide con el de la sesión
 */
function verifyCSRFToken() {
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    $valid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    // Regenerar token después de usarlo
    unset($_SESSION['csrf_token']);
    return $valid;
}
?>
