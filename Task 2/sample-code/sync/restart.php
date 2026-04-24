<?php
/**
 * Sample code for Remotely Restarting Fingerspot Device
 *
 * This sample demonstrates how to send a restart command to the
 * attendance machine.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token and Cloud ID from Fingerspot
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/restart';

// 2. Prepare Data
$data = [
    'trans_id' => '1',
    'cloud_id' => $cloudId
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
    echo 'Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Restart Device Sample ---\n";
    echo "Sending restart command to Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Restart command sent successfully. The machine will reboot shortly.\n";
    } else {
        echo "Failed to send restart command.\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
EXAMPLE REQUEST BODY
---------------------------------------------------------------------------
{
    "trans_id": "1",
    "cloud_id": "FTV123456789"
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (SUCCESS)
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success"
}
*/
?>
