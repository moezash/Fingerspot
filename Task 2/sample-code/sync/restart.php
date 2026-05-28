<?php
/**
 * Sample code for Restarting Machine Remotely
 *
 * This script sends a remote system reboot command to the
 * attendance machine.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/restart';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId
];

// 3. Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    $result = json_decode($response, true);
    echo "--- Restart Machine Sample ---\n";
    if ($result && isset($result['status']) && $result['status']) {
        echo "Restart command sent successfully.\n";
    } else {
        echo "Failed to send restart command.\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);
?>
