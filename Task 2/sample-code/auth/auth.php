<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - Pure PHP + cURL
 * - Valid API Token from Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// and define the content type as application/json.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * This is a template for any API call in this project.
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Security: Verification of SSL certificate
    // Setting to true is recommended for production.
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
        'response' => json_decode($response, true) ?? $response
    ];
}

// Displaying how headers should look
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: This is a configuration sample. Use these headers in all your API requests.\n";

/*
Example Request:
----------------
GET /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

Example Response (Success):
---------------------------
{
    "status": true,
    "message": "Success",
    "data": [...]
}

Example Response (Unauthorized):
--------------------------------
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
