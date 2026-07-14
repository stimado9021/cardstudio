<?php
// includes/Usuario.php
// Modelo de usuario

require_once __DIR__ . '/Database.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail($email) {
        return $this->db->queryOne(
            "SELECT id_usuario, email, password_hash, fecha_registro, nombre FROM usuarios WHERE email = ?",
            "s",
            [$email]
        );
    }

    public function findById($id) {
        return $this->db->queryOne(
            "SELECT id_usuario, email, password_hash, fecha_registro, nombre FROM usuarios WHERE id_usuario = ?",
            "i",
            [$id]
        );
    }

    public function create($email, $password) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->query(
            "INSERT INTO usuarios (email, password_hash) VALUES (?, ?)",
            "ss",
            [$email, $password_hash]
        );
        if ($stmt === false) return false;
        mysqli_stmt_close($stmt);
        return $this->db->lastInsertId();
    }

    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        if (!$user) return null;
        if (password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public function getAll() {
        return $this->db->queryAll(
            "SELECT id_usuario, email, nombre, fecha_registro FROM usuarios ORDER BY fecha_registro DESC"
        );
    }

    public function count() {
        $result = $this->db->queryOne("SELECT COUNT(*) as total FROM usuarios");
        return $result ? (int)$result['total'] : 0;
    }
}
?>
