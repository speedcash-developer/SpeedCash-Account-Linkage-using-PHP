<?php
/**
 * API Inquiry Bank Transfer
 * access token yang berfungsi untuk cek rekening/inquiry sebelum melakukan transfer bank.
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

$path = '/v1.0/emoney/bank-account-inquiry';
$httpMethod = 'POST';

// body
$body = [
    'partnerReferenceNo' => '1234567890123',
    'customerNumber' => '08526183xxxx',
    'beneficiaryAccountNumber' => '142002118xxxx',
    'amount' => [
        'value' => '24000.00',
        'currency' => 'IDR'
    ],
    'additionalInfo' => [
        'deviceId' => 'example-20013adf6cdd8123f',
        'notes' => 'test payment',
        'beneficiaryBankName' => 'MANDIRI',
        'bankAccountName' => 'AJI RAWARONTEK',
        'merchantId' => '1213xxx',
        'beneficiaryBankCode' => '008'
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

    writeLocalData([
        'bodyPaymentTransferBank' => [
            'partnerReferenceNo' => $body['partnerReferenceNo'],
            'customerNumber' => $body['customerNumber'],
            'beneficiaryAccountNumber' => $body['beneficiaryAccountNumber'],
            'beneficiaryBankCode' => $body['additionalInfo']['beneficiaryBankCode'],
            'amount' => $body['amount'],
            'additionalInfo' => [
                'deviceId' => $body['additionalInfo']['deviceId'],
                'notes' => 'topup',
                'bankAccountName' => $response['beneficiaryAccountName'],
                'referenceNo' => $response['referenceNo'],
                'merchantId' => $body['additionalInfo']['merchantId'],
                'transactionCode' => $response['additionalInfo']['transactionCode']
            ]
        ]
    ]);    
} else {
    echo "Failed to get a response.\n";
}