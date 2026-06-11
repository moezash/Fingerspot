<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// and must specify Content-Type: application/json
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Example of how to use these headers in a cURL request
 * Note: This is a conceptual helper to show the cURL setup.
 */
function callFingerspotAPI($url, $headers, $payload = null) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Set to true for production security

    // Most Fingerspot API endpoints use POST
    curl_setopt($ch, CURLOPT_POST, true);
    if ($payload) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response,
        'error' => $error_msg ?? null
    ];
}

// Displaying configuration for the user
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in all cURL requests:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: Setting 'CURLOPT_SSL_VERIFYPEER' to false is only for local troubleshooting.\n";
echo "Always use true in production to ensure secure communication.\n";

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
    "data": [...]
}

Example Response (Unauthorized/Invalid Token):
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
