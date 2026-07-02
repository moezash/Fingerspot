<?php
/**
 * Sample code for Requesting User Information from Fingerspot Device
 *
 * This sample demonstrates how to request employee data (PIN, Name, Templates)
 * from an attendance machine.
 *
 * IMPORTANT: The data is NOT returned in the direct API response.
 * It will be sent asynchronously to your registered Webhook.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Request Data
$data = [
    'trans_id' => uniqid(),        // Unique ID for this request
    'cloud_id' => $cloudId,        // Device Cloud ID
    'pin'      => '101'            // Employee PIN to request (use "" for all users if supported)
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
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Security: Always verify SSL in production
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo "--- Get User Info Error ---\n";
    echo 'cURL Error: ' . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get User Information Request Sample ---\n";
    echo "Requesting info for PIN: " . $data['pin'] . " from Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && ( (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']) ) ) {
        echo "Request sent successfully.\n";
        echo "Check your Webhook for the 'get_userinfo' response containing the user data.\n";
    } else {
        echo "Failed to send request.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/get_userinfo HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "65abc123d456",
    "cloud_id": "FTV123456",
    "pin": "101"
}

Example Response (API Confirmation):
{
    "success": true,
    "message": "Success"
}

Example Webhook Data (Asynchronous Result):
{
    "cloud_id": "FTV123456",
    "trans_id": "65abc123d456",
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "finger_data": "...",
    "face_data": "..."
}
*/
?>
