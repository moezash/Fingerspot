<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Documentation: https://developer.fingerspot.io
 *
 * Example Request:
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 *
 * Example Response (Success):
 * {
 *     "status": true,
 *     "message": "Success",
 *     "data": [...]
 * }
 *
 * Example Response (Error):
 * {
 *     "status": false,
 *     "message": "Unauthorized"
 * }
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request
 *
 * @param string $url The endpoint URL
 * @param array $headers The authentication headers
 * @return array The response and HTTP code
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Set to true for production security.
    // Set to false strictly for local development troubleshooting only.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Using POST as Fingerspot API typically expects POST for most operations
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
            'response' => null,
            'error' => $error
        ];
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// Displaying how headers should look
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nTesting authentication with example endpoint...\n";
$testResult = testAuthentication('https://developer.fingerspot.io/api/get_device', $headers);

echo "HTTP Status Code: " . $testResult['code'] . "\n";
if (isset($testResult['error'])) {
    echo "cURL Error: " . $testResult['error'] . "\n";
} else {
    echo "Response Message: " . ($testResult['response']['message'] ?? 'No message') . "\n";
}

echo "\nNote: This is a configuration sample. Use these headers in all your API requests.\n";
?>
