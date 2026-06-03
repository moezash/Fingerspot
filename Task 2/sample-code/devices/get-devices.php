<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account using the /api/get_device endpoint.
 *
 * Requirements:
 * - Pure PHP & cURL
 * - Valid API Token
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Data
// Fingerspot API usually requires a trans_id even for list requests
$data = [
    'trans_id' => (string)time()
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

/**
 * Security: CURLOPT_SSL_VERIFYPEER set to true for production.
 * If local CA certs are missing, troubleshooting may require false temporarily.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo "--- Get Device List Error ---\n";
    echo 'cURL Error: ' . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Devices found:\n";
        if (!empty($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "- SN: " . $device['cloud_id'] . "\n";
                echo "  Name  : " . $device['name'] . "\n";
                echo "  Status: " . ($device['status'] ?? 'Unknown') . "\n\n";
            }
        } else {
            echo "No devices registered in this account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
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
    "trans_id": "1719240000"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456789",
            "name": "Lobby Entrance",
            "status": "Online"
        },
        {
            "cloud_id": "FTV987654321",
            "name": "Warehouse",
            "status": "Offline"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
