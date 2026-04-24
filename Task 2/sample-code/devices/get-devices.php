<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices registered
 * in your Fingerspot account using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Data
// Although it's a GET-style list, Fingerspot API often expects a POST with trans_id
$data = [
    'trans_id' => (string)time() // A unique identifier for this transaction
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

// Set options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);                 // Fingerspot API typically uses POST
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);      // Disable for local testing environment
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling
if (curl_errno($ch)) {
    echo "--- Get Device List Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Process and Display Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Device List ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Successfully retrieved " . count($result['data']) . " devices:\n";
        echo str_repeat("-", 50) . "\n";
        echo sprintf("%-15s | %-20s | %-10s\n", "Cloud ID", "Device Name", "Status");
        echo str_repeat("-", 50) . "\n";

        foreach ($result['data'] as $device) {
            echo sprintf(
                "%-15s | %-20s | %-10s\n",
                $device['cloud_id'],
                $device['name'],
                $device['status'] ?? 'Unknown'
            );
        }
        echo str_repeat("-", 50) . "\n";
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown API error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

// 8. Close cURL session
curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request Body (JSON):
---------------------------------------------------------------------------
{
    "trans_id": "1705824000"
}

---------------------------------------------------------------------------
Example Success Response:
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Main Office Entry",
            "status": "Online"
        },
        {
            "cloud_id": "FTV789012",
            "name": "Warehouse Gate",
            "status": "Offline"
        }
    ]
}

---------------------------------------------------------------------------
Example Error Response (Invalid Token):
---------------------------------------------------------------------------
{
    "status": false,
    "message": "Unauthorized"
}
---------------------------------------------------------------------------
*/
?>
