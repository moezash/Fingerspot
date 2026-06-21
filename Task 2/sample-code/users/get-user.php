<?php
/**
 * Sample code for Requesting User Information from Fingerspot Device
 *
 * This sample demonstrates how to request user data (names, templates, etc.)
 * from the attendance machine.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Data
$payload = [
    'trans_id' => uniqid('get_usr_'),  // Unique ID for this request
    'cloud_id' => $cloudId,            // Device Cloud ID
    'pin'      => '101'                // Employee PIN to request (Optional, leave empty for all)
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

    echo "--- Get User Information Sample ---\n";
    echo "Requesting info for PIN: " . ($payload['pin'] ?: 'All Users') . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result === null) {
        echo "Error: Received invalid JSON response.\n";
    } else {
        $isSuccess = (isset($result['status']) && $result['status']) ||
                     (isset($result['success']) && $result['success']);

        if ($isSuccess) {
            echo "Request sent successfully to the machine.\n";
            echo "IMPORTANT: This is an asynchronous request. The machine will send the user data\n";
            echo "to your configured Webhook URL once it is processed.\n";
        } else {
            echo "Failed to send request.\n";
            echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        }
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "get_usr_65a1234567890",
    "cloud_id": "FTV123456",
    "pin": "101"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}

Note: The actual user data will arrive at your Webhook like this:
{
    "cloud_id": "FTV123456",
    "trans_id": "get_usr_65a1234567890",
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "template": [...]
}
*/
?>
