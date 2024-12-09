<?php
/**
 * API cek status user
 * access token yang berfungsi untuk cek status user sebelum binding (terdaftar/belum).
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
$accessTokenb2b = $data['token_b2b'];

$path = '/v1.0/registration-account-inquiry';
$httpMethod = 'POST';

// body
$body = [
    'additionalInfo' => [
        'phoneNo' => '089601014551'
    ]
];

// Headers
$clientId = $config['CLIENT_ID'];
$externalId = generateToken(15);
$timeStamp = dateTime();
$channelId = $config['CHANNEL_ID']; 
$signature = signatureGeneration($httpMethod, $path, $accessTokenb2b, json_encode($body), $timeStamp);

$headers = [
    'Authorization: Bearer ' . $accessTokenb2b,
    'X-PARTNER-ID: ' . $clientId,
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
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Failed to get a response.\n";
}