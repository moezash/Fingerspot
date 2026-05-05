<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Payload
// trans_id is a unique transaction identifier (usually timestamp or incrementing number)
$payload = [
    'trans_id' => (string)time()
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
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Disable SSL verification for local testing environments if necessary
// Comment this out or set to true in production
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 5. Error Handling and Output
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Device List ---\n";
    echo "HTTP Status: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Devices Found (" . count($result['data']) . "):\n";
        echo str_repeat("-", 50) . "\n";
        echo sprintf("%-15s | %-20s | %-10s\n", "Cloud ID", "Device Name", "Status");
        echo str_repeat("-", 50) . "\n";

        foreach ($result['data'] as $device) {
            echo sprintf("%-15s | %-20s | %-10s\n",
                $device['cloud_id'],
                $device['name'],
                $device['status']
            );
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1715678955"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV12345678",
            "name": "Main Entrance",
            "status": "Online"
        },
        {
            "cloud_id": "FTV87654321",
            "name": "Back Office",
            "status": "Offline"
        }
    ]
}
*/
?>
