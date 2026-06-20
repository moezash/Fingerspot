<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP cURL extension
 * - API Token from Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
/**
 * Every request to Fingerspot API must include:
 * - Authorization: Bearer {token}
 * - Content-Type: application/json
 * - Accept: application/json
 */
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Connection Test Example
 * We use the 'get_device' endpoint to verify if the token is valid.
 */
function testAuthentication($apiToken, $headers) {
    $url = 'https://developer.fingerspot.io/api/get_device';

    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    /**
     * CURLOPT_SSL_VERIFYPEER should be true in production for security.
     * Set to false only during local development if you encounter SSL certificate issues.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // For many Fingerspot endpoints, POST is required even for listing
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['trans_id' => '1']));

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);

    curl_close($ch);

    if ($error) {
        return "Connection Error: " . $error;
    }

    return [
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// Displaying instructions
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nTo test your connection, replace 'YOUR_API_TOKEN_HERE' and run this script.\n";

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": []
}

Example Response (Unauthorized):
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
