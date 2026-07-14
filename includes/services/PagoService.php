<?php
// includes/services/PagoService.php
// Servicio de pagos - encapsula integración con PayPal

require_once __DIR__ . '/../Compra.php';

class PagoService {
    private $compra;

    public function __construct() {
        $this->compra = new Compra();
    }

    /**
     * Verificar si un usuario ya pagó por un diseño
     */
    public function hasPaid($userId, $disenoId) {
        $result = $this->compra->findByUserAndDiseno($userId, $disenoId);
        return $result !== null;
    }

    /**
     * Capturar un pago de PayPal
     */
    public function capturePayment($orderID, $disenoId, $userId) {
        require_once __DIR__ . '/../../config.php';

        $base_url = (PAYPAL_MODE === 'live')
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        // 1. Obtener Access Token
        $token = $this->getAccessToken($base_url);
        if (!$token) {
            return ['success' => false, 'error' => 'No se pudo autenticar con PayPal'];
        }

        // 2. Capturar la orden
        $captureResponse = $this->captureOrder($base_url, $orderID, $token);
        if (!$captureResponse) {
            return ['success' => false, 'error' => 'Error al conectar con PayPal'];
        }

        // 3. Verificar estado
        if (($captureResponse['status'] ?? '') === 'COMPLETED') {
            $this->compra->create($userId, $disenoId, 'completado');
            return ['success' => true];
        }

        $status = $captureResponse['status'] ?? 'UNKNOWN';
        error_log("Pago incompleto: status=$status, order=$orderID");
        return ['success' => false, 'error' => 'El pago no se completó correctamente'];
    }

    private function getAccessToken($base_url) {
        require_once __DIR__ . '/../../config.php';

        $ch = curl_init("$base_url/v1/oauth2/token");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_USERPWD        => PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET,
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response['access_token'] ?? null;
    }

    private function captureOrder($base_url, $orderID, $token) {
        $ch = curl_init("$base_url/v2/checkout/orders/$orderID/capture");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                "Content-Type: application/json",
                "Authorization: Bearer $token",
            ],
            CURLOPT_POSTFIELDS => '{}',
            CURLOPT_TIMEOUT    => 30,
        ]);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response;
    }
}
?>
