<?php
/**
 * API Transfer Bank
 * access token yang berfungsi untuk transfer bank.
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
$accessTokenb2b = $data['token_b2b2c'];
$accessTokenb2b2c = $data['token_b2b2c'];

$path = '/v1.0/emoney/transfer-bank';
$httpMethod = 'POST';

// body
$body = $data['bodyPaymentTransferBank'];
$body['additionalInfo']['centralId'] = '123654';

// Headers
$clientKey = $config['CLIENT_ID'];
$externalId = generateToken(15);
$timeStamp = dateTime();
$channelId = $config['CHANNEL_ID']; 
$signature = signatureGeneration($httpMethod, $path, $accessTokenb2b, json_encode($body), $timeStamp);

$headers = [
    'Authorization: Bearer ' . $accessTokenb2b,
    'authorization-customer: Bearer ' . $accessTokenb2b,
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