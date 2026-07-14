<?php
// admin/includes/db.php

require_once __DIR__ . '/../../config.php';

// Crear la conexión
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar la conexión
if (!$conn) {
    error_log("Conexión a BD fallida: " . mysqli_connect_error());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de conexión con la base de datos']);
    exit;
}

// Asegurar que la conexión use UTF-8 para caracteres especiales
mysqli_set_charset($conn, "utf8mb4");
?>
