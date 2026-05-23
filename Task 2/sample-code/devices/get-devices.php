<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// --- 1. Configuration ---
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// --- 2. Prepare Data ---
/**
 * Even for listing requests, a trans_id is recommended for tracking.
 * trans_id should be a unique string, often using the current timestamp.
 */
$requestData = [
    'trans_id' => (string)time()
];

// --- 3. Prepare Headers ---
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// --- 4. Initialize and Configure cURL ---
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

/**
 * Production Security: Keep CURLOPT_SSL_VERIFYPEER as true.
 * Use false only for local troubleshooting of SSL certificate issues.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// --- 5. Execute Request ---
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// --- 6. Check for Errors and Process Response ---
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API usually returns a 'status' boolean field
    if ($result && isset($result['status']) && $result['status']) {
        echo "Successfully retrieved " . count($result['data']) . " devices:\n";

        if (!empty($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "------------------------------------------\n";
                echo "Device Name : " . ($device['name'] ?? 'N/A') . "\n";
                echo "Cloud ID    : " . ($device['cloud_id'] ?? 'N/A') . "\n";
                echo "Status      : " . ($device['status'] ?? 'Unknown') . "\n";
            }
            echo "------------------------------------------\n";
        } else {
            echo "No devices found in your account.\n";
        }
    } else {
        echo "Failed to retrieve device list.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full API Response: " . $response . "\n";
    }
}

// --- 7. Close Connection ---
curl_close($ch);

/**
 * Example Request:
 *
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 *
 * {
 *    "trans_id": "1700000000"
 * }
 *
 * Example Response (Success):
 * {
 *    "status": true,
 *    "message": "Success",
 *    "data": [
 *        {
 *            "cloud_id": "FTV123456789",
 *            "name": "Office Front Door",
 *            "status": "Online"
 *        },
 *        {
 *            "cloud_id": "FTV987654321",
 *            "name": "Warehouse Entrance",
 *            "status": "Offline"
 *        }
 *    ]
 * }
 *
 * Example Response (Failure):
 * {
 *    "status": false,
 *    "message": "Invalid API Token"
 * }
 */
?>
