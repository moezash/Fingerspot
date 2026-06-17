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
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Body
// Fingerspot API expects a POST request with a JSON body.
// trans_id is a unique identifier for the transaction.
$body = json_encode([
    'trans_id' => uniqid()
]);

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

/**
 * Security Note:
 * CURLOPT_SSL_VERIFYPEER is set to true by default for production security.
 * Setting it to false is strictly for local development troubleshooting ONLY.
 */
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

    // Fingerspot API returns success status in 'status' or 'success' key
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($isSuccess && isset($result['data'])) {
        echo "Devices found (" . count($result['data']) . "):\n";
        echo str_repeat("-", 50) . "\n";
        foreach ($result['data'] as $device) {
            $cloudId = $device['cloud_id'] ?? 'N/A';
            $name    = $device['name'] ?? 'Unnamed Device';
            $status  = $device['status'] ?? 'Unknown';

            echo "Cloud ID : $cloudId\n";
            echo "Name     : $name\n";
            echo "Status   : $status\n";
            echo str_repeat("-", 50) . "\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response : " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
--------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "65a123b456789"
}

Example Response (Success):
--------------------------------------------------
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456789",
            "name": "Main Entrance",
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
--------------------------------------------------
{
    "status": false,
    "message": "Invalid Token"
}
*/
?>
