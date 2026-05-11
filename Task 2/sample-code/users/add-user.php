<?php
/**
 * Sample code for Adding/Setting User Information on Fingerspot Device
 *
 * This sample demonstrates how to send employee data to the
 * attendance machine.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid Fingerspot API Token
 * - Device Cloud ID
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_userinfo';

// 2. Prepare User Data
$data = [
    'trans_id'  => (string)time(), // Unique identifier for this request
    'cloud_id'  => $cloudId,       // Device Cloud ID
    'pin'       => '101',          // Employee PIN / ID
    'name'      => 'John Doe',     // Employee Name
    'privilege' => '0',            // 0: User, 1: Admin
    'password'  => '',             // Device password (optional)
    'rfid'      => '',             // RFID Card ID (optional)
    // Biometric templates can also be included if available (finger_data, face_data, etc.)
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// SSL Verification: Disable for local testing if needed, enable for production
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling and Response Processing
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Add/Set User Information ---\n";
    echo "Sending data for PIN: " . $data['pin'] . " (" . $data['name'] . ")\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['success']) && $result['success']) {
        echo "Command sent successfully to the machine.\n";
        echo "Note: This is an asynchronous command. The machine will process it and report the result via your Webhook.\n";
    } else {
        echo "Failed to send command.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error occurred') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1700000000",
    "cloud_id": "FTV123456789",
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
    "message": "Invalid Cloud ID"
}
*/
?>
