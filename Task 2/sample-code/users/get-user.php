<?php
/**
 * Sample code for Getting User Information from Fingerspot Device
 *
 * This sample demonstrates how to request user data (names, templates, etc.)
 * from the attendance machine.
 *
 * Note: This command is ASYNCHRONOUS. The API call only triggers the request.
 * The machine will send the actual user data to your Webhook URL.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token and Cloud ID from developer.fingerspot.io
 * - A configured Webhook URL in your developer dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    'pin'      => '101' // PIN to retrieve.
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

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Get User Information Sample ---\n";
    echo "Endpoint: $apiUrl\n";
    echo "Requesting data for PIN: " . $data['pin'] . " on Device: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Request successful. The command has been queued.\n";
        echo "The machine will send the user data to your Webhook URL shortly.\n";
    } else {
        echo "Failed to request data.\n";
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
    "trans_id": "1705824000",
    "cloud_id": "FTV123456",
    "pin": "101"
}

Example Response (Success - Command Queued):
{
    "status": true,
    "message": "Success"
}

Example Webhook Data (sent to your server later):
{
    "type": "get_userinfo",
    "cloud_id": "FTV123456",
    "trans_id": "1705824000",
    "data": {
        "pin": "101",
        "name": "John Doe",
        "privilege": "0",
        "password": "",
        "rfid": "",
        "finger": "2",
        "face": "1",
        "template": [
            {"index": 0, "type": "finger", "data": "..."},
            {"index": 1, "type": "face", "data": "..."}
        ]
    }
}
*/
?>
