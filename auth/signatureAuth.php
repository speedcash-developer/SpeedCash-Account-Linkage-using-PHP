<?php
/**
 * API Signature Generation
 * Signature digunakan untuk proses autentikasi request yang di kirimkan ke QRIS MPM
 * 
 * Referensi:
 * API Documentation: https://qris-mpm-docs.speedcash.co.id/docs/payments/qris-mpm/Keamanan/signature-generation
 */

require_once __DIR__ . '/../config/config.php';

function signatureAuth ($stringToSign) {
    global $CONFIG;

    $privateKeyPath = './private_key.pem';
    $privateKeyPem = file_get_contents($privateKeyPath);
    if (!$privateKeyPem) {
        die('Failed to read the private key file.');
    }
    $privateKey = openssl_pkey_get_private($privateKeyPem);
    if (!$privateKey) {
        die('Failed to load private key: ' . openssl_error_string());
    }
    openssl_free_key($privateKey);

    $signature = '';
    openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

    $base64Signature = base64_encode($signature);
    writeLocalData(['signature_auth' => $base64Signature]);
    return $base64Signature;
}
function signatureGeneration($method, $pathUrl, $accessToken, $requestBody, $timeStamp) {
    global $CONFIG;

    $body = is_array($requestBody) ? json_encode($requestBody) : $requestBody;
    $clientSecret = $CONFIG['CLIENT_SECRET'];
    $hexEncode = strtolower(hash('sha256', $body));
    $stringToSign = implode(':', [$method, $pathUrl, $accessToken, $hexEncode, $timeStamp]);

    $hmacSignature = base64_encode(hash_hmac('sha512', $stringToSign, $clientSecret, true));
    
    return $hmacSignature;
}
function signatureRsaGeneration($method, $pathUrl, $requestBody, $timeStamp) {
    $body = is_string($requestBody) ? $requestBody : json_encode($requestBody);
    $hexEncode = strtolower(hash('sha256', $body));
    $stringToSign = implode(':', [$method, $pathUrl, $hexEncode, $timeStamp]);
    
    $privateKeyPem = file_get_contents(__DIR__ . '/../cb_private_key.pem');
    if (!$privateKeyPem) {
        die('Failed to read the private key file.');
    }
    
    $privateKey = openssl_pkey_get_private($privateKeyPem);
    if (!$privateKey) {
        die('Failed to load private key: ' . openssl_error_string());
    }
    
    $signature = '';
    if (!openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
        die('Failed to create signature: ' . openssl_error_string());
    }
    
    openssl_free_key($privateKey);
    return base64_encode($signature);
}

function signatureRsaValidation($method, $url, $requestBody, $timestamp, $receivedSignature) {
    if (empty($receivedSignature)) {
        return false;
    }
    
    $bodyString = is_string($requestBody) ? $requestBody : json_encode($requestBody);
    $hashedBody = strtolower(hash('sha256', $bodyString));
    $stringToSign = implode(':', [$method, $url, $hashedBody, $timestamp]);
    
    $publicKeyPem = file_get_contents(__DIR__ . '/../cb_public_key.pem');
    if (!$publicKeyPem) {
        die('Failed to read the public key file.');
    }
    
    $publicKey = openssl_pkey_get_public($publicKeyPem);
    if (!$publicKey) {
        die('Failed to load public key: ' . openssl_error_string());
    }
    
    $result = openssl_verify(
        $stringToSign,
        base64_decode($receivedSignature),
        $publicKey,
        OPENSSL_ALGO_SHA256
    );
    
    openssl_free_key($publicKey);
    return $result === 1;
}