<?php
/**
 * Sample code for Remote Registration (Register Online)
 *
 * This sample demonstrates how to trigger the machine to start
 * a registration process for a specific user (Finger, Face, etc.)
 * using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Data
$data = [
    'trans_id'     => (string)time(),
    'cloud_id'     => $cloudId,
    'pin'          => '101',
    /**
     * Verification Mode:
     * 0-9: Finger index
     * 12: Face
     * 13: Vein
     * 14: Card (RFID)
     * 15: Password
     */
    'verification' => '0'
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling
if (curl_errno($ch)) {
    echo "--- Register Online Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Process Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Register Online ---\n";
    echo "Triggering registration for PIN " . $data['pin'] . " (Mode: " . $data['verification'] . ")\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command successful. The machine should now be in registration mode.\n";
        echo "Ask the user to perform the scan on the device.\n";
    } else {
        echo "Failed to trigger remote registration.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown API error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

// 8. Close cURL session
curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request Body (JSON):
---------------------------------------------------------------------------
{
    "trans_id": "1705824000",
    "cloud_id": "FTV123456",
    "pin": "101",
    "verification": "12"
}

---------------------------------------------------------------------------
Example Success Response:
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success"
}

---------------------------------------------------------------------------
Example Error Response:
---------------------------------------------------------------------------
{
    "status": false,
    "message": "Machine is Offline"
}
---------------------------------------------------------------------------
*/
?>
