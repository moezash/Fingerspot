<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - Pure PHP + cURL only
 * - Beginner-friendly and professional
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// and set both Content-Type and Accept to application/json.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * This is a template for other API calls.
 *
 * @param string $url The endpoint URL
 * @param array $headers The authorization and content headers
 * @return array The response status code and body
 */
function testAuthentication($url, $headers) {
    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);     // Set the custom headers
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for local testing (Enable in production!)

    // Execute request
    $response = curl_exec($ch);

    // Get HTTP response code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close cURL session
    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response
    ];
}

// Displaying how headers should look for educational purposes
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: This is a configuration sample. Use these headers in all your API requests.\n";

/*
---------------------------------------------------------------------------
Example Request:
---------------------------------------------------------------------------
GET /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

---------------------------------------------------------------------------
Example Response (Success):
---------------------------------------------------------------------------
{
    "success": true,
    "message": "Success",
    "data": [...]
}

---------------------------------------------------------------------------
Example Response (Unauthorized):
---------------------------------------------------------------------------
{
    "success": false,
    "message": "Unauthorized"
}
*/
?>
