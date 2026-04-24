<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This script demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API using pure PHP and cURL.
 *
 * Requirements:
 * - PHP 7.4 or higher
 * - PHP cURL extension enabled
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
// Replace with your actual API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// and the Content-Type must be set to application/json.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

/**
 * Example function to demonstrate how these headers are used in a standard API call.
 * This function performs a simple GET request to check authentication.
 *
 * @param string $url The target API endpoint
 * @param array $headers The prepared authentication headers
 * @return array The response and HTTP status code
 */
function checkAuthentication($url, $headers) {
    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);     // Set the authentication headers
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // Skip SSL verification for local testing
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);              // Set timeout to 30 seconds

    // Execute the request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'status' => 'error',
            'message' => $error_msg
        ];
    }

    // Get HTTP status code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close cURL session
    curl_close($ch);

    return [
        'status' => 'success',
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// --- Output for Demonstration ---
echo "--- Fingerspot API Authentication Sample ---\n";
echo "1. API Token: " . (empty($apiToken) || $apiToken == 'YOUR_API_TOKEN_HERE' ? "[NOT SET]" : "********") . "\n";
echo "2. Prepared Headers:\n";
foreach ($headers as $header) {
    echo "   - $header\n";
}

echo "\nHow to use in your code:\n";
echo "Pass these headers to any cURL request targeting developer.fingerspot.io endpoints.\n";

/*
---------------------------------------------------------------------------
Example Request:
---------------------------------------------------------------------------
GET /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

---------------------------------------------------------------------------
Example Success Response:
---------------------------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "status": true,
    "message": "Success",
    "data": [...]
}

---------------------------------------------------------------------------
Example Error Response (Invalid Token):
---------------------------------------------------------------------------
HTTP/1.1 401 Unauthorized
Content-Type: application/json

{
    "status": false,
    "message": "Unauthorized"
}
---------------------------------------------------------------------------
*/
?>
