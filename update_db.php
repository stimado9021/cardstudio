<?php
require_once 'admin/includes/db.php';
$sql = "ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS nombre VARCHAR(255) DEFAULT 'Cliente' AFTER id_usuario";
if (mysqli_query($conn, $sql)) {
    echo "Columna 'nombre' añadida con éxito.";
} else {
    echo "Error o la columna ya existe: " . mysqli_error($conn);
}
?>
