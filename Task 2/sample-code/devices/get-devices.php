<?php
/**
 * Fingerspot API Sample Code: Get Device List
 *
 * This sample code demonstrates how to retrieve the list of all
 * attendance devices registered in your Fingerspot Cloud account.
 *
 * Requirements:
 * - PHP with cURL extension
 * - Valid API Token
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Request Body
// Every request usually requires a trans_id to track the communication
$data = [
    'trans_id' => (string)time()
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // See auth.php for security note

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Check for cURL errors
if (curl_errno($ch)) {
    echo "--- Get Device List Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Parse and Display Result
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Device List ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['success']) && $result['success']) {
        echo "Devices found in your account:\n";
        echo str_repeat("-", 50) . "\n";
        echo sprintf("%-15s | %-20s | %-10s\n", "Cloud ID", "Device Name", "Status");
        echo str_repeat("-", 50) . "\n";

        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                echo sprintf(
                    "%-15s | %-20s | %-10s\n",
                    $device['cloud_id'],
                    $device['name'],
                    $device['status'] ?? 'Unknown'
                );
            }
        } else {
            echo "No devices found.\n";
        }
        echo str_repeat("-", 50) . "\n";
    } else {
        echo "Failed to retrieve device list.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request Body:
---------------------------------------------------------------------------
{
    "trans_id": "1706692800"
}

---------------------------------------------------------------------------
Example Response (Success):
---------------------------------------------------------------------------
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456789",
            "name": "Main Office",
            "status": "Online"
        },
        {
            "cloud_id": "FTV987654321",
            "name": "Warehouse",
            "status": "Offline"
        }
    ]
}

---------------------------------------------------------------------------
Example Response (Error):
---------------------------------------------------------------------------
{
    "success": false,
    "message": "Unauthorized"
}
---------------------------------------------------------------------------
*/
?>
