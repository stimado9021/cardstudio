<?php
// admin/includes/db.php

$servername = "localhost";
$username = "root"; // Reemplaza con tu usuario
$password = ""; // Reemplaza con tu contraseña
$database = "cardstudio"; // Reemplaza con el nombre de tu base de datos

// Crear la conexión
$conn = mysqli_connect($servername, $username, $password, $database);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Asegurar que la conexión use UTF-8 para caracteres especiales
mysqli_set_charset($conn, "utf8mb4");
?>