<?php
/**
 * Sample code for Getting User Information from Fingerspot Device
 *
 * This sample demonstrates how to request user data (names, templates, etc.)
 * from the attendance machine.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_userinfo';

// 2. Prepare Data
$data = [
    'trans_id' => '1',
    'cloud_id' => $cloudId,
    'pin'      => '101' // PIN to retrieve. Leave empty or omit if supported to get all.
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

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Get User Information Sample ---\n";
    echo "Requesting data for PIN: " . $data['pin'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Request successful. The machine will send the user data to your Webhook URL.\n";
    } else {
        echo "Failed to request data.\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Note: Fingerspot API often works asynchronously for "Get Userinfo".
The API call initiates the request, and the machine pushes the actual
user data back to your server via the configured Webhook.
*/
?>
