<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account using the /api/get_device endpoint.
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

// 2. Prepare Request Body
/**
 * Fingerspot API recommends sending a unique trans_id for each request.
 * We use (string)time() as a simple way to generate a unique ID.
 */
$requestBody = [
    'trans_id' => (string)time()
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));

/**
 * SSL Verification:
 * Set to true for production security.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling and Output
if (curl_errno($ch)) {
    echo "--- Get Device List Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // Decode JSON response
    $result = json_decode($response, true);

    echo "--- Fingerspot Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Standard check for success in Fingerspot API
    if ($result && isset($result['status']) && $result['status']) {
        echo "Successfully retrieved " . count($result['data']) . " device(s):\n";
        echo str_repeat("-", 50) . "\n";

        foreach ($result['data'] as $device) {
            echo "Device Name : " . $device['name'] . "\n";
            echo "Cloud ID    : " . $device['cloud_id'] . "\n";
            echo "Status      : " . ($device['status'] ?? 'N/A') . "\n";
            echo str_repeat("-", 50) . "\n";
        }
    } else {
        echo "Failed to retrieve device list.\n";
        echo "API Message: " . ($result['message'] ?? 'No message provided') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/**
 * Example Request:
 * ------------------------------------------------------------
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 * Accept: application/json
 *
 * {
 *   "trans_id": "1716736000"
 * }
 *
 * Example Response (Success):
 * ------------------------------------------------------------
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": [
 *     {
 *       "cloud_id": "FTV12345678",
 *       "name": "Main Entrance",
 *       "status": "Online"
 *     }
 *   ]
 * }
 *
 * Example Response (Error):
 * ------------------------------------------------------------
 * {
 *   "status": false,
 *   "message": "Unauthorized"
 * }
 */
?>
