<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid Fingerspot API Token
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
// 'trans_id' is a unique identifier for the request, usually a timestamp
$body = json_encode([
    'trans_id' => (string)time()
]);

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

// SSL Verification: Disable for local testing if needed, enable for production
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling and Response Processing
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Device List ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['success']) && $result['success']) {
        echo "Devices retrieved successfully:\n";
        if (!empty($result['data'])) {
            foreach ($result['data'] as $device) {
                echo " - Name: " . $device['name'] . "\n";
                echo "   Cloud ID: " . $device['cloud_id'] . "\n";
                echo "   Status: " . ($device['status'] ?? 'N/A') . "\n";
                echo "---------------------------\n";
            }
        } else {
            echo "No devices found in your account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error occurred') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1700000000"
}

Example Response (Success):
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456789",
            "name": "Office Main Entrance",
            "status": "Online"
        }
    ]
}

Example Response (Error):
{
    "success": false,
    "message": "Unauthorized"
}
*/
?>
