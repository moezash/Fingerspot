<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Data
// Even for listing, a unique trans_id is recommended for every request
$data = [
    'trans_id' => uniqid('dev_')
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

/**
 * SECURITY NOTE:
 * CURLOPT_SSL_VERIFYPEER is set to true for production security.
 * Ensure your server has up-to-date CA certificates.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// Fingerspot API uses POST for most operations, including retrieval
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

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

    // Standard Fingerspot API response uses 'status' or 'success' key
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Devices found:\n";
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                $status = $device['status'] ?? 'Unknown';
                echo "- SN: " . $device['cloud_id'] . " | Name: " . $device['name'] . " | Status: " . $status . "\n";
            }
        } else {
            echo "No devices listed in the account.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
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
    "trans_id": "dev_65a123456789b"
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

Example Response (Unauthorized):
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
