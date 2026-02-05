<?php
/**
 * Google Sheets API Helper
 * Uses Service Account authentication with JWT
 * Writes to existing spreadsheet instead of creating new ones
 */

require_once __DIR__ . '/google_config.php';

class GoogleSheetsExporter
{
    private $accessToken;
    private $credentials;

    public function __construct()
    {
        if (!file_exists(GOOGLE_SERVICE_ACCOUNT_FILE)) {
            throw new Exception('Archivo de credenciales no encontrado. Coloca service-account.json en la carpeta config/');
        }
        $this->credentials = json_decode(file_get_contents(GOOGLE_SERVICE_ACCOUNT_FILE), true);
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * Generate JWT and exchange for access token
     */
    private function getAccessToken()
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $now = time();
        $claim = [
            'iss' => $this->credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600
        ];

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $claimEncoded = $this->base64UrlEncode(json_encode($claim));

        $signature = '';
        openssl_sign(
            $headerEncoded . '.' . $claimEncoded,
            $signature,
            $this->credentials['private_key'],
            'SHA256'
        );
        $signatureEncoded = $this->base64UrlEncode($signature);

        $jwt = $headerEncoded . '.' . $claimEncoded . '.' . $signatureEncoded;

        // Exchange JWT for access token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (isset($data['access_token'])) {
            return $data['access_token'];
        }
        throw new Exception('Error obteniendo token: ' . ($data['error_description'] ?? json_encode($data)));
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Clear existing data and write new data to spreadsheet
     */
    public function exportToSpreadsheet($data)
    {
        $spreadsheetId = GOOGLE_SPREADSHEET_ID;
        $sheetName = GOOGLE_SHEET_NAME;

        // First, clear existing data
        $this->clearSheet($spreadsheetId, $sheetName);

        // Then write new data
        $range = $sheetName . '!A1';
        $values = [];
        foreach ($data as $row) {
            $values[] = array_values($row);
        }

        $body = ['values' => $values];

        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?valueInputOption=RAW";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode === 200) {
            return [
                'success' => true,
                'spreadsheetId' => $spreadsheetId,
                'url' => "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit",
                'updatedRows' => $result['updatedRows'] ?? count($data)
            ];
        }

        return [
            'success' => false,
            'error' => $result['error']['message'] ?? 'Error desconocido: ' . $response
        ];
    }

    private function clearSheet($spreadsheetId, $sheetName)
    {
        $range = $sheetName . '!A:Z';
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}:clear";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
        curl_exec($ch);
        curl_close($ch);
    }
}
?>