<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Documentation: https://developer.fingerspot.io
 *
 * Example Request:
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 *
 * {
 *     "trans_id": "65a1234567890"
 * }
 *
 * Example Response (Success):
 * {
 *     "status": true,
 *     "message": "Success",
 *     "data": [
 *         {
 *             "cloud_id": "FTV123456",
 *             "name": "Main Entrance",
 *             "status": "Online"
 *         },
 *         {
 *             "cloud_id": "FTV789012",
 *             "name": "Back Office",
 *             "status": "Offline"
 *         }
 *     ]
 * }
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
$body = [
    'trans_id' => uniqid() // Unique transaction ID
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

// Set to true for production security.
// Set to false strictly for local development troubleshooting only.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API may use 'status' or 'success' key
    $isSuccess = (isset($result['status']) && $result['status']) ||
                 (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Devices found:\n";
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "- Cloud ID: " . $device['cloud_id'] . "\n";
                echo "  Name    : " . ($device['name'] ?? 'N/A') . "\n";
                echo "  Status  : " . ($device['status'] ?? 'Unknown') . "\n\n";
            }
        } else {
            echo "No device data available in response.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message : " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);
?>
