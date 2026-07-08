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
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // We use this endpoint for testing authentication

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how authentication is handled in a cURL request
 *
 * @param string $url The API endpoint
 * @param array $headers The array of HTTP headers
 * @return array The response and HTTP status code
 */
function testAuthentication($url, $headers) {
    // 3. Prepare Body
    // Most Fingerspot API endpoints expect a POST request with at least a trans_id
    $data = [
        'trans_id' => uniqid()
    ];

    $ch = curl_init($url);

    // 4. Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Setting CURLOPT_SSL_VERIFYPEER to true for production security.
    // Setting it to false is strictly for local development troubleshooting only.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // 5. Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error_msg = null;

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }

    curl_close($ch);

    return [
        'code'     => $httpCode,
        'response' => $response,
        'error'    => $error_msg
    ];
}

// --- Execution & Display ---
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nTesting authentication...\n";
$result = testAuthentication($apiUrl, $headers);

echo "HTTP Status Code: " . $result['code'] . "\n";
if ($result['error']) {
    echo "cURL Error: " . $result['error'] . "\n";
} else {
    echo "Response: " . $result['response'] . "\n";
}

echo "\nNote: Replace 'YOUR_API_TOKEN_HERE' with your actual token from the dashboard.\n";

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "65b1234567890"
}

Example Response (if token is invalid):
{
    "success": false,
    "error_code": "401",
    "message": "Unauthorized"
}

Example Response (if token is valid):
{
    "success": true,
    "message": "Success",
    "data": []
}
*/
?>
