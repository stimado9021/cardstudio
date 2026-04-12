<?php
// api_publica.php
require_once 'admin/includes/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action == 'list_designs') {
    $query = "SELECT id_diseño, nombre_diseño, imagen_fondo_url FROM diseños ORDER BY id_diseño DESC";
    $res = mysqli_query($conn, $query);
   
    $diseños = [];
    while($row = mysqli_fetch_assoc($res)) {
        $diseños[] = $row;
    }
    
    echo json_encode($diseños);
    exit;
}

// Si se pide un diseño específico por ID
if ($action == 'get_design_details') {
    $id = (int)$_GET['id'];
    $query = "SELECT * FROM diseños WHERE id_diseño = $id";
    $res = mysqli_query($conn, $query);
    echo json_encode(mysqli_fetch_assoc($res));
    exit;
}
