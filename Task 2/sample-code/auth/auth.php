<?php
/**
 * Fingerspot API Sample Code: Authentication
 *
 * This script demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot Cloud API.
 *
 * Documentation: https://developer.fingerspot.io
 * Requirements: Pure PHP, cURL extension
 */

// 1. API Configuration
// Obtain your Bearer Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include 'Authorization' and 'Content-Type' headers.
// The 'Accept: application/json' header ensures the API returns JSON responses.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Example function showing how to use the authentication headers in a cURL request
 */
function callFingerspotAPI($url, $headers, $payload = []) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    // IMPORTANT: In a production environment, always enable SSL verification.
    // For local development, you might need to set this to false.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "cURL Error: " . $error;
    }

    curl_close($ch);
    return [
        'http_code' => $httpCode,
        'response'  => json_decode($response, true)
    ];
}

// --- Displaying Sample Usage ---
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Required Headers:\n";
foreach ($headers as $header) {
    echo "  - $header\n";
}

echo "\nExample implementation of a request:\n";
echo "1. Initialize cURL\n";
echo "2. Set CURLOPT_HTTPHEADER with the array above\n";
echo "3. Send request as POST with JSON body\n";

/*
---------------------------------------------------------
EXAMPLE REQUEST (RAW HTTP)
---------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1"
}

---------------------------------------------------------
EXAMPLE RESPONSE (JSON)
---------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "status": true,
    "message": "Success",
    "data": [ ... ]
}

OR (If Unauthorized)

HTTP/1.1 401 Unauthorized
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
