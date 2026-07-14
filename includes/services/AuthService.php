<?php
// includes/services/AuthService.php
// Servicio de autenticación - maneja login/logout/sesiones

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../Usuario.php';

class AuthService {
    private $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
    }

    /**
     * Login de admin
     */
    public function loginAdmin($usuario, $password) {
        require_once __DIR__ . '/../../config.php';

        if ($usuario === ADMIN_USER && password_verify($password, ADMIN_PASS_HASH)) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $usuario;
            return true;
        }
        return false;
    }

    /**
     * Verificar si el admin está autenticado
     */
    public function isAdminLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    /**
     * Logout del admin
     */
    public function logoutAdmin() {
        session_destroy();
    }

    /**
     * Login de cliente
     */
    public function loginCliente($email, $password) {
        $user = $this->usuario->verifyPassword($email, $password);
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['email'] = $user['email'];
            return true;
        }
        return false;
    }

    /**
     * Registro de cliente
     */
    public function registerCliente($email, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Formato de email inválido'];
        }
        if (empty($password) || strlen($password) < 6) {
            return ['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres'];
        }

        $existing = $this->usuario->findByEmail($email);
        if ($existing) {
            return ['success' => false, 'error' => 'El email ya está registrado'];
        }

        $userId = $this->usuario->create($email, $password);
        if ($userId) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['email'] = $email;
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Error al crear la cuenta'];
    }

    /**
     * Verificar si el cliente está autenticado
     */
    public function isClienteLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Logout del cliente
     */
    public function logoutCliente() {
        session_destroy();
    }
}
?>
