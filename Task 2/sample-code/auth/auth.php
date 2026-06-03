<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - Pure PHP & cURL (no frameworks)
 * - API Token from Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// Content-Type: application/json is also mandatory for POST requests
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    /**
     * Security Recommendation:
     * CURLOPT_SSL_VERIFYPEER should be set to true in production for security.
     * If you face issues in local development (e.g. missing CA certificates),
     * setting it to false is strictly for troubleshooting only.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'code' => $httpCode,
            'error' => $error,
            'success' => false
        ];
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response,
        'success' => true
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
GET /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

Example Response (if token is invalid):
{
    "status": false,
    "message": "Unauthorized"
}

Example Response (if token is valid):
{
    "status": true,
    "data": [
        {
            "cloud_id": "FTV123456789",
            "name": "Office Machine"
        }
    ]
}
*/
?>
