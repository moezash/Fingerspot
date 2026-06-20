<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
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
// A trans_id (Transaction ID) is recommended for tracking requests
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);

// 7. Check for cURL errors
if ($curlErr) {
    echo "cURL Error: " . $curlErr . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && (isset($result['status']) && $result['status'] || isset($result['success']) && $result['success'])) {
        echo "Devices retrieved successfully:\n";

        if (!empty($result['data'])) {
            foreach ($result['data'] as $device) {
                $status = $device['status'] ?? 'Unknown';
                echo "- Cloud ID: " . $device['cloud_id'] . " | Name: " . $device['name'] . " | Status: " . $status . "\n";
            }
        } else {
            echo "No devices registered in this account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
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
    "trans_id": "1704067200"
}

Example Response (Success):
{
    "status": true,
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
    "status": false,
    "message": "Unauthorized"
}
*/
?>
