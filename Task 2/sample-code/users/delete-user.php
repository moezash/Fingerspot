<?php
/**
 * Sample code for Deleting User from Fingerspot Device
 */

$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/delete_userinfo';

$data = [
    'trans_id' => '1',
    'cloud_id' => $cloudId,
    'pin'      => '101' // PIN to delete
];

$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

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
    echo "=== Delete User Information ===\n";
    if ($result && isset($result['status']) && $result['status']) {
        echo "Success: Delete command for PIN " . $data['pin'] . " sent to device.\n";
    } else {
        echo "Failed: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
}
curl_close($ch);
?>
