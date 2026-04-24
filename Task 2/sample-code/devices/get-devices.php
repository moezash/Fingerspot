<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token from Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Data
// Fingerspot API expects a JSON object.
// trans_id is used for tracking requests.
$data = [
    'trans_id' => '1'
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
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

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "Endpoint: $apiUrl\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Devices found:\n";
        foreach ($result['data'] as $device) {
            echo "  [+] Name: " . $device['name'] . "\n";
            echo "      Cloud ID: " . $device['cloud_id'] . "\n";
            echo "      Status: " . ($device['status'] ?? 'N/A') . "\n\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
EXAMPLE REQUEST
---------------------------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1"
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (SUCCESS)
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456789",
            "name": "Office Front",
            "status": "Online"
        }
    ]
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (ERROR)
---------------------------------------------------------------------------
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
