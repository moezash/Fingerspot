<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// and specify that the content being sent/received is JSON.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * This is a template for making authenticated requests.
 *
 * @param string $url The API endpoint URL
 * @param array $headers The array of HTTP headers
 * @return array The HTTP response code and body
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    /**
     * SSL Verification:
     * Set to 'true' for production environments to ensure secure communication.
     * If you are troubleshooting in a local development environment without updated CA certs,
     * you might temporarily set this to 'false', but this is NOT recommended for production.
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
            'response' => "cURL Error: " . $error
        ];
    }

    // Always close the cURL handle to free up resources
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

// Demonstration: Attempting to call the get_device endpoint as a test
// Note: This will result in an Unauthorized error if the token is not replaced.
$testUrl = 'https://developer.fingerspot.io/api/get_device';
echo "\nTesting authentication logic with: $testUrl\n";
$result = testAuthentication($testUrl, $headers);

echo "HTTP Code: " . $result['code'] . "\n";
echo "Response: " . $result['response'] . "\n";

/*
Example Request Headers:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

Example Response (if token is invalid - 401 Unauthorized):
{
    "status": false,
    "message": "Unauthorized"
}

Example Response (if token is valid - 200 OK):
{
    "status": true,
    "message": "Success",
    "data": []
}
*/
?>
