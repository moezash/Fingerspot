<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP cURL extension
 * - Valid API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// --- 1. Configuration ---
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// --- 2. Prepare Headers ---
/**
 * Every request to Fingerspot API MUST include:
 * 1. Authorization: Bearer {token}
 * 2. Content-Type: application/json
 */
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a standard cURL request
 *
 * @param string $url The endpoint URL
 * @param array $headers The headers array
 * @return array Response details
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    /**
     * CURLOPT_SSL_VERIFYPEER should be true in production for security.
     * Set to false only during local development if you encounter SSL issues.
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

// --- 3. Displaying Sample Usage ---
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in all cURL requests:\n";
foreach ($headers as $header) {
    echo "  [OK] $header\n";
}

echo "\nTip: Include these headers in every POST request to the Fingerspot Cloud API endpoints.\n";

/**
 * Example Request (Conceptual):
 *
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 * Accept: application/json
 *
 * {
 *    "trans_id": "123456"
 * }
 *
 * Example Response (Invalid Token):
 * {
 *    "status": false,
 *    "message": "Unauthorized"
 * }
 *
 * Example Response (Success):
 * {
 *    "status": true,
 *    "message": "Success",
 *    "data": [...]
 * }
 */
?>
