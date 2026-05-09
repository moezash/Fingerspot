<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP cURL extension
 * - A valid API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the 'Authorization' header.
// It is also essential to set 'Content-Type' and 'Accept' to 'application/json'.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 *
 * @param string $url The API endpoint URL
 * @param array $headers The array of HTTP headers
 * @return array The HTTP response and status code
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // SSL Verification:
    // For local development environments, you might need to set this to false if CA certificates are not configured.
    // In production, ALWAYS set this to true for security.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['code' => 0, 'response' => $error];
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response
    ];
}

// --- Script Output ---
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: This is a configuration sample. Use these headers in all your API requests.\n";

/*
--------------------------------------------------------------------------------
Example Request:
--------------------------------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1"
}

--------------------------------------------------------------------------------
Example Response (Unauthorized):
--------------------------------------------------------------------------------
HTTP/1.1 401 Unauthorized
Content-Type: application/json

{
    "success": false,
    "message": "Unauthorized"
}

--------------------------------------------------------------------------------
Example Response (Success):
--------------------------------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "success": true,
    "message": "Success",
    "data": [...]
}
--------------------------------------------------------------------------------
*/
?>
