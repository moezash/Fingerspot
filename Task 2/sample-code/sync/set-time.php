<?php
/**
 * Sample code for Synchronizing Machine Time
 *
 * This sample demonstrates how to synchronize the attendance machine's
 * date and time with the server or a specific timezone using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_time';

// 2. Prepare Data
$data = [
    'trans_id' => (string)time(),
    'cloud_id' => $cloudId
    // Some devices might accept a 'time' parameter, but usually it syncs with server time
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
    echo "--- Sync Time Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Process Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Synchronize Machine Time ---\n";
    echo "Target Device: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Time synchronization command successfully sent.\n";
    } else {
        echo "Failed to sync time.\n";
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
    "cloud_id": "FTV123456"
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
    "message": "Invalid Cloud ID"
}
---------------------------------------------------------------------------
*/
?>
