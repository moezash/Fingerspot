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

// 2. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Body
// 'trans_id' is a unique transaction ID. Using uniqid() is recommended.
$body = json_encode([
    'trans_id' => uniqid()
]);

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

// Security setting: In production, always set CURLOPT_SSL_VERIFYPEER to true.
// Setting it to false is strictly for local development troubleshooting only.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API usually returns success/status as true
    if ($result && (isset($result['success']) && $result['success'] || isset($result['status']) && $result['status'])) {
        echo "Devices found:\n";
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "- Cloud ID: " . $device['cloud_id'] . " | Name: " . $device['name'] . " | Status: " . ($device['status'] ?? 'Unknown') . "\n";
            }
        } else {
            echo "No devices listed in your account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Error: " . ($result['message'] ?? 'Invalid response') . "\n";
        echo "Raw Response: " . $response . "\n";
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
    "trans_id": "65ab123456789"
}

Example Response (Success):
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Main Entrance",
            "status": "Online"
        },
        {
            "cloud_id": "FTV789012",
            "name": "Back Office",
            "status": "Offline"
        }
    ]
}

Example Response (Error):
{
    "success": false,
    "error_code": "1001",
    "message": "Token expired or invalid"
}
*/
?>
