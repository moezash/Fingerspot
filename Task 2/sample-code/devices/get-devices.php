<?php
/**
 * Fingerspot API Sample Code: Get Device List
 *
 * This sample demonstrates how to retrieve the list of registered
 * attendance machines from the Fingerspot Cloud API.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Data
// Even for a simple list, sending a unique trans_id is recommended.
$data = [
    'trans_id' => uniqid('get_device_')
];

// 3. Initialize cURL
$ch = curl_init($apiUrl);

// 4. Set Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

/**
 * SECURITY NOTE:
 * CURLOPT_SSL_VERIFYPEER is set to true for production security.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && (isset($result['status']) && $result['status'])) {
        echo "Successfully retrieved devices:\n";
        if (!empty($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "- Name: " . htmlspecialchars($device['name'] ?? 'N/A') . "\n";
                echo "  Cloud ID: " . ($device['cloud_id'] ?? 'N/A') . "\n";
                echo "  Status: " . ($device['status'] ?? 'Offline') . "\n\n";
            }
        } else {
            echo "No devices found in this account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "get_device_65f1234567890"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV12345678",
            "name": "Main Office Door",
            "status": "Online"
        }
    ]
}

Example Response (Failure):
{
    "status": false,
    "message": "Invalid API Token"
}
*/
?>
