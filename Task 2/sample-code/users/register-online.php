<?php
/**
 * Sample code for Remote Registration (Register Online)
 *
 * This sample demonstrates how to trigger the machine to start
 * a registration process for a specific user (finger, face, etc.)
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
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Data
$data = [
    'trans_id'     => (string)time(),
    'cloud_id'     => $cloudId,
    'pin'          => '101',
    'verification' => '0' // 0-9: Finger index, 12: Face, 13: Palm Vein
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

    echo "--- Register Online Sample ---\n";
    echo "Endpoint: $apiUrl\n";
    echo "Triggering registration for PIN " . $data['pin'] . " (Mode: " . $data['verification'] . ")\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command successful. The machine will now enter registration mode.\n";
        echo "The user should follow instructions on the machine screen.\n";
        echo "The registration result will be sent to your Webhook.\n";
    } else {
        echo "Failed to trigger registration.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/reg_online HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1705824000",
    "cloud_id": "FTV123456",
    "pin": "101",
    "verification": "12"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
