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

// --- OBTENER TODOS LOS DISEÑOS ---
if ($action == 'get_disenos') {
    $sql = "SELECT d.id_diseno, d.nombre_diseno, d.miniatura_url, c.nombre as categoria_nombre 
            FROM disenos d 
            JOIN categorias c ON d.id_categoria = c.id 
            ORDER BY d.id_diseno DESC";
    $result = mysqli_query($conn, $sql);
    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}

// --- OBTENER UN DISEÑO POR ID ---
if ($action == 'get_diseno') {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM disenos WHERE id_diseno = $id";
    $result = mysqli_query($conn, $sql);
    if($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'diseno' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Diseño no encontrado']);
    }
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
    $id_diseno = isset($_POST['id_diseno']) ? (int)$_POST['id_diseno'] : 0;

    $ruta_fondo = "";
    // 1. Procesar Imagen de Fondo (Original)
    if (isset($_FILES["imagen_fondo"]) && $_FILES["imagen_fondo"]["error"] == UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES["imagen_fondo"]["name"], PATHINFO_EXTENSION);
        $nombre_fondo = "fondo_" . uniqid() . "." . $extension;
        $ruta_fondo = "uploads/" . $nombre_fondo;
        if (!move_uploaded_file($_FILES["imagen_fondo"]["tmp_name"], $ruta_fondo)) {
            echo json_encode(['success' => false, 'message' => 'Error al subir la imagen de fondo']);
            exit;
        }
    } else if ($id_diseno == 0) {
        echo json_encode(['success' => false, 'message' => 'La imagen de fondo es obligatoria para nuevos diseños']);
        exit;
    }

    $ruta_miniatura = ""; 
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

    // 3. Guardar o Actualizar en Base de Datos
    if ($id_diseno > 0) {
        // Update
        $update_parts = [
            "nombre_diseno = '$nombre'",
            "id_categoria = $id_cat",
            "configuracion_textos_json = '$config'"
        ];
        if (!empty($ruta_fondo)) {
            $update_parts[] = "imagen_fondo_url = '$ruta_fondo'";
        }
        if (!empty($ruta_miniatura)) {
            $update_parts[] = "miniatura_url = '$ruta_miniatura'";
        }
        
        $sql = "UPDATE disenos SET " . implode(', ', $update_parts) . " WHERE id_diseno = $id_diseno";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                'success' => true, 
                'id_insertado' => $id_diseno,
                'miniatura' => $ruta_miniatura,
                'is_update' => true
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error BD al actualizar: ' . mysqli_error($conn)]);
        }
    } else {
        // Insert
        $sql = "INSERT INTO disenos (nombre_diseno, id_categoria, imagen_fondo_url, miniatura_url, configuracion_textos_json) 
                VALUES ('$nombre', $id_cat, '$ruta_fondo', '$ruta_miniatura', '$config')";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                'success' => true, 
                'id_insertado' => mysqli_insert_id($conn),
                'miniatura' => $ruta_miniatura,
                'is_update' => false
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error BD al insertar: ' . mysqli_error($conn)]);
        }
    }
}
?>