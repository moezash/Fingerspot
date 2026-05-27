<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token from Fingerspot Developer Dashboard
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

// 3. Prepare Request Body
// trans_id: A unique identifier for the transaction (e.g., current timestamp)
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

// Security: Enable SSL verification in production
// Set to false only for local development troubleshooting if you face SSL certificate issues
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "Endpoint: $apiUrl\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && (isset($result['status']) && $result['status'] || isset($result['success']) && $result['success'])) {
        echo "Devices found:\n";
        // Note: The response structure might vary slightly, common keys are 'data'
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                $cloud_id = $device['cloud_id'] ?? 'N/A';
                $name     = $device['name'] ?? 'Unknown';
                echo "- [ID: $cloud_id] Name: $name\n";
            }
        } else {
            echo "No device data found in response.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1715846400"
}

Example Response (Success):
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
            "name": "Back Door",
            "status": "Offline"
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
