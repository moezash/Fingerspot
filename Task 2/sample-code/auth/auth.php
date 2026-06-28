<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';
$testUrl  = 'https://developer.fingerspot.io/api/get_device'; // Example URL for testing

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// Content-Type must be application/json as the API expects JSON payloads
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * Fingerspot API typically requires POST method for most endpoints.
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    /**
     * SECURITY NOTE:
     * CURLOPT_SSL_VERIFYPEER is set to true by default for production security.
     * This ensures the SSL certificate of the Fingerspot API server is verified.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Most Fingerspot API endpoints require POST
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['trans_id' => uniqid()]));

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'code' => $httpCode,
            'error' => $error
        ];
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

echo "\nTesting authentication with endpoint: $testUrl\n";
$result = testAuthentication($testUrl, $headers);

if (isset($result['error'])) {
    echo "Connection Error: " . $result['error'] . "\n";
} else {
    echo "HTTP Status Code: " . $result['code'] . "\n";
    echo "API Response: " . $result['response'] . "\n";
}

echo "\nNote: This is a configuration sample. Use these headers in all your API requests.\n";

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "65a123456789b"
}

Example Response (if token is invalid):
{
    "status": false,
    "message": "Unauthorized"
}

Example Response (if token is valid):
{
    "status": true,
    "message": "Success",
    "data": [...]
}
*/
?>
