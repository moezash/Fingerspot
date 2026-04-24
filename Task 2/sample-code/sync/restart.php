<?php
/**
 * Sample code for Restarting Machine Remotely
 *
 * This sample demonstrates how to remotely reboot the attendance machine
 * using pure PHP and cURL. Use this feature with caution.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/restart';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId
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
    echo "--- Restart Machine Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Process Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Restart Machine ---\n";
    echo "Target Device: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Restart command successfully sent to the machine.\n";
    } else {
        echo "Failed to send restart command.\n";
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
    "cloud_id": "FTV123456"
}

---------------------------------------------------------------------------
Example Success Response:
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success"
}

---------------------------------------------------------------------------
Example Error Response (Device Offline):
---------------------------------------------------------------------------
{
    "status": false,
    "message": "Device is currently offline"
}
---------------------------------------------------------------------------
*/
?>
