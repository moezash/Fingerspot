<?php
/**
 * Sample code for Remotely Restarting a Fingerspot Device
 *
 * This sample demonstrates how to trigger a system reboot on a
 * specific attendance machine.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/restart';

// 2. Prepare Payload
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize and Execute cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 5. Error Handling and Output
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Restart Machine ---\n";
    echo "Requesting restart for device: $cloudId\n";
    echo "HTTP Status: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Restart command successfully accepted.\n";
    } else {
        echo "Failed to send restart command.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1715679600",
    "cloud_id": "FTV12345678"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
