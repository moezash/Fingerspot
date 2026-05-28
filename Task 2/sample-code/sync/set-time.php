<?php
/**
 * Sample code for Synchronizing Machine Time
 *
 * This script sends a command to the attendance machine to sync its
 * system clock with the cloud server.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_time';

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
    echo "--- Sync Machine Time Sample ---\n";
    if ($result && isset($result['status']) && $result['status']) {
        echo "Time sync command sent successfully.\n";
    } else {
        echo "Failed to sync time.\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);
?>
