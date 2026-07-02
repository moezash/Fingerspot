<?php
/**
 * Sample code for Adding/Updating User Information on Fingerspot Device
 *
 * This sample demonstrates how to send employee data to the
 * attendance machine.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_userinfo';

// 2. Prepare User Data
$data = [
    'trans_id'  => uniqid(),       // Unique ID for this request
    'cloud_id'  => $cloudId,       // Device Cloud ID
    'pin'       => '101',          // Employee PIN / ID
    'name'      => 'John Doe',     // Employee Name
    'privilege' => '0',            // 0: User, 1: Admin
    'password'  => '',             // Device password (optional)
    'rfid'      => '',             // RFID Card ID (optional)
    // Templates can be sent here as well if you have the hex/base64 data
    // 'finger_data' => '...',
    // 'face_data'   => '...'
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
    echo "--- Add User Error ---\n";
    echo 'cURL Error: ' . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Add/Set User Information Sample ---\n";
    echo "Sending data for PIN: " . $data['pin'] . " (" . $data['name'] . ") to Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && ( (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']) ) ) {
        echo "Command sent successfully to the cloud.\n";
        echo "Note: The machine will process this command and report the actual execution result via your Webhook.\n";
    } else {
        echo "Failed to send command.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "65ab123456789",
    "cloud_id": "FTV123456",
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "password": "",
    "rfid": ""
}

Example Response (Success):
{
    "success": true,
    "message": "Success"
}

Example Response (Error):
{
    "success": false,
    "error_code": "1002",
    "message": "Invalid Cloud ID"
}
*/
?>
