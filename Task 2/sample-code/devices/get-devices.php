<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Request Body
// Fingerspot API expects a POST request even for fetching lists.
// trans_id is a unique identifier for the transaction.
$data = [
    'trans_id' => (string)time()
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// SSL verification: Set to false for local testing if needed
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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

    if ($result && isset($result['status']) && $result['status']) {
        echo "Devices found:\n";
        if (!empty($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "- Cloud ID: " . ($device['cloud_id'] ?? 'N/A') . "\n";
                echo "  Name    : " . ($device['name'] ?? 'N/A') . "\n";
                echo "  Status  : " . ($device['status'] ?? 'N/A') . "\n";
                echo "--------------------------\n";
            }
        } else {
            echo "No devices registered in your account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
--------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1700000000"
}

Example Response (Success):
--------------------------------------------------
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Front Office",
            "status": "Online"
        },
        {
            "cloud_id": "FTV789012",
            "name": "Warehouse",
            "status": "Offline"
        }
    ]
}

Example Response (Error):
--------------------------------------------------
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
