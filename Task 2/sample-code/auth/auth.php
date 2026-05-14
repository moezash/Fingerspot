<?php
/**
 * Fingerspot API Sample Code: Authentication
 *
 * This sample code demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot Cloud API.
 *
 * Requirements:
 * - PHP with cURL extension
 * - Valid API Token (obtainable from Fingerspot Developer Dashboard)
 *
 * Features demonstrated:
 * - Proper header construction (Bearer Token)
 * - Basic cURL setup for Fingerspot API
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
// Your unique API Token provided by Fingerspot
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// and set Content-Type to application/json
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to perform a sample authenticated request
 *
 * @param string $url The endpoint URL
 * @param array $headers The authentication headers
 * @return array The response status and body
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    /**
     * NOTE: CURLOPT_SSL_VERIFYPEER is set to false for local testing environments
     * where SSL certificates might not be properly configured.
     * In a production environment, it is strongly recommended to set this to true.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Handle cURL errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'success' => false,
            'message' => "cURL Error: $error_msg"
        ];
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response
    ];
}

// --- Sample Output ---
echo "--- Fingerspot API Authentication Setup ---\n";
echo "The following headers must be included in all API calls:\n\n";

foreach ($headers as $header) {
    echo "  $header\n";
}

echo "\nMake sure to replace 'YOUR_API_TOKEN_HERE' with your actual token.\n";

/*
---------------------------------------------------------------------------
Example Request (as sent by cURL):
---------------------------------------------------------------------------
GET /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer 85|v58yE7... (your actual token)
Content-Type: application/json
Accept: application/json

---------------------------------------------------------------------------
Example Response (Success - Valid Token):
---------------------------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "success": true,
    "message": "Success",
    "data": [...]
}

---------------------------------------------------------------------------
Example Response (Error - Invalid/Expired Token):
---------------------------------------------------------------------------
HTTP/1.1 401 Unauthorized
Content-Type: application/json

{
    "success": false,
    "message": "Unauthorized"
}
---------------------------------------------------------------------------
*/
?>
