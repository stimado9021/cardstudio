<?php
// config.example.php - PLANTILLA DE CONFIGURACIÓN
// Este archivo SI se sube a GitHub para que otros desarrolladores
// sepan qué variables necesitan. 
// INSTRUCCIONES: Renombra este archivo a config.php y coloca tus datos reales.

// Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tu_base_de_datos');

// Configuración de PayPal
define('PAYPAL_CLIENT_ID', 'TU_CLIENT_ID_AQUI');
define('PAYPAL_CLIENT_SECRET', 'TU_CLIENT_SECRET_AQUI');
define('PAYPAL_MODE', 'sandbox'); // 'sandbox' o 'live'

// Configuración de Brevo (Email Marketing)
define('BREVO_API_KEY', 'TU_API_KEY_DE_BREVO_AQUI');
