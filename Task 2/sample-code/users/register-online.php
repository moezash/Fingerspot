<?php
/**
 * Sample code for Remote Registration (Register Online)
 *
 * This sample demonstrates how to trigger the machine to start
 * a registration process for a specific user.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token and Cloud ID from Fingerspot
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Data
$data = [
    'trans_id'     => '1',
    'cloud_id'     => $cloudId,
    'pin'          => '101',
    /**
     * Verification Mode:
     * 0-9: Finger 0-9
     * 10: Any Finger
     * 12: Face
     * 13: Vein
     * 14: RFID Card
     * 15: Password
     */
    'verification' => '0'
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

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Register Online Sample ---\n";
    echo "Triggering registration for PIN " . $data['pin'] . " (Mode: " . $data['verification'] . ")\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Command successful. Machine is now in registration mode.\n";
        echo "Note: Once registration is complete, the data will be sent to your Webhook.\n";
    } else {
        echo "Failed to trigger registration.\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
EXAMPLE REQUEST BODY
---------------------------------------------------------------------------
{
    "trans_id": "1",
    "cloud_id": "FTV123456789",
    "pin": "101",
    "verification": "0"
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (SUCCESS)
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success"
}
*/
?>
