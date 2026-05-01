<?php
/**
 * Sample code for Adding/Setting User Information on Fingerspot Device
 *
 * This sample demonstrates how to send employee data to the
 * attendance machine.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token and Cloud ID from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_userinfo';

// 2. Prepare User Data
$data = [
    'trans_id'  => (string)time(), // Unique ID for this request
    'cloud_id'  => $cloudId,       // Device Cloud ID
    'pin'       => '101',          // Employee PIN / ID (max 9 digits usually)
    'name'      => 'John Doe',     // Employee Name (max 24 characters usually)
    'privilege' => '0',            // 0: Normal User, 1: Administrator
    'password'  => '',             // Device password (optional, max 6 digits)
    'rfid'      => '',             // RFID Card ID (optional, 10 digits hex or decimal)
    // Templates can be sent here as well if you have the hex/base64 data
    // 'finger_data' => [
    //     ['index' => 0, 'template' => '...'],
    //     ['index' => 1, 'template' => '...']
    // ],
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Add/Set User Information Sample ---\n";
    echo "Endpoint: $apiUrl\n";
    echo "Sending data for PIN: " . $data['pin'] . " (" . $data['name'] . ")\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command sent successfully to the cloud.\n";
        echo "The machine will receive and process this command shortly.\n";
        echo "Result of execution on the machine will be sent to your Webhook.\n";
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
    "trans_id": "1705824000",
    "cloud_id": "FTV123456",
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
