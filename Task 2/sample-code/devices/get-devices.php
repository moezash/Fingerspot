<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account and their current status.
 *
 * Requirements:
 * - PHP cURL extension
 * - API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Data
// Most Fingerspot API endpoints require a trans_id (transaction identifier)
$data = [
    'trans_id' => uniqid()
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

// Security: Always verify SSL in production.
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

    echo "--- Get Device List Sample ---\n";
    echo "Endpoint: $apiUrl\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API usually returns success in 'status' or 'success' key
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($isSuccess && isset($result['data'])) {
        echo "Devices found (" . count($result['data']) . "):\n";
        echo str_repeat("-", 50) . "\n";
        echo sprintf("%-15s | %-20s | %-10s\n", "Cloud ID", "Device Name", "Status");
        echo str_repeat("-", 50) . "\n";

        foreach ($result['data'] as $device) {
            echo sprintf(
                "%-15s | %-20s | %-10s\n",
                $device['cloud_id'],
                $device['name'],
                $device['status'] ?? 'Unknown'
            );
        }
        echo str_repeat("-", 50) . "\n";
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Error Message: " . ($result['message'] ?? 'Check your API Token or network.') . "\n";
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
Accept: application/json

{
    "trans_id": "65e6d8a7c2e9b"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Main Entrance",
            "status": "Online",
            "webhook_url": "https://yourdomain.com/webhook.php"
        },
        {
            "cloud_id": "FTV789012",
            "name": "Back Office",
            "status": "Offline",
            "webhook_url": ""
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "message": "Invalid Token"
}
*/
?>
