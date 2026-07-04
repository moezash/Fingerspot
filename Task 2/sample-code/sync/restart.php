<?php
/**
 * Fingerspot API Sample Code: Restart Device
 *
 * This sample demonstrates how to remotely reboot the attendance machine.
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
$apiUrl   = 'https://developer.fingerspot.io/api/restart';

// 2. Prepare Request Data
$data = [
    'trans_id' => uniqid('restart_'), // Unique transaction identifier
    'cloud_id' => $cloudId            // Device Identifier
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

    echo "--- Fingerspot API: Restart Device Sample ---\n";
    echo "Sending restart command for Cloud ID: $cloudId\n";
    echo "HTTP Status Code: $httpCode\n\n";

    $success = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($success) {
        echo "Restart command sent successfully. The machine will reboot shortly.\n";
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
    "trans_id": "restart_65f1234567890",
    "cloud_id": "FTV12345678"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
