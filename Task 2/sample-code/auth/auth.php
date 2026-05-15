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
// and use application/json for content types.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request
 * Fingerspot API typically uses POST for all data-related endpoints.
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Prepare a simple payload as Fingerspot API expects JSON in most requests
    $data = ['trans_id' => (string)time()];

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // NOTE: In a local development environment, you might need to disable SSL verification
    // if you encounter certificate issues. However, for production, ALWAYS enable it.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer 1234567890abcdef...
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1710000000"
}

Example Response (Success):
{
    "success": true,
    "message": "Success",
    "data": [...]
}

Example Response (Unauthorized):
{
    "success": false,
    "message": "Unauthorized"
}
*/
?>
