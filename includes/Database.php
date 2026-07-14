<?php
// includes/Database.php
// Conexión centralizada a la base de datos (singleton)

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        require_once __DIR__ . '/../config.php';
        $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$this->conn) {
            error_log("Conexión a BD fallida: " . mysqli_connect_error());
            throw new Exception("Error de conexión con la base de datos");
        }
        mysqli_set_charset($this->conn, "utf8mb4");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    /**
     * Preparar y ejecutar una query con parámetros
     */
    public function query($sql, $types = '', $params = []) {
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt === false) {
            error_log("Error preparando query: " . mysqli_error($this->conn));
            return false;
        }
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        return $stmt;
    }

    /**
     * Ejecutar query y retornar resultados como array
     */
    public function queryAll($sql, $types = '', $params = []) {
        $stmt = $this->query($sql, $types, $params);
        if ($stmt === false) return [];
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    /**
     * Ejecutar query y retornar una sola fila
     */
    public function queryOne($sql, $types = '', $params = []) {
        $stmt = $this->query($sql, $types, $params);
        if ($stmt === false) return null;
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row;
    }

    /**
     * Obtener el último ID insertado
     */
    public function lastInsertId() {
        return mysqli_insert_id($this->conn);
    }
}
?>
