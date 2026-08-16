<?php
/**
 * Sample code for Get Device Info from Fingerspot API
 *
 * get_device membutuhkan cloud_id dan trans_id (bukan list semua mesin).
 * Cloud ID didapat dari dashboard developer.fingerspot.io → Mesin Saya.
 */
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device';

$data = [
    'trans_id' => '1',
    'cloud_id' => $cloudId,
];

$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);
$ok = ($result['success'] ?? $result['status'] ?? false);

echo "--- Get Device Info Sample ---\n";
echo "HTTP Status Code: $httpCode\n\n";

if ($ok) {
    echo "Device info:\n";
    print_r($result['data'] ?? $result);
} else {
    echo "Failed.\n";
    echo "Response: $response\n";
}
