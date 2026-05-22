<?php
/**
 * Sample code for Set Time (Sync Time) on Fingerspot Device
 *
 * This sample demonstrates how to synchronize the device's date and time
 * with the server time or a specific timezone.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_time';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    // Set to current server time or specific time
    'time'     => date('Y-m-d H:i:s')
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Set Time Sample ---\n";
    echo "Syncing time: " . $data['time'] . " to Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Time sync command sent successfully.\n";
    } else {
        echo "Failed to sync time.\n";
        $errorMsg = $result['message'] ?? 'Unknown API error';
        echo "Error: " . $errorMsg . "\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/set_time HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1700000000",
    "cloud_id": "FTV123456",
    "time": "2024-01-01 10:00:00"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
