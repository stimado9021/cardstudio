<?php
session_start();
require_once 'admin/includes/db.php';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($action === 'register') {
        $email = mysqli_real_escape_string($conn, $data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'Email y contraseña requeridos']);
            exit;
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO usuarios (email, password_hash) VALUES ('$email', '$password_hash')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['email'] = $email;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'El email ya está registrado o error interno']);
        }
    } elseif ($action === 'login') {
        $email = mysqli_real_escape_string($conn, $data['email'] ?? '');
        $password = $data['password'] ?? '';

        $query = "SELECT id_usuario, password_hash FROM usuarios WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['id_usuario'];
                $_SESSION['email'] = $email;
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Contraseña incorrecta']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
        }
    } elseif ($action === 'logout') {
        session_destroy();
        echo json_encode(['success' => true]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'check') {
        if (isset($_SESSION['user_id'])) {
            echo json_encode(['logged_in' => true, 'email' => $_SESSION['email'], 'user_id' => $_SESSION['user_id']]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
    }
}
?>
