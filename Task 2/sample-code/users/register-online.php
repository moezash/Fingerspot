<?php
/**
 * Sample code for Remote Online Registration on Fingerspot Device
 *
 * This sample demonstrates how to trigger the attendance machine to
 * enter registration mode for a specific employee.
 *
 * IMPORTANT: This is an asynchronous operation. Once the command is sent,
 * the machine will prompt the user to register their biometrics. The
 * result will be sent to your WEBHOOK.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Payload
// type: 0 (Fingerprint), 1 (Face), 2 (Card), 3 (Password)
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    'pin'      => '101',
    'type'     => '0' // Register Fingerprint
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

    echo "--- Fingerspot API: Remote Online Registration ---\n";
    echo "Triggering registration for PIN: " . $data['pin'] . " (Type: " . $data['type'] . ")\n";
    echo "HTTP Status: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Registration command successfully sent.\n";
        echo "The machine should now prompt the user for registration.\n";
    } else {
        echo "Failed to trigger registration.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1715679400",
    "cloud_id": "FTV12345678",
    "pin": "101",
    "type": "0"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
