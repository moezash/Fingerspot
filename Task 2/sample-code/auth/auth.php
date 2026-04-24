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
// Get your API Token from the Fingerspot Developer Dashboard (https://developer.fingerspot.io)
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header.
// Content-Type must be set to application/json as the API expects JSON payloads.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

/**
 * Example Request using cURL
 *
 * This function shows how to apply the headers to a standard PHP cURL request.
 */
function sendRequest($url, $headers, $payload = []) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local development environments

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

// Displaying how headers should look
echo "--- Fingerspot API Authentication Sample ---\n";
echo "The following headers must be included in every API call:\n\n";
foreach ($headers as $header) {
    echo "  [+] $header\n";
}

echo "\nNote: This is a configuration sample. Always keep your API Token secure.\n";

/*
---------------------------------------------------------------------------
EXAMPLE REQUEST (RAW HTTP)
---------------------------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1"
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (SUCCESS)
---------------------------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "status": true,
    "message": "Success",
    "data": [ ... ]
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (UNAUTHORIZED)
---------------------------------------------------------------------------
HTTP/1.1 401 Unauthorized
Content-Type: application/json

{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
