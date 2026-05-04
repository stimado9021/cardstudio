<?php
// admin/includes/db.php

require_once __DIR__ . '/../../config.php';

// Crear la conexión
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Asegurar que la conexión use UTF-8 para caracteres especiales
mysqli_set_charset($conn, "utf8mb4");
?>