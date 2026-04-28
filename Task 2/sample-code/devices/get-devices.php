<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This script retrieves all attendance devices registered under your account.
 *
 * Requirements:
 * - Pure PHP + cURL
 * - Valid API Token
 *
 * Documentation: https://developer.fingerspot.io
 */

// --- 1. CONFIGURATION ---
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// --- 2. PREPARE DATA & HEADERS ---
// Every request usually requires a trans_id (unique transaction identifier)
$data = [
    'trans_id' => '1'
];

$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// --- 3. INITIALIZE cURL ---
$ch = curl_init($apiUrl);

// --- 4. SET OPTIONS ---
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// --- 5. EXECUTE ---
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// --- 6. HANDLE RESPONSE ---
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "=== Get Device List Result ===\n";
    echo "HTTP Status: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Found " . count($result['data']) . " device(s):\n";
        foreach ($result['data'] as $device) {
            echo "  - SN: " . $device['cloud_id'] . " | Name: " . $device['name'] . " | Status: " . ($device['status'] ?? 'Unknown') . "\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown Error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
EXAMPLE REQUEST:
---------------------------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1"
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE:
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Lobby Entrance",
            "status": "Online"
        },
        {
            "cloud_id": "FTV987654",
            "name": "Warehouse",
            "status": "Offline"
        }
    ]
}
*/
?>
