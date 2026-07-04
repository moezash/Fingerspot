<?php
/**
 * Fingerspot API Sample Code: Set Device Time
 *
 * This sample demonstrates how to synchronize the date, time, and
 * timezone of the attendance machine.
 *
 * Requirements:
 * - api_token: Obtain from Fingerspot Developer Dashboard
 * - cloud_id: The unique ID of your registered device
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/set_time';

// 2. Prepare Request Data
$data = [
    'trans_id' => uniqid('settime_'), // Unique transaction identifier
    'cloud_id' => $cloudId,           // Device Identifier
    /**
     * timezone: Difference from UTC in minutes.
     * E.g., WIB (UTC+7) is 420 minutes.
     */
    'timezone' => '420',
    /**
     * set_time: Date and time in YYYY-MM-DD HH:MM:SS format.
     */
    'set_time' => date('Y-m-d H:i:s')
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

/**
 * SECURITY NOTE:
 * CURLOPT_SSL_VERIFYPEER is set to true by default for production security.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Check for cURL Errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Parse and Process Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Set Device Time Sample ---\n";
    echo "Syncing time to: " . $data['set_time'] . " (TZ: " . $data['timezone'] . " min)\n";
    echo "HTTP Status Code: $httpCode\n\n";

    $success = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($success) {
        echo "Time sync command sent successfully.\n";
    } else {
        echo "Failed to send command.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "settime_65f1234567890",
    "cloud_id": "FTV12345678",
    "timezone": "420",
    "set_time": "2024-05-20 10:30:00"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
