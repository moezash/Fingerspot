<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account using pure PHP and cURL.
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

// 3. Prepare Body Data
// Fingerspot API typically expects a POST request even for fetching data.
// A trans_id is recommended for tracking.
$data = [
    'trans_id' => (string)time()
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Disable SSL verification for local development environments.
// WARNING: Enable this in production for secure communication.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL Errors
if (curl_errno($ch)) {
    echo "--- Get Device List Sample ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API returns success status in the JSON body
    // Note: Some versions use 'status', others might use 'success'.
    if ($result && ((isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']))) {
        echo "Devices retrieved successfully:\n";

        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                $cloudId = $device['cloud_id'] ?? 'N/A';
                $name    = $device['name'] ?? 'Unnamed';
                $status  = $device['status'] ?? 'Unknown';

                echo "- Cloud ID: $cloudId | Name: $name | Status: $status\n";
            }
        } else {
            echo "No devices found in your account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        $message = $result['message'] ?? 'Check your API token or request parameters.';
        echo "Error Message: $message\n";
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
Accept: application/json

{
    "trans_id": "1704067200"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Front Office",
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
    "message": "Unauthorized"
}
*/
?>
