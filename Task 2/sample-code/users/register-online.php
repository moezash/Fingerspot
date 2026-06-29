<?php
/**
 * Sample code for Remote Registration on Fingerspot Device
 *
 * This sample demonstrates how to trigger the machine to enter
 * registration mode for a specific user and template type.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Data
$data = [
    'trans_id' => uniqid(),
    'cloud_id' => $cloudId,
    'pin'      => '101',           // PIN of the user to register
    'type'     => 'finger'         // Registration type: finger, face, card, password
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

// Setting CURLOPT_SSL_VERIFYPEER to true for production security.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Remote Registration Sample ---\n";
    echo "Activating registration mode for PIN: " . $data['pin'] . " (Type: " . $data['type'] . ")\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Registration command sent successfully.\n";
        echo "Please proceed to the machine to register the template.\n";
    } else {
        echo "Failed to send registration command.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
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
Accept: application/json

{
    "trans_id": "65ab123456789",
    "cloud_id": "FTV123456",
    "pin": "101",
    "type": "finger"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
