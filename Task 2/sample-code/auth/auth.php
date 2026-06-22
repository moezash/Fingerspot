<?php
/**
 * Sample code for Authentication with Fingerspot Cloud API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements: Pure PHP + cURL
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header.
// It also expects JSON content type for all POST requests.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * Most Fingerspot API endpoints require the POST method.
 *
 * @param string $url The endpoint URL
 * @param array $headers The array of headers
 * @param array $payload The data to be sent in the POST body
 * @return array The HTTP response code and body
 */
function testAuthentication($url, $headers, $payload = ['trans_id' => '1']) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Most Fingerspot API endpoints use POST
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    // Security: SSL Verification
    // In production, CURLOPT_SSL_VERIFYPEER must be set to true.
    // Setting it to false is strictly for local development troubleshooting only.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'code' => $httpCode,
            'response' => json_encode(['status' => false, 'message' => $error_msg])
        ];
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response
    ];
}

// Displaying how headers should look
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: This is a configuration sample. Use these headers in all your API requests.\n";

/**
 * Example Request:
 *
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 * Accept: application/json
 *
 * {
 *     "trans_id": "1"
 * }
 *
 * Example Response (Success):
 * {
 *     "status": true,
 *     "message": "Success",
 *     "data": [...]
 * }
 *
 * Example Response (Unauthorized):
 * {
 *     "status": false,
 *     "message": "Unauthorized"
 * }
 */
?>
