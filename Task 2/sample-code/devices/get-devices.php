<?php
/**
 * Fingerspot Cloud API - Get Device List Sample
 *
 * This sample code demonstrates how to retrieve the list of all
 * attendance machines registered under your Fingerspot Cloud account.
 *
 * Documentation: https://developer.fingerspot.io
 *
 * @author Internship Student
 */

// 1. CONFIGURATION
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. PREPARE REQUEST DATA
// Fingerspot API requests typically require a 'trans_id' to track the transaction
$requestData = [
    'trans_id' => (string)time()
];

// 3. PREPARE HEADERS
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. INITIALIZE cURL
$ch = curl_init($apiUrl);

// 5. SET cURL OPTIONS
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. EXECUTE REQUEST
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. ERROR HANDLING & OUTPUT
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Get Device List ---\n";
    echo "HTTP Status: $httpCode\n\n";

    if (isset($result['status']) && $result['status']) {
        echo "Successfully retrieved " . count($result['data']) . " device(s):\n";
        echo str_repeat("-", 50) . "\n";
        echo sprintf("%-15s | %-20s | %-10s\n", "Cloud ID", "Device Name", "Status");
        echo str_repeat("-", 50) . "\n";

        foreach ($result['data'] as $device) {
            echo sprintf(
                "%-15s | %-20s | %-10s\n",
                $device['cloud_id'],
                $device['name'],
                $device['status'] ?? 'N/A'
            );
        }
        echo str_repeat("-", 50) . "\n";
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        if (isset($result['error_code'])) {
            echo "Error Code: " . $result['error_code'] . "\n";
        }
    }
}

curl_close($ch);

/**
 * Example Request:
 *
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_TOKEN
 * Content-Type: application/json
 *
 * {
 *   "trans_id": "1717772400"
 * }
 *
 * Example Response (Success):
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": [
 *     {
 *       "cloud_id": "FTV123456789",
 *       "name": "Front Office Machine",
 *       "status": "Online"
 *     },
 *     {
 *       "cloud_id": "FTV987654321",
 *       "name": "Warehouse Machine",
 *       "status": "Offline"
 *     }
 *   ]
 * }
 */
?>
