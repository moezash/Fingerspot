<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot Cloud account.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Data
// Even for simple GET-like operations, Fingerspot API often expects a POST request
// with at least a transaction ID (trans_id).
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disabled for compatibility; enable in production

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Result ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status'] === true) {
        echo "Successfully retrieved " . count($result['data']) . " device(s):\n";
        echo str_repeat("-", 50) . "\n";
        echo sprintf("%-15s | %-20s | %-10s\n", "Cloud ID", "Device Name", "Status");
        echo str_repeat("-", 50) . "\n";

        foreach ($result['data'] as $device) {
            echo sprintf(
                "%-15s | %-20s | %-10s\n",
                $device['cloud_id'],
                $device['name'],
                $device['status']
            );
        }
        echo str_repeat("-", 50) . "\n";
    } else {
        echo "Failed to retrieve device list.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unexpected API response') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/**
 * Example Request:
 * ----------------
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 *
 * {
 *   "trans_id": "1672531200"
 * }
 *
 * Example Response (Success):
 * ---------------------------
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": [
 *     {
 *       "cloud_id": "FTV0001",
 *       "name": "Main Entrance",
 *       "status": "Online"
 *     },
 *     {
 *       "cloud_id": "FTV0002",
 *       "name": "Staff Room",
 *       "status": "Offline"
 *     }
 *   ]
 * }
 *
 * Example Response (Error):
 * -------------------------
 * {
 *   "status": false,
 *   "message": "Unauthorized"
 * }
 */
?>
