<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Requirements:
 * - Pure PHP + cURL
 * - Valid API Token from Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Data
// The API often requires a 'trans_id' to uniquely identify the request.
$data = [
    'trans_id' => (string)time() // Using timestamp as a unique transaction ID
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

// NOTE: In a local development environment, you might need to disable SSL verification
// if you encounter certificate issues. However, for production, ALWAYS enable it.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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
    echo "HTTP Status Code: $httpCode\n\n";

    // Standard Fingerspot API success check
    if ($result && isset($result['success']) && $result['success']) {
        echo "Devices found:\n";
        if (isset($result['data']) && !empty($result['data'])) {
            foreach ($result['data'] as $device) {
                // cloud_id is the unique identifier for the machine
                echo "- SN: " . $device['cloud_id'] . " | Name: " . $device['name'] . " | Status: " . ($device['status'] ?? 'Unknown') . "\n";
            }
        } else {
            echo "No devices registered in this account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Invalid response from server') . "\n";
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
    "trans_id": "1710000000"
}

Example Response (Success):
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Main Office",
            "status": "Online"
        },
        {
            "cloud_id": "FTV789012",
            "name": "Warehouse",
            "status": "Offline"
        }
    ]
}

Example Response (Unauthorized):
{
    "success": false,
    "message": "Unauthorized"
}
*/
?>
