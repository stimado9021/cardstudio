<?php
// ================================================================
//  admin/auth_guard.php
//  Incluye este archivo al inicio de CADA página del panel admin.
//  Si el admin no está autenticado, lo redirige al login.
// ================================================================
session_name('cardstudio_admin');
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
