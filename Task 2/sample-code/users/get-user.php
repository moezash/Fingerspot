<?php
/**
 * Sample code for Get User Info from Fingerspot Device
 *
 * This sample demonstrates how to request employee information
 * (like PIN, name, and templates) from the device.
 *
 * IMPORTANT: This command is asynchronous. The API will respond with
 * a success status if the command is accepted, but the ACTUAL user data
 * will be sent back later via your configured WEBHOOK.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    'pin'      => '123'            // The PIN of the user to request
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get User Info Sample ---\n";
    echo "Requesting info for PIN: " . $data['pin'] . " from Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Request successful. The device will send the data to your Webhook shortly.\n";
    } else {
        echo "Failed to request user info.\n";
        $errorMsg = $result['message'] ?? 'Unknown API error';
        echo "Error: " . $errorMsg . "\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/get_userinfo HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1700000000",
    "cloud_id": "FTV123456",
    "pin": "123"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}

Note: The actual user data will be pushed to your Webhook endpoint.
*/
?>
