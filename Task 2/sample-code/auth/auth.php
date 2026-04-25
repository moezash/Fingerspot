<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP cURL extension
 * - API Token from Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard (https://developer.fingerspot.io)
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header.
// Content-Type must be set to application/json as the API expects JSON bodies.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 *
 * @param string $url The API endpoint URL
 * @param array $headers The authentication headers
 * @return array The HTTP response status and body
 */
function testAuthentication($url, $headers) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local testing if needed

    // Most Fingerspot endpoints require POST, even for fetching data
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['trans_id' => '1']));

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response
    ];
}

// --- Execution Display ---
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nTo test this sample, replace 'YOUR_API_TOKEN_HERE' with your actual token.\n";

// Example of how to call the function
// $result = testAuthentication('https://developer.fingerspot.io/api/get_device', $headers);
// print_r($result);

/*
---------------------------------------------------------------------------
Example Request:
---------------------------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1"
}

---------------------------------------------------------------------------
Example Response (Unauthorized - Invalid Token):
---------------------------------------------------------------------------
HTTP/1.1 401 Unauthorized
Content-Type: application/json

{
    "status": false,
    "message": "Unauthorized"
}

---------------------------------------------------------------------------
Example Response (Success - Valid Token):
---------------------------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "status": true,
    "message": "Success",
    "data": [...]
}
*/
?>
