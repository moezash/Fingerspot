<?php
/**
 * Sample code for Restarting Machine Remotely
 *
 * This sample demonstrates how to reboot the attendance machine from the cloud.
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
$apiUrl   = 'https://developer.fingerspot.io/api/restart';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId
];

// 3. Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);
    echo "--- Restart Machine Sample ---\n";
    echo "Endpoint: $apiUrl\n";
    echo "Target Device: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Restart command sent successfully. The machine will reboot shortly.\n";
    } else {
        echo "Failed to send restart command.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/restart HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1705824000",
    "cloud_id": "FTV123456"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
