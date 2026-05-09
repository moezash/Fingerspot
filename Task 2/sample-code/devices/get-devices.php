<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account.
 *
 * Requirements:
 * - PHP cURL extension
 * - A valid API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Data
// Fingerspot API requires POST method even for data retrieval.
// A 'trans_id' is used to uniquely identify the transaction.
$data = [
    'trans_id' => (string)time()
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

// SSL Verification: false for local development, true for production
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    // Decode the JSON response into an associative array
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Note: The Fingerspot API returns 'success' boolean in the response
    if ($result && isset($result['success']) && $result['success']) {
        echo "Successfully retrieved " . count($result['data']) . " devices:\n";
        echo str_pad("CLOUD ID", 15) . " | " . str_pad("NAME", 20) . " | STATUS\n";
        echo str_repeat("-", 50) . "\n";

        foreach ($result['data'] as $device) {
            echo str_pad($device['cloud_id'], 15) . " | " .
                 str_pad($device['name'], 20) . " | " .
                 ($device['status'] ?? 'N/A') . "\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Invalid JSON response') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
--------------------------------------------------------------------------------
Example Request:
--------------------------------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1704067200"
}

--------------------------------------------------------------------------------
Example Response (Success):
--------------------------------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "success": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Main Office Entrance",
            "status": "Online"
        },
        {
            "cloud_id": "FTV789012",
            "name": "Staff Canteen",
            "status": "Offline"
        }
    ]
}

--------------------------------------------------------------------------------
Example Response (Error):
--------------------------------------------------------------------------------
HTTP/1.1 400 Bad Request
{
    "success": false,
    "message": "Parameter trans_id is required"
}
--------------------------------------------------------------------------------
*/
?>
