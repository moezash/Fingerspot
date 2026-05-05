<?php
/**
 * Sample code for Adding/Setting User Information on Fingerspot Device
 *
 * This sample demonstrates how to send employee data (PIN and Name)
 * to the attendance machine using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // Target device Cloud ID
$apiUrl   = 'https://developer.fingerspot.io/api/set_userinfo';

// 2. Prepare User Data
// Note: Privilege 0 = Normal User, 1 = Administrator
$data = [
    'trans_id'  => (string)time(),
    'cloud_id'  => $cloudId,
    'pin'       => '101',          // Unique Employee PIN
    'name'      => 'John Doe',     // Employee Name
    'privilege' => '0',            // 0: User, 1: Admin
    'password'  => '',             // Optional device password
    'rfid'      => ''              // Optional RFID card number
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize and Execute cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 5. Error Handling and Output
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Add/Set User Information ---\n";
    echo "Sending data for PIN: " . $data['pin'] . " (" . $data['name'] . ")\n";
    echo "HTTP Status: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command successfully accepted by the server.\n";
        echo "The data will be pushed to the device shortly.\n";
        echo "Check your Webhook for the execution result from the device.\n";
    } else {
        echo "Failed to send user data.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1715679100",
    "cloud_id": "FTV12345678",
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "password": "",
    "rfid": ""
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
