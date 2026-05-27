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
// Replace with your actual API Token from Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// and 'Content-Type: application/json' since all requests send JSON bodies.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Example function showing how to perform a request with these headers.
 * This is for demonstration purposes.
 */
function performApiRequest($url, $method = 'POST', $data = null) {
    global $headers;

    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Security: Enable SSL verification in production
    // Set to false only for local development troubleshooting if you face SSL certificate issues
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    // Execute request
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($error) {
        return "cURL Error: " . $error;
    }

    return [
        'code'     => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// Displaying how headers should be structured
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in all cURL requests:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: All API endpoints at developer.fingerspot.io require these headers.\n";

/*
Example Request Headers:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

Example Response (Success):
HTTP/1.1 200 OK
{
    "success": true,
    "data": [...]
}

Example Response (Unauthorized):
HTTP/1.1 401 Unauthorized
{
    "success": false,
    "message": "Unauthorized"
}
*/
?>
