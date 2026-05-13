<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Requirements:
 * - PHP cURL extension
 * - API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Data
// Even for listing, a trans_id is required to track the request
$data = [
    'trans_id' => (string)time() // Using timestamp as a unique transaction ID
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
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

/**
 * SSL Verification Note:
 * We disable SSL peer verification for local development compatibility.
 * In a production environment, you SHOULD set this to true for security.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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

    // Check if the API returned a success status
    if ($result && isset($result['success']) && $result['success']) {
        echo "Devices found:\n";
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "- Cloud ID: " . $device['cloud_id'] . "\n";
                echo "  Name: " . $device['name'] . "\n";
                echo "  Status: " . ($device['status'] ?? 'Unknown') . "\n";
                echo "---------------------------\n";
            }
        } else {
            echo "No devices registered in this account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request:
---------------------------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1700000000"
}

---------------------------------------------------------------------------
Example Response (Success):
---------------------------------------------------------------------------
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV12345678",
            "name": "Main Entrance",
            "status": "Online"
        },
        {
            "cloud_id": "FTV87654321",
            "name": "Warehouse",
            "status": "Offline"
        }
    ]
}

---------------------------------------------------------------------------
Example Response (Unauthorized):
---------------------------------------------------------------------------
{
    "success": false,
    "message": "Unauthorized"
}
---------------------------------------------------------------------------
*/
?>
