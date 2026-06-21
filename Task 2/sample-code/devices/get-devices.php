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

// 2. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Initialize cURL
$ch = curl_init($apiUrl);

// 4. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

/**
 * Security: Always set CURLOPT_SSL_VERIFYPEER to true in production.
 * Setting it to false is strictly for local development troubleshooting only.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// All Fingerspot Cloud API requests use POST
curl_setopt($ch, CURLOPT_POST, true);

// Use uniqid() for a unique transaction ID
$payload = [
    'trans_id' => uniqid('dev_')
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 7. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Handle potential null result from json_decode
    if ($result === null) {
        echo "Error: Received invalid JSON response.\n";
        echo "Raw Response: " . $response . "\n";
    } else {
        // Robust success check: looks for 'status' or 'success' keys
        $isSuccess = (isset($result['status']) && $result['status']) ||
                     (isset($result['success']) && $result['success']);

        if ($isSuccess && isset($result['data'])) {
            echo "Devices found:\n";
            foreach ($result['data'] as $device) {
                echo "- Cloud ID: " . $device['cloud_id'] . " | Name: " . $device['name'] . " | Status: " . ($device['status'] ?? 'N/A') . "\n";
            }
        } else {
            echo "Failed to retrieve devices.\n";
            echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
            if (isset($result['error_code'])) {
                echo "Error Code: " . $result['error_code'] . "\n";
            }
        }
    }
}

curl_close($ch);

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "dev_65a1234567890"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Main Office",
            "status": "Online"
        },
        {
            "cloud_id": "FTV789012",
            "name": "Warehouse",
            "status": "Offline"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "error_code": "401",
    "message": "Unauthorized"
}
*/
?>
