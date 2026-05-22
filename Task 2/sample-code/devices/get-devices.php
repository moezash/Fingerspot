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
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Body
// For /api/get_device, a 'trans_id' is typically sent.
$body = json_encode([
    'trans_id' => (string)time() // Using timestamp as a unique transaction ID
]);

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

/**
 * SSL Verification
 * In production, always keep CURLOPT_SSL_VERIFYPEER as true (default).
 * If you encounter SSL certificate issues in your local development environment:
 * 1. Preferred: Update your local CA bundle (php.ini: curl.cainfo).
 * 2. Alternative (NOT for production): Set CURLOPT_SSL_VERIFYPEER to false.
 */
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
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot Cloud API might use 'status' or 'success' key
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Devices found:\n";
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                $status = $device['status'] ?? 'Unknown';
                echo "- Cloud ID: " . $device['cloud_id'] . " | Name: " . $device['name'] . " | Status: " . $status . "\n";
            }
        } else {
            echo "No device data found in response.\n";
        }
    } else {
        echo "Failed to retrieve devices.\n";
        $errorMsg = $result['message'] ?? 'Unknown API error';
        echo "Error: " . $errorMsg . "\n";
        echo "Response: " . $response . "\n";
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
    "trans_id": "1700000000"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "FTV123456",
            "name": "Main Office Entrance",
            "status": "Online"
        },
        {
            "cloud_id": "FTV789012",
            "name": "Backdoor",
            "status": "Offline"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
