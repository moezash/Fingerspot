<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token from Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Replace with your actual API Token
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
/**
 * Every request to Fingerspot API must include:
 * - Authorization: Bearer {Token}
 * - Content-Type: application/json
 * - Accept: application/json
 */
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Example function to demonstrate how headers are applied to a cURL request
 *
 * @param string $url The endpoint URL
 * @param array $headers The array of headers
 * @return array Response data and HTTP status code
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    /**
     * SSL Verification:
     * Set to true for production.
     * Setting to false is strictly for local development troubleshooting only.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// Displaying sample header setup
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in all cURL requests:\n";
foreach ($headers as $header) {
    echo "  [OK] $header\n";
}

echo "\nDeveloper Note:\n";
echo "This file serves as a reference for setting up API credentials.\n";
echo "Use these headers for all subsequent feature samples (Get Device, Get Logs, etc.).\n";

/**
 * Example Request:
 * ------------------------------------------------------------
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 * Accept: application/json
 *
 * {
 *   "trans_id": "1"
 * }
 *
 * Example Response (Success):
 * ------------------------------------------------------------
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": [...]
 * }
 *
 * Example Response (Unauthorized):
 * ------------------------------------------------------------
 * {
 *   "status": false,
 *   "message": "Unauthorized"
 * }
 */
?>
