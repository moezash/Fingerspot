<?php
/**
 * Sample code for Get All PIN
 *
 * This sample demonstrates how to retrieve all User IDs / PINs
 * registered on a specific attendance machine.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_allpin';

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

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);
    echo "--- Get All PIN Sample ---\n";
    echo "Requesting all PINs from device: $cloudId\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command sent successfully.\n";
        echo "The machine will send the PIN list back via your configured Webhook.\n";
    } else {
        echo "Failed to request data.\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Note: Get All PIN works asynchronously.
The API call initiates the request, and the machine pushes the actual
PIN list back to your server via the configured Webhook.

Expected Webhook Response:
{
    "type": "get_allpin",
    "cloud_id": "FTVXXXXXX",
    "trans_id": "1",
    "data": [
        {"pin": "1"},
        {"pin": "2"},
        {"pin": "101"}
    ]
}
*/
?>
