<?php
/**
 * Fingerspot API Sample Code: Add/Set User Information
 *
 * This sample demonstrates how to send employee/user data to the
 * attendance machine. This can be used to add new employees or update
 * existing ones.
 *
 * Requirements:
 * - api_token: Obtain from Fingerspot Developer Dashboard
 * - cloud_id: The unique ID of your registered device
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_userinfo';

// 2. Prepare User Data
$data = [
    'trans_id'  => uniqid('setuser_'), // Unique transaction identifier
    'cloud_id'  => $cloudId,           // Device Identifier
    'pin'       => '101',              // Employee PIN / ID
    'name'      => 'John Doe',         // Employee Name
    'privilege' => '0',                // 0: Normal User, 1: Administrator
    'password'  => '',                 // Numeric password for device login (optional)
    'rfid'      => '',                 // RFID Card ID (optional)
    // Templates can be sent here as well if you have the data
    // 'finger_data' => 'HEX_OR_BASE64_TEMPLATE_DATA',
    // 'face_data'   => 'HEX_OR_BASE64_TEMPLATE_DATA'
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

/**
 * SECURITY NOTE:
 * CURLOPT_SSL_VERIFYPEER is set to true by default for production security.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Check for cURL Errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Parse and Process Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Add/Set User Information Sample ---\n";
    echo "Sending data for PIN: " . htmlspecialchars($data['pin']) . " (" . htmlspecialchars($data['name']) . ")\n";
    echo "HTTP Status Code: $httpCode\n\n";

    $success = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($success) {
        echo "Command sent successfully to the machine.\n";
        echo "Note: The machine will process this command and report the result via Webhook.\n";
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
    "trans_id": "setuser_65f1234567890",
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

Example Response (Error):
{
    "status": false,
    "message": "Invalid API Token"
}
*/
?>
