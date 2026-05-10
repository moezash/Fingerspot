<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account using pure PHP and cURL.
 *
 * Requirements:
 * - Pure PHP + cURL only
 * - Beginner-friendly and professional
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Data
// Fingerspot API usually requires a trans_id (transaction identifier) for most POST requests.
$data = [
    'trans_id' => (string)time() // Using current timestamp as a unique transaction ID
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
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // Return the response as a string
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        // Set the custom headers
curl_setopt($ch, CURLOPT_POST, true);              // Use POST method
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Attach JSON payload
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // Disable SSL verification for local testing (Enable in production!)

// 6. Execute Request
$response = curl_exec($ch);

// 7. Get HTTP Response Code
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 8. Check for cURL errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    // 9. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API typically returns a 'success' boolean
    if ($result && isset($result['success']) && $result['success']) {
        echo "Devices found:\n";
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "- Cloud ID: " . $device['cloud_id'] . " | Name: " . $device['name'] . " | Status: " . ($device['status'] ?? 'N/A') . "\n";
            }
        } else {
            echo "No device data available.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

// 10. Close cURL session
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
            "cloud_id": "FTV123456",
            "name": "Main Office",
            "status": "Online"
        },
        {
            "cloud_id": "FTV789012",
            "name": "Branch Office",
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
*/
?>
