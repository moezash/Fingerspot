<?php
/**
 * Fingerspot API Sample Code: Remote Registration (Online Registration)
 *
 * This sample demonstrates how to trigger the registration mode on the
 * attendance machine for a specific user and template type.
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
$apiUrl   = 'https://developer.fingerspot.io/api/reg_online';

// 2. Prepare Request Data
$data = [
    'trans_id' => uniqid('reg_'),     // Unique transaction identifier
    'cloud_id' => $cloudId,           // Device Identifier
    'pin'      => '101',              // User PIN to register
    /**
     * type_data:
     * 0: Fingerprint
     * 1: Face
     * 2: RFID/Card
     * 3: Password
     */
    'type_data' => '0'
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

    echo "--- Fingerspot API: Remote Registration Sample ---\n";
    echo "Triggering registration for PIN: " . htmlspecialchars($data['pin']) . " (Type: " . $data['type_data'] . ")\n";
    echo "HTTP Status Code: $httpCode\n\n";

    $success = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($success) {
        echo "Command sent successfully. The machine will now enter registration mode.\n";
        echo "The user must perform the scan on the physical device.\n";
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
    "trans_id": "reg_65f1234567890",
    "cloud_id": "FTV12345678",
    "pin": "101",
    "type_data": "0"
}

Example Response (Success):
{
    "status": true,
    "message": "Success"
}
*/
?>
