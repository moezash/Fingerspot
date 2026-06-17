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
// Replace with your actual API Token
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header.
// 'Content-Type: application/json' is required for POST requests with JSON body.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Example function showing how headers are integrated into a cURL request.
 *
 * @param string $url The endpoint URL
 * @param array $headers The array of HTTP headers
 * @return array The response and HTTP status code
 */
function callFingerspotApi($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    /**
     * Security Note:
     * CURLOPT_SSL_VERIFYPEER is set to true by default for production security.
     * Setting it to false is strictly for local development troubleshooting ONLY
     * if you encounter SSL certificate issues.
     */
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
        'response' => json_decode($response, true)
    ];
}

// Displaying the configuration for educational purposes
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: This is a configuration guide. Ensure these headers are present in all API calls.\n";

/*
Example Request:
--------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1"
}

Example Response (Successful Auth):
--------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "status": true,
    "message": "Success",
    "data": [...]
}

Example Response (Failed Auth):
--------------------------------------------------
HTTP/1.1 401 Unauthorized
Content-Type: application/json

{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
