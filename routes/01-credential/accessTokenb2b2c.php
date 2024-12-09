<?php
/**
 * API Token B2B2C
 * access token yang berfungsi untuk transaksi.
 * 
 * Referensi:
 * API Documentation: 
 */

require_once __DIR__ . '/../../config/config.php';
$config = $GLOBALS['CONFIG'];

require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../service/sender.php';
require_once __DIR__ . '/../../auth/signatureAuth.php';

$fileContent = file_get_contents('localData.json');
$data = json_decode($fileContent, true);

$path = '/access-token/b2b2c';
$httpMethod = 'POST';

// body
$body = [
    'grantType' => 'refresh_token',
    'refreshToken' => '74d63fbe-cd3e-4816-85b4-483fa5a563a3',
];

// Headers
$clientId = $config['CLIENT_ID'];
$externalId = generateToken(15);
$timeStamp = $data['timeStamp'];
$channelId = $config['CHANNEL_ID']; 
$signature = $data['signature_auth'];

$headers = [
    'X-CLIENT-KEY: ' . $clientId,
    'X-EXTERNAL-ID: ' . $externalId,
    'X-TIMESTAMP: ' . $timeStamp,
    'X-SIGNATURE: ' . $signature,
    'CHANNEL-ID: ' . $channelId,
    'Content-Type: application/json'
];

// Send the POST request
$response = post($path, $body, $headers);

// Handle the response
if ($response) {
    writeLocalData(['token_b2b2c' => $response['accessToken']]);
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Failed to get a response.\n";
}