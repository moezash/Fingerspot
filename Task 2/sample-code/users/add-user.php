<?php
/**
 * Sample code for Add/Update Employee Info on Fingerspot Device
 *
 * This sample demonstrates how to upload employee details (PIN, Name)
 * to a specific attendance machine.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_userinfo';

// 2. Prepare Data
// Data to be sent to the device
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    'pin'      => '123',           // Employee ID/PIN (String)
    'name'     => 'John Doe',      // Employee Name
    'privilege'=> '0'              // 0: Normal User, 3: Admin
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Keep true for production

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Add Employee Sample ---\n";
    echo "Sending info for PIN: " . $data['pin'] . " to Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Command sent successfully to the device.\n";
        echo "Message: " . ($result['message'] ?? 'Success') . "\n";
    } else {
        echo "Failed to send command.\n";
        $errorMsg = $result['message'] ?? 'Unknown API error';
        echo "Error: " . $errorMsg . "\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/set_userinfo HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1700000000",
    "cloud_id": "FTV123456",
    "pin": "123",
    "name": "John Doe",
    "privilege": "0"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}

Example Response (Error):
{
    "status": false,
    "message": "Device Offline"
}
*/
?>
