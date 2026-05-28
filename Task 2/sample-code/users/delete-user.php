<?php
/**
 * Sample code for Deleting User Information from Fingerspot Device
 *
 * This sample demonstrates how to remotely delete an employee record
 * from the attendance machine.
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    $result = json_decode($response, true);
    echo "--- Delete User Sample ---\n";
    echo "Request to delete PIN: " . $data['pin'] . "\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Delete command sent successfully to the machine.\n";
    } else {
        echo "Failed to send delete command.\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
--------------------
{
    "trans_id": "1705824000",
    "cloud_id": "FTV123456",
    "pin": "101"
}

Example Response:
--------------------
{
    "status": true,
    "message": "Success"
}
*/
?>
