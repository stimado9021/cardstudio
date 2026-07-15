<?php
// includes/Diseno.php
// Modelo de diseño

require_once __DIR__ . '/Database.php';

class Diseno {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findAll() {
        return $this->db->queryAll(
            "SELECT d.id_diseno, d.nombre_diseno, d.miniatura_url, d.id_categoria, c.nombre as categoria_nombre 
             FROM disenos d 
             JOIN categorias c ON d.id_categoria = c.id 
             ORDER BY d.id_diseno DESC"
        );
    }

    public function findById($id) {
        return $this->db->queryOne(
            "SELECT * FROM disenos WHERE id_diseno = ?",
            "i",
            [$id]
        );
    }

    public function create($data) {
        $stmt = $this->db->query(
            "INSERT INTO disenos (nombre_diseno, id_categoria, imagen_fondo_url, miniatura_url, configuracion_textos_json) 
             VALUES (?, ?, ?, ?, ?)",
            "sisss",
            [
                $data['nombre'],
                $data['id_categoria'],
                $data['imagen_fondo_url'],
                $data['miniatura_url'],
                $data['configuracion_textos_json']
            ]
        );
        if ($stmt === false) return false;
        mysqli_stmt_close($stmt);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $types = '';
        $params = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $types .= 's';
            $params[] = $value;
        }
        $params[] = $id;
        $types .= 'i';

        $sql = "UPDATE disenos SET " . implode(', ', $fields) . " WHERE id_diseno = ?";
        $stmt = $this->db->query($sql, $types, $params);
        if ($stmt === false) return false;
        mysqli_stmt_close($stmt);
        return true;
    }

    public function delete($id) {
        $stmt = $this->db->query("DELETE FROM disenos WHERE id_diseno = ?", "i", [$id]);
        if ($stmt === false) return false;
        mysqli_stmt_close($stmt);
        return true;
    }
}
?>
