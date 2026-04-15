<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'includes/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// --- LOGIN ---
if ($action == 'login') {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    
    if ($user == 'admin' && $pass == '1234') {
        echo json_encode(['success' => true, 'user' => ['nombre' => 'Administrador', 'rol' => 'admin']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    }
}

// --- OBTENER CATEGORÍAS ---
if ($action == 'get_categorias') {
    $result = mysqli_query($conn, "SELECT id, nombre FROM categorias ORDER BY nombre ASC");
    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}

// --- GUARDAR DISEÑO Y GENERAR JPG ---
if ($action == 'save_design') {
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }
    if (!file_exists('uploads/thumbnails')) {
        mkdir('uploads/thumbnails', 0777, true);
    }
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre_diseno']);
    $id_cat = (int)$_POST['id_categoria'];
    $config = mysqli_real_escape_string($conn, $_POST['config_json']);
    $miniatura_base64 = $_POST['miniatura_base64'] ?? ''; // Recibimos el JPG del canvas

    // 1. Procesar Imagen de Fondo (Original)
    $extension = pathinfo($_FILES["imagen_fondo"]["name"], PATHINFO_EXTENSION);
    $nombre_fondo = "fondo_" . uniqid() . "." . $extension;
    $ruta_fondo = "uploads/" . $nombre_fondo;

    if (move_uploaded_file($_FILES["imagen_fondo"]["tmp_name"], $ruta_fondo)) {
        
        $ruta_miniatura = ""; // Variable para la ruta del JPG generado

        // 2. Procesar la Miniatura JPG (Base64)
        if (!empty($miniatura_base64)) {
            // Limpiar la cabecera del base64
            $img_data = str_replace('data:image/jpeg;base64,', '', $miniatura_base64);
            $img_data = str_replace(' ', '+', $img_data);
            $img_decoded = base64_decode($img_data);

            // Nombre único para el JPG de la miniatura
            $nombre_miniatura = "thumb_" . uniqid() . ".jpg";
            $ruta_miniatura = "uploads/thumbnails/" . $nombre_miniatura;

            // Guardar físicamente el archivo JPG
            file_put_contents($ruta_miniatura, $img_decoded);
        }

        // 3. Guardar en Base de Datos incluyendo la ruta de la miniatura
        $sql = "INSERT INTO diseños (nombre_diseño, id_categoria, imagen_fondo_url, miniatura_url, configuracion_textos_json) 
                VALUES ('$nombre', $id_cat, '$ruta_fondo', '$ruta_miniatura', '$config')";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                'success' => true, 
                'id_insertado' => mysqli_insert_id($conn),
                'miniatura' => $ruta_miniatura
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error BD: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al subir la imagen de fondo']);
    }
}
?>