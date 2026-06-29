<?php
/**
 * Sample code for Requesting User Information from Fingerspot Device
 *
 * This sample demonstrates how to request employee details (PIN, Name, Templates)
 * from the machine. Note that the result is returned asynchronously via Webhook.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Data
$data = [
    'trans_id' => uniqid(),
    'cloud_id' => $cloudId,
    'pin'      => '101'            // Employee PIN to request (Optional, leave empty for all users)
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Setting CURLOPT_SSL_VERIFYPEER to true for production security.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get User Information Sample ---\n";
    echo "Requesting info for PIN: " . ($data['pin'] ?: 'All Users') . " from Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Request sent successfully.\n";
        echo "IMPORTANT: The machine will send the user data to your Webhook URL.\n";
    } else {
        echo "Failed to send request.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/get_userinfo HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "65ab123456789",
    "cloud_id": "FTV123456",
    "pin": "101"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}

Note: The actual user data will be sent to your Webhook.
*/
?>
