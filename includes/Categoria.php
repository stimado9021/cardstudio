<?php
// includes/Categoria.php
// Modelo de categoría

require_once __DIR__ . '/Database.php';

class Categoria {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findAll() {
        return $this->db->queryAll(
            "SELECT id, nombre FROM categorias ORDER BY nombre ASC"
        );
    }

    public function findById($id) {
        return $this->db->queryOne(
            "SELECT id, nombre FROM categorias WHERE id = ?",
            "i",
            [$id]
        );
    }

    public function create($nombre) {
        $stmt = $this->db->query(
            "INSERT INTO categorias (nombre) VALUES (?)",
            "s",
            [$nombre]
        );
        if ($stmt === false) return false;
        mysqli_stmt_close($stmt);
        return $this->db->lastInsertId();
    }

    public function update($id, $nombre) {
        $stmt = $this->db->query(
            "UPDATE categorias SET nombre = ? WHERE id = ?",
            "si",
            [$nombre, $id]
        );
        if ($stmt === false) return false;
        mysqli_stmt_close($stmt);
        return true;
    }

    public function delete($id) {
        $stmt = $this->db->query("DELETE FROM categorias WHERE id = ?", "i", [$id]);
        if ($stmt === false) return false;
        mysqli_stmt_close($stmt);
        return true;
    }
}
?>
