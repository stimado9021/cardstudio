<?php
/**
 * Brevo Email Service
 * Handles mass mailing using Brevo API v3
 */

class BrevoService {
    private $apiKey;
    private $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Send a single transactional email or marketing email
     */
    public function sendEmail($toEmail, $subject, $htmlContent, $senderName = 'CardStudio', $senderEmail = 'no-reply@cardstudio.com') {
        $data = [
            'sender' => ['name' => $senderName, 'email' => $senderEmail],
            'to' => [['email' => $toEmail]],
            'subject' => $subject,
            'htmlContent' => $htmlContent
        ];

        return $this->executeCurl($data);
    }

    /**
     * Send mass email to multiple recipients
     * Note: For massive marketing, Brevo recommends using 'campaigns' or batching.
     * This implementation sends them in a single call if possible or iterates.
     */
    public function sendMassEmail($recipients, $subject, $htmlContent, $senderName = 'CardStudio', $senderEmail = 'no-reply@cardstudio.com') {
        $results = [];
        foreach ($recipients as $email) {
            $results[$email] = $this->sendEmail($email, $subject, $htmlContent, $senderName, $senderEmail);
        }
        return $results;
    }

    private function executeCurl($data) {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'api-key: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'success' => ($httpCode >= 200 && $httpCode < 300),
            'status' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }
}
?>
