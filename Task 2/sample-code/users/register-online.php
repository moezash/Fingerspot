<?php
/**
 * Sample code for Remote Registration on Fingerspot Device
 *
 * This sample demonstrates how to trigger the machine's registration
 * mode for fingerprints, face, etc., directly from your application.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Request Data
$data = [
    'trans_id' => uniqid(),        // Unique ID for this request
    'cloud_id' => $cloudId,        // Device Cloud ID
    'pin'      => '101',           // PIN to register
    'type'     => 'finger'         // Type of registration: 'finger', 'face', 'card', etc.
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

// Security: Always verify SSL in production
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo "--- Remote Registration Error ---\n";
    echo 'cURL Error: ' . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Remote Registration Sample ---\n";
    echo "Triggering " . $data['type'] . " registration for PIN: " . $data['pin'] . " on Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && ( (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']) ) ) {
        echo "Registration command sent successfully.\n";
        echo "The machine should now enter registration mode for the specified PIN.\n";
    } else {
        echo "Failed to trigger registration.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/reg_online HTTP/1.1
{
    "trans_id": "65ac87654321",
    "cloud_id": "FTV123456",
    "pin": "101",
    "type": "finger"
}

Example Response:
{
    "success": true,
    "message": "Success"
}
*/
?>
