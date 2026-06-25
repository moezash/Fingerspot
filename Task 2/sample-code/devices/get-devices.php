<?php
/**
 * Sample code for Get Device List from Fingerspot Cloud API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

// 2. Prepare Data
// Fingerspot Cloud API expects a unique trans_id for each request
$data = [
    'trans_id' => uniqid()
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
 * SECURITY NOTE:
 * CURLOPT_SSL_VERIFYPEER should be set to true in production for secure communication.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL errors
if (curl_errno($ch)) {
    echo "--- Get Device List Sample ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    /**
     * The API may return success status in 'status' or 'success' key.
     * We check both for robustness.
     */
    $isSuccess = (isset($result['status']) && $result['status']) ||
                 (isset($result['success']) && $result['success']);

    if ($isSuccess && isset($result['data'])) {
        echo "Devices found successfully:\n";
        echo str_repeat("-", 50) . "\n";
        echo sprintf("%-20s | %-15s | %-10s\n", "Device Name", "Cloud ID (SN)", "Status");
        echo str_repeat("-", 50) . "\n";

        foreach ($result['data'] as $device) {
            echo sprintf(
                "%-20s | %-15s | %-10s\n",
                $device['name'],
                $device['cloud_id'],
                $device['status'] ?? 'N/A'
            );
        }
        echo str_repeat("-", 50) . "\n";
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error occurred') . "\n";

        if (isset($result['error_code'])) {
            echo "Error Code: " . $result['error_code'] . "\n";
        }

        // Helpful debugging for beginners
        if ($httpCode === 401) {
            echo "Tip: Check if your API Token is correct and active.\n";
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
    "trans_id": "65a1234567890"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456789",
            "name": "Main Office Entrance",
            "status": "Online"
        },
        {
            "cloud_id": "FTV987654321",
            "name": "Warehouse Exit",
            "status": "Offline"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "error_code": "1001",
    "message": "Invalid Token"
}
*/
?>
