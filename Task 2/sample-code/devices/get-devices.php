<?php
/**
 * Sample code for Get Device List from Fingerspot Cloud API
 *
 * This sample demonstrates how to retrieve the list of all devices
 * registered under your Fingerspot account.
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

// 3. Prepare Body Data
$data = [
    'trans_id' => (string)time() // Unique transaction ID
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local development convenience

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "=== Get Device List Result ===\n";
    echo "HTTP Status: $httpCode\n\n";

    if (isset($result['success']) && $result['success']) {
        echo "Successfully retrieved " . count($result['data']) . " device(s):\n";

        foreach ($result['data'] as $index => $device) {
            $num = $index + 1;
            echo "{$num}. Name: {$device['name']}\n";
            echo "   Cloud ID: {$device['cloud_id']}\n";
            echo "   Status: " . ($device['status'] ? 'Online' : 'Offline') . "\n";
            echo "-----------------------------------\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request Body:
---------------------------------------------------------------------------
{
    "trans_id": "1705824000"
}

---------------------------------------------------------------------------
Example Response (Success):
---------------------------------------------------------------------------
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FIO123456789",
            "name": "Main Office Entrance",
            "status": 1
        },
        {
            "cloud_id": "FIO987654321",
            "name": "Warehouse Exit",
            "status": 0
        }
    ]
}

---------------------------------------------------------------------------
Example Response (Error):
---------------------------------------------------------------------------
{
    "success": false,
    "message": "Token expired"
}
---------------------------------------------------------------------------
*/
?>
