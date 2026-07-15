<?php
// api_publica.php
require_once 'admin/includes/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action == 'list_designs') {
    $query = "SELECT d.id_diseno, d.nombre_diseno, d.imagen_fondo_url, d.miniatura_url, d.configuracion_textos_json, d.id_categoria, c.nombre as categoria_nombre FROM disenos d LEFT JOIN categorias c ON d.id_categoria = c.id ORDER BY d.id_diseno DESC";
    $res = mysqli_query($conn, $query);
   
    $disenos = [];
    while($row = mysqli_fetch_assoc($res)) {
        $disenos[] = $row;
    }
    
    echo json_encode($disenos);
    exit;
}

if ($action == 'list_categories') {
    $res = mysqli_query($conn, "SELECT id, nombre FROM categorias ORDER BY id");
    $cats = [];
    while($row = mysqli_fetch_assoc($res)) $cats[] = $row;
    echo json_encode($cats);
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
