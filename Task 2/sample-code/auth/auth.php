<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This script demonstrates how to properly set up the Authorization header
 * using a Bearer Token, which is required for all Fingerspot Cloud API requests.
 *
 * Requirements:
 * - PHP cURL extension
 * - Fingerspot API Token (from developer dashboard)
 *
 * Documentation: https://developer.fingerspot.io
 */

// --- 1. CONFIGURATION ---
// Get your API Token from https://developer.fingerspot.io
$apiToken = 'YOUR_API_TOKEN_HERE';

// --- 2. HEADER SETUP ---
// All requests to Fingerspot API must be sent as JSON and include the Bearer Token
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Example function showing how to apply these headers in a cURL request
 */
function callFingerspotAPI($endpoint, $apiToken) {
    $url = "https://developer.fingerspot.io/api/" . $endpoint;

    $headers = [
        'Authorization: Bearer ' . $apiToken,
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);

    // cURL Options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true); // Fingerspot API uses POST for most actions
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['trans_id' => '1']));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Set to true in production with valid CA

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        return 'Error: ' . curl_error($ch);
    }

    curl_close($ch);

    return [
        'status_code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// Displaying the setup
echo "=== Fingerspot API Authentication Setup ===\n";
echo "Required Headers:\n";
foreach ($headers as $header) {
    echo "  - $header\n";
}

echo "\nUsage Note: Use these headers for every API call to the Fingerspot Cloud endpoints.\n";

/*
---------------------------------------------------------------------------
EXAMPLE REQUEST (RAW HTTP):
---------------------------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1"
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (SUCCESS):
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success",
    "data": [...]
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (UNAUTHORIZED):
---------------------------------------------------------------------------
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
