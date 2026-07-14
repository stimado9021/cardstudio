<?php
session_name('cardstudio_admin');
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Diseno.php';
require_once __DIR__ . '/../includes/Categoria.php';
require_once __DIR__ . '/../includes/services/UploadService.php';

// Verificar autenticación del admin
$action = $_GET['action'] ?? '';
$acciones_publicas = [];

if (!in_array($action, $acciones_publicas)) {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'No autorizado']);
        exit;
    }
}

// --- OBTENER CATEGORÍAS ---
if ($action === 'get_categorias') {
    $categoria = new Categoria();
    echo json_encode($categoria->findAll());
    exit;
}

// --- OBTENER TODOS LOS DISEÑOS ---
if ($action === 'get_disenos') {
    $diseno = new Diseno();
    echo json_encode($diseno->findAll());
    exit;
}

// --- OBTENER UN DISEÑO POR ID ---
if ($action === 'get_diseno') {
    $id = (int)($_GET['id'] ?? 0);
    $diseno = new Diseno();
    $result = $diseno->findById($id);
    if ($result) {
        echo json_encode(['success' => true, 'diseno' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Diseño no encontrado']);
    }
    exit;
}

// --- GUARDAR DISEÑO ---
if ($action === 'save_design') {
    $uploadService = new UploadService();
    $disenoModel = new Diseno();

    $nombre = mysqli_real_escape_string(
        Database::getInstance()->getConnection(),
        $_POST['nombre_diseno'] ?? ''
    );
    $id_cat = (int)($_POST['id_categoria'] ?? 0);
    $config = mysqli_real_escape_string(
        Database::getInstance()->getConnection(),
        $_POST['config_json'] ?? ''
    );
    $miniatura_base64 = $_POST['miniatura_base64'] ?? '';
    $id_diseno = isset($_POST['id_diseno']) ? (int)$_POST['id_diseno'] : 0;

    $ruta_fondo = "";

    // 1. Procesar Imagen de Fondo
    if (isset($_FILES["imagen_fondo"]) && $_FILES["imagen_fondo"]["error"] == UPLOAD_ERR_OK) {
        $result = $uploadService->uploadBackground($_FILES["imagen_fondo"]);
        if (!$result['valid']) {
            echo json_encode(['success' => false, 'message' => $result['error']]);
            exit;
        }
        $ruta_fondo = $result['path'];
    } else if ($id_diseno == 0) {
        echo json_encode(['success' => false, 'message' => 'La imagen de fondo es obligatoria para nuevos diseños']);
        exit;
    }

    // 2. Procesar Miniatura
    $ruta_miniatura = "";
    if (!empty($miniatura_base64)) {
        $result = $uploadService->saveThumbnail($miniatura_base64);
        if (!$result['valid']) {
            echo json_encode(['success' => false, 'message' => $result['error']]);
            exit;
        }
        $ruta_miniatura = $result['path'];
    }

    // 3. Guardar en Base de Datos
    if ($id_diseno > 0) {
        $updateData = [
            'nombre_diseno' => $nombre,
            'id_categoria' => $id_cat,
            'configuracion_textos_json' => $config
        ];
        if (!empty($ruta_fondo)) {
            $updateData['imagen_fondo_url'] = $ruta_fondo;
        }
        if (!empty($ruta_miniatura)) {
            $updateData['miniatura_url'] = $ruta_miniatura;
        }

        if ($disenoModel->update($id_diseno, $updateData)) {
            echo json_encode([
                'success' => true,
                'id_insertado' => $id_diseno,
                'miniatura' => $ruta_miniatura,
                'is_update' => true
            ]);
        } else {
            error_log("Error BD actualizar diseno ID: $id_diseno");
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el diseño']);
        }
    } else {
        $newId = $disenoModel->create([
            'nombre' => $nombre,
            'id_categoria' => $id_cat,
            'imagen_fondo_url' => $ruta_fondo,
            'miniatura_url' => $ruta_miniatura,
            'configuracion_textos_json' => $config
        ]);

        if ($newId) {
            echo json_encode([
                'success' => true,
                'id_insertado' => $newId,
                'miniatura' => $ruta_miniatura,
                'is_update' => false
            ]);
        } else {
            error_log("Error BD insertar diseno: $nombre");
            echo json_encode(['success' => false, 'message' => 'Error al guardar el diseño']);
        }
    }
    exit;
}

echo json_encode(['error' => 'Acción no reconocida']);
?>
