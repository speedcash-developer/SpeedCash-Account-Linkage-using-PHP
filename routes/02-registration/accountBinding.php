<?php
/**
 * API Binding Account
 * access token yang berfungsi untuk binding account user.
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

$path = '/v1.0/registration-account-binding';
$httpMethod = 'POST';

// body
$body = [
    'msisdn' => '08960101xxxx',
    'merchantId' => '1212xxx',
    'additionalInfo' => [
        'callbackUrl' => 'https://youraddressforcallback.com',
        'deviceId' => 'example-20013adf6cdd8123f'
    ]
];

// Headers
$clientKey = $config['CLIENT_ID'];
$externalId = generateToken(15);
$timeStamp = dateTime();
$channelId = $config['CHANNEL_ID']; 
$signature = signatureGeneration($httpMethod, $path, $accessTokenb2b, json_encode($body), $timeStamp);

$headers = [
    'Authorization: Bearer ' . $accessTokenb2b,
    'x-timestamp: ' . $timeStamp,
    'x-signature: ' . $signature,
    'x-partner-id: ' . $clientKey,
    'channel-id: ' . $channelId,
    'x-external-id: ' . $externalId,
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