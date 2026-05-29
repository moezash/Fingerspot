<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Requirements:
 * - Pure PHP + cURL
 * - Valid API Token
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

// 3. Prepare Payload
// Fingerspot API expects a POST request even for listing resources.
// trans_id is a unique transaction identifier, often a timestamp.
$payload = json_encode([
    'trans_id' => (string)time()
]);

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Check for Errors and Display Result
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    // Decode JSON Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "URL: $apiUrl\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Devices found:\n";
        if (!empty($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "- SN: " . $device['cloud_id'] . "\n";
                echo "  Name: " . $device['name'] . "\n";
                echo "  Status: " . ($device['status'] ?? 'Unknown') . "\n";
                echo "--------------------------\n";
            }
        } else {
            echo "No devices registered in this account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/**
Example Request:
----------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1719245600"
}

Example Response (Success):
---------------------------
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

Example Response (Unauthorized):
--------------------------------
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
