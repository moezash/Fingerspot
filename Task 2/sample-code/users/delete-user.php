<?php
/**
 * Sample code for Deleting User Information from Fingerspot Device
 *
 * This sample demonstrates how to delete an employee from the machine remotely.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token and Cloud ID from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/delete_userinfo';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId,
    'pin'      => '101' // PIN of the employee to delete
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Delete User Sample ---\n";
    echo "Endpoint: $apiUrl\n";
    echo "Deleting PIN: " . $data['pin'] . " from Device: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Delete command sent successfully to the cloud.\n";
        echo "The machine will process this command and report the result via Webhook.\n";
    } else {
        echo "Failed to send delete command.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/delete_userinfo HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1705824000",
    "cloud_id": "FTV123456",
    "pin": "101"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
