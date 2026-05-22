<?php
/**
 * Sample code for Online Registration from Fingerspot API
 *
 * This sample demonstrates how to trigger a device to enter
 * registration mode for a specific user. This allows users to
 * register their face, fingerprint, etc., directly on the device
 * while being controlled remotely.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    'pin'      => '123',           // PIN of the user to register
    'type'     => 'fingerprint'    // Type: 'fingerprint', 'face', 'card', 'palm'
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

    echo "--- Online Registration Sample ---\n";
    echo "Starting " . $data['type'] . " registration for PIN: " . $data['pin'] . " on Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Command sent successfully. The device is now in registration mode.\n";
    } else {
        echo "Failed to start registration.\n";
        $errorMsg = $result['message'] ?? 'Unknown API error';
        echo "Error: " . $errorMsg . "\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/reg_online HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1700000000",
    "cloud_id": "FTV123456",
    "pin": "123",
    "type": "fingerprint"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
