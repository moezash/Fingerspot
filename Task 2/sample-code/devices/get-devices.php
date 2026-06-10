<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Requirements:
 * - PHP cURL extension
 * - Valid API Token from developer.fingerspot.io
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
// For /api/get_device, a trans_id is required to identify the transaction.
$body = [
    'trans_id' => uniqid()
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

// SSL Verification:
// For production, always set to true. Use false only for local development troubleshooting.
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
    echo "HTTP Status Code: $httpCode\n\n";

    // Check success status (Fingerspot uses 'status' or 'success' key)
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($isSuccess && isset($result['data'])) {
        echo "Devices found:\n";
        foreach ($result['data'] as $device) {
            echo "- SN: " . $device['cloud_id'] . "\n";
            echo "  Name: " . $device['name'] . "\n";
            echo "  Status: " . ($device['status'] ?? 'Unknown') . "\n";
            echo "---------------------------\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Response: " . $response . "\n";
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
    "trans_id": "65ab1234c5678"
}

Example Response (Success):
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456789",
            "name": "Main Office",
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
    "success": false,
    "error_code": "401",
    "message": "Invalid Token"
}
*/
?>
