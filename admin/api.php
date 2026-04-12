<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'includes/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action == 'login') {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    
    // Aquí deberías consultar tu tabla de usuarios (asumimos admin/admin para el ejemplo)
    if ($user == 'admin' && $pass == '1234') {
        echo json_encode(['success' => true, 'user' => ['nombre' => 'Administrador', 'rol' => 'admin']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    }
}

if ($action == 'get_categorias') {
    $result = mysqli_query($conn, "SELECT id, nombre FROM categorias ORDER BY nombre ASC");
    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}

if ($action == 'save_design') {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre_diseno']);
    $id_cat = (int)$_POST['id_categoria'];
    $config = mysqli_real_escape_string($conn, $_POST['config_json']);
    
    // Subida de imagen
    $extension = pathinfo($_FILES["imagen_fondo"]["name"], PATHINFO_EXTENSION);
    $nombre_archivo = uniqid() . "." . $extension;
    $ruta_destino = "uploads/" . $nombre_archivo;

    if (move_uploaded_file($_FILES["imagen_fondo"]["tmp_name"], $ruta_destino)) {
        $sql = "INSERT INTO diseños (nombre_diseño, id_categoria, imagen_fondo_url, configuracion_textos_json) 
                VALUES ('$nombre', $id_cat, '$ruta_destino', '$config')";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'id_insertado' => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
    }
}