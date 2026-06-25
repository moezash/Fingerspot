<?php
/**
 * Sample code for Authentication with Fingerspot Cloud API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - API Token (from Fingerspot Developer Dashboard)
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// Content-Type: application/json is mandatory as all requests send JSON bodies
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * This example uses the 'get_device' endpoint to test the authentication.
 *
 * @param string $apiToken Your Fingerspot API Token
 * @param array $headers Prepared HTTP headers
 * @return array Response details
 */
function testAuthentication($apiToken, $headers) {
    $apiUrl = 'https://developer.fingerspot.io/api/get_device';

    // Body data (most Fingerspot endpoints expect at least a trans_id)
    $data = [
        'trans_id' => uniqid()
    ];

    $ch = curl_init($apiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    /**
     * SECURITY NOTE:
     * CURLOPT_SSL_VERIFYPEER should be set to true in production for secure communication.
     * Set it to false ONLY during local development troubleshooting if you encounter SSL certificate issues.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['success' => false, 'message' => $error];
    }

    curl_close($ch);

    return [
        'success' => ($httpCode === 200),
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// --- Execution & Display ---

echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: This script is a configuration sample and template for your API requests.\n";

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "65a1234567890"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [...]
}

Example Response (Unauthorized/Invalid Token):
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
