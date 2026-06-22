<?php
/**
 * Sample code for Get Device List from Fingerspot Cloud API
 *
 * This sample demonstrates how to retrieve the list of attendance machines
 * registered in your Fingerspot account.
 *
 * Requirements: Pure PHP + cURL
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
// Every request usually requires a trans_id (transaction identifier)
$data = [
    'trans_id' => '1'
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Security: SSL Verification
// In production, CURLOPT_SSL_VERIFYPEER must be set to true.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API indicates success with 'status' or 'success' key
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($isSuccess && isset($result['data'])) {
        echo "Devices found (" . count($result['data']) . "):\n";
        echo str_pad("SN (Cloud ID)", 15) . " | " . str_pad("Device Name", 20) . " | Status\n";
        echo str_repeat("-", 50) . "\n";

        foreach ($result['data'] as $device) {
            echo str_pad($device['cloud_id'], 15) . " | " .
                 str_pad($device['name'], 20) . " | " .
                 ($device['status'] ?? 'N/A') . "\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error occurred') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

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
 *     "trans_id": "1"
 * }
 *
 * Example Response (Success):
 * {
 *     "status": true,
 *     "message": "Success",
 *     "data": [
 *         {
 *             "cloud_id": "FTV123456",
 *             "name": "Office Front",
 *             "status": "Online"
 *         },
 *         {
 *             "cloud_id": "FTV789012",
 *             "name": "Warehouse",
 *             "status": "Offline"
 *         }
 *     ]
 * }
 */
?>
