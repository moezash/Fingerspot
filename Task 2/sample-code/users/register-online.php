<?php
/**
 * Sample code for Remote Registration (Register Online)
 *
 * This sample demonstrates how to trigger the machine's registration
 * UI for a specific user and template type.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Data
$data = [
    'trans_id'     => (string)time(),
    'cloud_id'     => $cloudId,
    'pin'          => '101',
    'verification' => '0' // 0-9: Fingers, 12: Face, 13: Vein, 14: Palm
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

if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    $result = json_decode($response, true);
    echo "--- Register Online Sample ---\n";
    echo "Triggering registration for PIN " . $data['pin'] . " (Mode: " . $data['verification'] . ")\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command successful. Machine is now waiting for user input.\n";
    } else {
        echo "Failed to trigger registration.\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);
?>
