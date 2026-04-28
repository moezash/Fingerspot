<?php
/**
 * Sample code for Setting User Information on Fingerspot Device
 *
 * This script sends employee data (Name, PIN, etc.) to the machine.
 *
 * Documentation: https://developer.fingerspot.io
 */

// --- 1. CONFIGURATION ---
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_userinfo';

// --- 2. USER DATA ---
$data = [
    'trans_id'  => '1',
    'cloud_id'  => $cloudId,
    'pin'       => '101',          // Unique Employee ID
    'name'      => 'Jules Agent',  // Employee Name
    'privilege' => '0',            // 0: User, 1: Admin
    'password'  => '',             // Optional device password
    'rfid'      => ''              // Optional Card ID
];

$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// --- 3. EXECUTE ---
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);
    echo "=== Add/Set User Information ===\n";
    if ($result && isset($result['status']) && $result['status']) {
        echo "Success: Command sent to the device for PIN " . $data['pin'] . ".\n";
    } else {
        echo "Failed to send command.\n";
        echo "Response: " . $response . "\n";
    }
}
curl_close($ch);
?>
