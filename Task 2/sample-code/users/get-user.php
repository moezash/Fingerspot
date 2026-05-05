<?php
/**
 * Sample code for Requesting User Information from Fingerspot Device
 *
 * This sample demonstrates how to request full user details (including
 * templates) from an attendance machine.
 *
 * IMPORTANT: This is an asynchronous operation. The API will respond
 * with success if the command is accepted, but the actual user data
 * will be sent by the machine to your configured WEBHOOK URL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Payload
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    'pin'      => '101' // PIN of the employee to request
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

    echo "--- Fingerspot API: Get User Information (Request) ---\n";
    echo "Requesting data for PIN: " . $data['pin'] . " on device: $cloudId\n";
    echo "HTTP Status: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command successfully sent to the machine.\n";
        echo "Please monitor your WEBHOOK to receive the actual user data.\n";
    } else {
        echo "Failed to send request.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1715679300",
    "cloud_id": "FTV12345678",
    "pin": "101"
}

Example Response (Initial Success):
{
    "status": true,
    "message": "Success"
}

Example Webhook Data (Received later):
{
    "type": "get_userinfo",
    "cloud_id": "FTV12345678",
    "trans_id": "1715679300",
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "finger_data": "...",
    "face_data": "..."
}
*/
?>
