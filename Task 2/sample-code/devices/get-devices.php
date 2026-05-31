<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Data
// Even for listing, a trans_id is required to track the transaction.
$data = [
    'trans_id' => (string)time() // Using timestamp as a unique transaction identifier
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

// Set CURLOPT_SSL_VERIFYPEER to true for production security.
// Setting it to false is strictly for local development troubleshooting only.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// All Fingerspot Cloud API requests use the POST method.
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

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
    echo "HTTP Status Code: $httpCode\n\n";

    // Checking for both 'status' or 'success' keys for robustness.
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Devices found:\n";
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                $status = $device['status'] ?? 'Unknown';
                echo "- SN: " . $device['cloud_id'] . " | Name: " . $device['name'] . " | Status: " . $status . "\n";
            }
        } else {
            echo "No devices found in this account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/**
 * Example Request Body:
 * {
 *     "trans_id": "1710123456"
 * }
 */

/**
 * Example Response (Success):
 * {
 *     "status": true,
 *     "message": "Success",
 *     "data": [
 *         {
 *             "cloud_id": "FTV1234567890",
 *             "name": "Office Front Door",
 *             "status": "Online"
 *         },
 *         {
 *             "cloud_id": "FTV0987654321",
 *             "name": "Warehouse",
 *             "status": "Offline"
 *         }
 *     ]
 * }
 */

/**
 * Example Response (Error):
 * {
 *     "status": false,
 *     "message": "Unauthorized"
 * }
 */
?>
