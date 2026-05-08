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
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Headers
// Fingerspot API expects Bearer Token in the Authorization header
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// 3. Initialize cURL
$ch = curl_init($apiUrl);

// 4. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Enable in production
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['trans_id' => '1']));

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);

curl_close($ch);

// 6. Process Result
echo "--- Fingerspot API Authentication Sample ---\n";
if ($error) {
    echo "cURL Error: $error\n";
} else {
    $decoded = json_decode($response, true);
    if ($decoded === null) {
        echo "Invalid JSON response received.\n";
    } else {
        echo "HTTP Code: $httpCode\n";
        echo "Status: " . ($decoded['status'] ? 'Success' : 'Failed') . "\n";
        echo "Message: " . ($decoded['message'] ?? 'N/A') . "\n";
    }
}

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1"
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
