<?php
/**
 * Sample code for Remotely Restarting Fingerspot Device
 *
 * This sample demonstrates how to send a restart command to the
 * attendance machine.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/restart';

// 2. Prepare Data
$data = [
    'trans_id' => uniqid(),       // Unique ID for this request
    'cloud_id' => $cloudId        // Device Cloud ID
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

/**
 * SECURITY NOTE:
 * CURLOPT_SSL_VERIFYPEER is set to true by default for production security.
 * Setting it to false is strictly for local development troubleshooting only.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Restart Device Sample ---\n";
    echo "Sending restart command to Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Robust check for success status (either 'status' or 'success' key)
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Restart command sent successfully to the machine.\n";
    } else {
        echo "Failed to send restart command.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "65b2a1c3e4f60",
    "cloud_id": "FTV123456"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
