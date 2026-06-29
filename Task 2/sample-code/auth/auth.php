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

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// Content-Type and Accept headers should be set to application/json
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request
 *
 * @param string $url The API endpoint URL
 * @param array $headers The authentication and content headers
 * @param array|null $data The data to be sent in the POST request body
 * @return array The HTTP status code and the response body
 */
function testAuthentication($url, $headers, $data = null) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Setting CURLOPT_SSL_VERIFYPEER to true for production security.
    // Set to false only for local development troubleshooting if encountering SSL certificate issues.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Fingerspot API expects POST requests for almost all endpoints
    curl_setopt($ch, CURLOPT_POST, true);
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'code' => $httpCode,
            'response' => $error,
            'success' => false
        ];
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response,
        'success' => $httpCode === 200
    ];
}

// Displaying how headers should look
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nExample Usage:\n";
echo "1. Initialize cURL with the target endpoint.\n";
echo "2. Set the Authorization header with your Bearer Token.\n";
echo "3. Use POST method for sending data.\n";

echo "\nNote: This is a configuration sample. Use these headers in all your API requests.\n";

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "123456"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [...]
}

Example Response (Unauthorized):
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
