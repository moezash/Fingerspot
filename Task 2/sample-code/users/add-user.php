<?php
/**
 * Sample code for Adding/Setting User Information on Fingerspot Device
 *
 * This sample demonstrates how to send employee data to the
 * attendance machine using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_userinfo';

// 2. Prepare User Data
$data = [
    'trans_id'  => (string)time(), // Unique identifier
    'cloud_id'  => $cloudId,       // Target Device
    'pin'       => '101',          // Employee ID/PIN
    'name'      => 'John Doe',     // Employee Name
    'privilege' => '0',            // 0: Normal User, 1: Administrator
    'password'  => '123456',       // Optional device password
    'rfid'      => ''              // Optional RFID Card ID
    // Templates can also be included here (finger_data, face_data, etc.)
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling
if (curl_errno($ch)) {
    echo "--- Add User Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Process Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Add/Set User Information ---\n";
    echo "Sending data for PIN: " . $data['pin'] . " (" . $data['name'] . ")\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Successfully sent user data to the machine.\n";
        echo "Note: The machine will process this command and report the outcome via Webhook.\n";
    } else {
        echo "Failed to send user data.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown API error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

// 8. Close cURL session
curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request Body (JSON):
---------------------------------------------------------------------------
{
    "trans_id": "1705824000",
    "cloud_id": "FTV123456",
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "password": "123",
    "rfid": ""
}

---------------------------------------------------------------------------
Example Success Response:
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success"
}

---------------------------------------------------------------------------
Example Error Response:
---------------------------------------------------------------------------
{
    "status": false,
    "message": "Invalid Parameter"
}
---------------------------------------------------------------------------
*/
?>
