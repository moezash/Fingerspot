<?php
/**
 * Sample code for Remote Registration Mode on Fingerspot Device
 *
 * This sample demonstrates how to trigger the machine's online registration
 * mode so a user can register their fingerprints/face remotely.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Data
$payload = [
    'trans_id'     => uniqid('reg_'),  // Unique ID for this request
    'cloud_id'     => $cloudId,        // Device Cloud ID
    'pin'          => '101',           // Employee PIN to register
    'verification' => '0'              // 0: Fingerprint, 1: Face, 2: Finger vein, etc.
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
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

/**
 * Security: Always set CURLOPT_SSL_VERIFYPEER to true in production.
 * Setting it to false is strictly for local development troubleshooting only.
 */
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

    echo "--- Remote Registration Sample ---\n";
    echo "Triggering registration (type: $payload[verification]) for PIN: $payload[pin]\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result === null) {
        echo "Error: Received invalid JSON response.\n";
    } else {
        $isSuccess = (isset($result['status']) && $result['status']) ||
                     (isset($result['success']) && $result['success']);

        if ($isSuccess) {
            echo "Registration command sent successfully.\n";
            echo "The machine will enter registration mode. Results will be sent via Webhook.\n";
        } else {
            echo "Failed to trigger registration.\n";
            echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        }
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "reg_65a1234567890",
    "cloud_id": "FTV123456",
    "pin": "101",
    "verification": "0"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
