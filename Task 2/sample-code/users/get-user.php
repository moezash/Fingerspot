<?php
/**
 * Sample code for Getting User Information from Fingerspot Device
 *
 * This sample demonstrates how to request user data (names, templates, etc.)
 * from the attendance machine remotely.
 *
 * Requirements:
 * - PHP cURL extension
 * - API Token from Fingerspot Developer Dashboard
 * - Cloud ID (Serial Number) of the device
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Data
$data = [
    'trans_id' => '1',            // Unique ID for this request
    'cloud_id' => $cloudId,       // Device Cloud ID
    'pin'      => '101'           // PIN to retrieve
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get User Information Sample ---\n";
    echo "Requesting data for PIN: " . $data['pin'] . " on device " . $cloudId . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Request sent successfully.\n";
        echo "IMPORTANT: Fingerspot API works asynchronously for 'Get Userinfo'.\n";
        echo "The machine will push the actual user data to your configured Webhook URL.\n";
    } else {
        echo "Failed to request data.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request:
---------------------------------------------------------------------------
POST /api/get_userinfo HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1",
    "cloud_id": "FTV123456",
    "pin": "101"
}

---------------------------------------------------------------------------
Example Response (Command Accepted):
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success"
}

---------------------------------------------------------------------------
Example Webhook Payload (Data pushed to your server later):
---------------------------------------------------------------------------
{
    "type": "get_userinfo",
    "cloud_id": "FTV123456",
    "trans_id": "1",
    "data": {
        "pin": "101",
        "name": "John Doe",
        "privilege": "0",
        "password": "",
        "rfid": "",
        "template": [
            {
                "index": "0",
                "type": "finger",
                "data": "HEX_OR_BASE64_DATA..."
            }
        ]
    }
}
*/
?>
