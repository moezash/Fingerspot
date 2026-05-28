<?php
/**
 * Sample code for Requesting User Information from Fingerspot Device
 *
 * This sample demonstrates how to request a user's details (name, templates)
 * from the machine. This is an asynchronous operation.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    'pin'      => '101' // Specific PIN to retrieve
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
    echo "--- Get User Information Request ---\n";
    echo "Requesting data for PIN: " . $data['pin'] . "\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command sent. The machine will push user data to your Webhook URL.\n";
    } else {
        echo "Failed to send request.\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Note: The actual user data is NOT returned in this API response.
It will be sent by the machine to your Webhook in a separate POST request.
*/
?>
