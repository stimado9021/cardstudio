<?php
// api_publica.php
require_once 'admin/includes/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action == 'list_designs') {
    $query = "SELECT id_diseno, nombre_diseno, imagen_fondo_url, miniatura_url, configuracion_textos_json FROM disenos ORDER BY id_diseno DESC";
    $res = mysqli_query($conn, $query);
   
    $disenos = [];
    while($row = mysqli_fetch_assoc($res)) {
        $disenos[] = $row;
    }
    
    echo json_encode($disenos);
    exit;
}

// Si se pide un diseño específico por ID
if ($action == 'get_design_details') {
    $id = (int)$_GET['id'];
    $query = "SELECT * FROM disenos WHERE id_diseno = $id";
    $res = mysqli_query($conn, $query);
    echo json_encode(mysqli_fetch_assoc($res));
    exit;
}
