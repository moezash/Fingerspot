<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of all attendance machines
 * registered in your Fingerspot Cloud account.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Data
// Even for listing devices, a trans_id is required to identify the transaction.
$data = [
    'trans_id' => uniqid() // Unique transaction identifier
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Security: always verify SSL in production

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API returns success status in 'status' or 'success' key
    if ($result && (isset($result['status']) && $result['status'] || isset($result['success']) && $result['success'])) {
        echo "Devices retrieved successfully:\n";

        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "- Cloud ID: " . $device['cloud_id'] . "\n";
                echo "  Name    : " . ($device['name'] ?? 'N/A') . "\n";
                echo "  Status  : " . ($device['status'] ?? 'N/A') . "\n";
                echo "---------------------------\n";
            }
        } else {
            echo "No devices found in this account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
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
    "trans_id": "65ba1234abcd"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV12345678",
            "name": "Main Office Gate",
            "status": "Online"
        },
        {
            "cloud_id": "FTV87654321",
            "name": "Factory Entrance",
            "status": "Offline"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "message": "Invalid Token"
}
*/
?>
