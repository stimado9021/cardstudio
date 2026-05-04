<?php
/**
 * admin/api_emails.php
 * Endpoint for marketing email management
 */
require_once 'auth_guard.php';
require_once 'includes/db.php';
require_once 'includes/brevo_service.php';

header('Content-Type: application/json');

// Using the provided API key from config.php (included via db.php)
$mailer = new BrevoService(BREVO_API_KEY);

$action = $_GET['action'] ?? '';

if ($action === 'get_stats') {
    // Basic stats for the dashboard
    $resUsers = mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios");
    $totalUsers = mysqli_fetch_assoc($resUsers)['total'];
    
    echo json_encode([
        'success' => true,
        'total_subscribers' => (int)$totalUsers
    ]);
    exit;
}

if ($action === 'send_mass' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $subject = $data['subject'] ?? '';
    $content = $data['content'] ?? '';
    
    if (empty($subject) || empty($content)) {
        echo json_encode(['success' => false, 'error' => 'Asunto y contenido son requeridos']);
        exit;
    }

    // Fetch all users with their data
    $query = "SELECT nombre, email FROM usuarios";
    $result = mysqli_query($conn, $query);
    
    $successCount = 0;
    $totalCount = 0;

    // Usamos el remitente verificado
    $senderName = 'Palmar Studio';
    $senderEmail = 'quinteroenrrique321@gmail.com';

    while ($row = mysqli_fetch_assoc($result)) {
        $totalCount++;
        $userName = $row['nombre'] ?? 'Cliente';
        $userEmail = $row['email'];

        // Personalización: Reemplazamos las variables en el contenido
        $personalizedContent = str_replace('{{nombre}}', $userName, $content);
        $personalizedContent = str_replace('{{email}}', $userEmail, $personalizedContent);

        $res = $mailer->sendEmail($userEmail, $subject, $personalizedContent, $senderName, $senderEmail);
        if ($res['success']) {
            $successCount++;
        }
    }

    echo json_encode([
        'success' => true,
        'sent_count' => $successCount,
        'total_count' => $totalCount
    ]);
    exit;
}

echo json_encode(['error' => 'Acción no reconocida']);
?>
