<?php
/**
 * Sample code for Authentication with Fingerspot Cloud API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';
// Example URL for testing (Getting device list)
$apiUrl = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// 'Accept: application/json' and 'Content-Type: application/json' are also required.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * Fingerspot API mostly expects POST requests even for data retrieval.
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Prepare example payload
    $payload = json_encode(['trans_id' => '1']);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    /**
     * SECURITY NOTE:
     * CURLOPT_SSL_VERIFYPEER is set to true by default for production security.
     * This ensures that the SSL certificate of the Fingerspot API server is verified.
     *
     * Setting this to false is strictly for local development troubleshooting ONLY
     * if you encounter SSL certificate verification issues on your local environment.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }

    curl_close($ch);

    return [
        'code'     => $httpCode,
        'response' => $response,
        'error'    => $error_msg ?? null
    ];
}

// Displaying how headers should look
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nCalling testAuthentication...\n";
$result = testAuthentication($apiUrl, $headers);
echo "HTTP Status Code: " . $result['code'] . "\n";
echo "Response: " . $result['response'] . "\n";

if ($result['error']) {
    echo "CURL Error: " . $result['error'] . "\n";
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
    "trans_id": "1"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": []
}

Example Response (Unauthorized):
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
