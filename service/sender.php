<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Function to make a POST request
 *
 * @param string $path The API path to send the request to
 * @param array $body The body of the POST request
 * @param array $headers Optional headers for the request
 * @return array The response from the API
 */
function post($path, $body, $headers = []) {
    global $CONFIG;
    $ch = curl_init($CONFIG['BASE_URL'] . $path);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(['Content-Type: application/json'], $headers));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $errorMessage = curl_error($ch);
        curl_close($ch);
        return [
            'responseCode' => $httpCode ?: 500,
            'responseMessage' => $errorMessage,
            'additionalInfo' => null
        ];
    }

    $responseBody = json_decode($response, true);
    curl_close($ch);

    return $responseBody ?: [
        'responseCode' => $httpCode,
        'responseMessage' => 'No response received',
        'additionalInfo' => null
    ];
}
function postCallback($path, $body, $headers = []) {
    global $CONFIG;
    $formattedHeaders = [];
    foreach ($headers as $header) {
        if (is_string($header)) {
            $formattedHeaders[] = $header;
        }
    }
    
    $ch = curl_init(rtrim($CONFIG['YOUR_URL'], '/') . $path);
    
    curl_setopt_array($ch, [
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_HTTPHEADER => $formattedHeaders,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true, 
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
    ]);
    
    
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $responseHeaders = substr($response, 0, $headerSize);
    $responseBody = substr($response, $headerSize);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $errorMessage = curl_error($ch);
        curl_close($ch);
        return [
            'responseCode' => $httpCode ?: 500,
            'responseMessage' => $errorMessage,
            'additionalInfo' => null
        ];
    }
    
    curl_close($ch);
    
    return json_decode($responseBody, true) ?: [
        'responseCode' => $httpCode,
        'responseMessage' => 'No response received',
        'additionalInfo' => null
    ];
}
function get($path, $headers = []) {
    global $CONFIG;
    $ch = curl_init($CONFIG['BASE_URL'] . $path);

    // Set cURL options for GET request
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        // Handle error
        $errorMessage = curl_error($ch);
        curl_close($ch);
        return [
            'responseCode' => $httpCode ?: 500,
            'responseMessage' => $errorMessage,
            'additionalInfo' => null
        ];
    }

    // Parse the response body
    $responseBody = json_decode($response, true);
    curl_close($ch);

    // Return the response body or error details
    return $responseBody ?: [
        'responseCode' => $httpCode,
        'responseMessage' => 'No response received',
        'additionalInfo' => null
    ];
}

function postMultipart($path, $formData, $headers = []) {
    global $CONFIG;
    $ch = curl_init($CONFIG['BASE_URL'] . $path);

    curl_setopt_array($ch, [
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $formData,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $errorMessage = curl_error($ch);
        curl_close($ch);
        return [
            'responseCode' => $httpCode ?: 500,
            'responseMessage' => $errorMessage,
            'additionalInfo' => null
        ];
    }

    $responseBody = json_decode($response, true);
    curl_close($ch);

    return $responseBody ?: [
        'responseCode' => $httpCode,
        'responseMessage' => 'No response received',
        'additionalInfo' => null
    ];
}