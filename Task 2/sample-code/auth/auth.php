<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP cURL extension
 * - Valid API Token from Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Replace with your actual API Token
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// and define Content-Type as application/json.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Example function to demonstrate how headers are used in a cURL request
 */
function testAuthentication($apiUrl, $headers) {
    // Initialize cURL
    $ch = curl_init($apiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Security best practice for production

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return 'cURL Error: ' . $error;
    }

    curl_close($ch);

    return [
        'http_code' => $httpCode,
        'response'  => json_decode($response, true)
    ];
}

// --- Output for Demonstration ---
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Required Headers:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: These headers must be included in ALL API requests to Fingerspot.\n";

/*
Example Request:
GET /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

Example Response (Successful Auth):
{
    "status": true,
    "message": "Success",
    "data": []
}

Example Response (Invalid Token):
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
