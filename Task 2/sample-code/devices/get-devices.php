<?php
/**
 * Fingerspot API Sample Code: Get Device List
 *
 * This script demonstrates how to retrieve the list of all attendance machines
 * registered under your Fingerspot Cloud account.
 *
 * Documentation: https://developer.fingerspot.io
 * Requirements: Pure PHP, cURL extension
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Request Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Request Body
// A trans_id is generally required to track the request transaction.
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

// IMPORTANT: Set to false for local development if SSL issues occur.
// Enable (true) in production for security.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Error Handling & Response Processing
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // Decode the JSON response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result === null) {
        echo "Error: Invalid JSON response received.\n";
        echo "Raw Response: " . $response . "\n";
    } elseif (isset($result['status']) && $result['status']) {
        echo "Successfully retrieved device list:\n";

        if (!empty($result['data'])) {
            foreach ($result['data'] as $index => $device) {
                echo ($index + 1) . ". Name: " . ($device['name'] ?? 'N/A') . "\n";
                echo "   Cloud ID: " . ($device['cloud_id'] ?? 'N/A') . "\n";
                echo "   Status  : " . ($device['status'] ?? 'Unknown') . "\n";
                echo "---------------------------\n";
            }
        } else {
            echo "No devices registered in your account.\n";
        }
    } else {
        echo "API Error: " . ($result['message'] ?? 'Unknown error occurred') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

// 8. Close cURL Session
curl_close($ch);

/*
---------------------------------------------------------
EXAMPLE REQUEST (RAW HTTP)
---------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1700000000"
}

---------------------------------------------------------
EXAMPLE RESPONSE (JSON)
---------------------------------------------------------
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456789",
            "name": "Main Office Entry",
            "status": "Online"
        },
        {
            "cloud_id": "FTV987654321",
            "name": "Warehouse Exit",
            "status": "Offline"
        }
    ]
}
*/
?>
