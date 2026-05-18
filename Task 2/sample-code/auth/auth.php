<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API using pure PHP and cURL.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token from Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header.
// 'Content-Type: application/json' is required for POST requests with body data.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request
 *
 * @param string $url The endpoint URL
 * @param array $headers The array of HTTP headers
 * @return array The response status and body
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Disable SSL verification for local development environments if needed.
    // WARNING: Set to true in production for better security.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'success' => false,
            'message' => "cURL Error: $error"
        ];
    }

    curl_close($ch);

    return [
        'success' => ($httpCode === 200),
        'code' => $httpCode,
        'response' => $response
    ];
}

// Displaying how headers should be configured
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nUsage Example:\n";
echo "\$ch = curl_init('https://developer.fingerspot.io/api/get_device');\n";
echo "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n";
echo "...\n";

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
    "success": false,
    "message": "Unauthorized"
}

Example Response (if token is valid):
{
    "success": true,
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Office Main"
        }
    ]
}
*/
?>
