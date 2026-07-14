<?php
// includes/Compra.php
// Modelo de compra

require_once __DIR__ . '/Database.php';

class Compra {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByUserAndDiseno($userId, $disenoId) {
        return $this->db->queryOne(
            "SELECT id_compra FROM compras WHERE id_usuario = ? AND id_diseno = ? AND estado_pago = 'completado' LIMIT 1",
            "ii",
            [$userId, $disenoId]
        );
    }

    public function create($userId, $disenoId, $estado = 'completado') {
        $stmt = $this->db->query(
            "INSERT INTO compras (id_usuario, id_diseno, estado_pago) VALUES (?, ?, ?) 
             ON DUPLICATE KEY UPDATE estado_pago = ?",
            "iiss",
            [$userId, $disenoId, $estado, $estado]
        );
        if ($stmt === false) return false;
        mysqli_stmt_close($stmt);
        return true;
    }

    public function countCompleted() {
        $result = $this->db->queryOne(
            "SELECT COUNT(*) as total FROM compras WHERE estado_pago = 'completado'"
        );
        return $result ? (int)$result['total'] : 0;
    }

    public function findAll() {
        return $this->db->queryAll(
            "SELECT c.*, u.email, d.nombre_diseno 
             FROM compras c 
             JOIN usuarios u ON c.id_usuario = u.id_usuario 
             JOIN disenos d ON c.id_diseno = d.id_diseno 
             ORDER BY c.fecha_compra DESC"
        );
    }
}
?>
