<?php
/**
 * Sample code for Getting User Information from Fingerspot Device
 *
 * This sample demonstrates how to request user data (names, templates, etc.)
 * from the attendance machine using pure PHP and cURL.
 *
 * Note: Fingerspot API often works asynchronously for "Get Userinfo".
 * The API call initiates the request, and the machine pushes the actual
 * user data back to your server via the configured Webhook.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    'pin'      => '101' // PIN of the user to retrieve
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling
if (curl_errno($ch)) {
    echo "--- Get User Information Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Process Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get User Information ---\n";
    echo "Requesting data for PIN: " . $data['pin'] . " on Device: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command successfully sent to the machine.\n";
        echo "The device will process this and send the user details to your Webhook URL.\n";
    } else {
        echo "Failed to initiate user info request.\n";
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
    "trans_id": "1705824000",
    "cloud_id": "FTV123456",
    "pin": "101"
}

---------------------------------------------------------------------------
Example Success Response (API):
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success"
}

---------------------------------------------------------------------------
Example Data Sent to your Webhook (Incoming Data):
---------------------------------------------------------------------------
{
    "type": "get_userinfo",
    "cloud_id": "FTV123456",
    "trans_id": "1705824000",
    "data": {
        "pin": "101",
        "name": "John Doe",
        "privilege": "0",
        "finger": "1",
        "face": "0",
        "password": "123",
        "rfid": "",
        "template": "HEX_DATA_OR_BASE64_HERE"
    }
}
---------------------------------------------------------------------------
*/
?>
