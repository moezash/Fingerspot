<?php
/**
 * Sample code for Get Device List from Fingerspot API
 *
 * This sample demonstrates how to retrieve the list of devices
 * registered in your Fingerspot account using pure PHP and cURL.
 *
 * Requirements:
 * - PHP cURL extension
 * - Valid API Token
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Endpoint to get device list

// 2. Prepare Headers
// Every request requires the Authorization Bearer Token and JSON Content-Type
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Body Data
// Most Fingerspot API endpoints expect a trans_id to track the transaction.
$data = [
    'trans_id' => (string)time() // Using current timestamp as a unique transaction ID
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

/**
 * Fingerspot API uses POST for most actions, including retrieval.
 */
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

/**
 * SSL Verification:
 * Set to false for local development if CA certificates are not up to date.
 * Set to true in production for security.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Device List Sample ---\n";
    echo "Endpoint: $apiUrl\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Validate JSON response
    if ($result === null) {
        echo "Error: Failed to decode JSON response.\n";
        echo "Raw Response: " . $response . "\n";
    } elseif (isset($result['status']) && $result['status']) {
        // Success
        echo "Message: " . ($result['message'] ?? 'Success') . "\n";
        echo "Devices found:\n";

        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $device) {
                echo "---------------------------------\n";
                echo "Cloud ID : " . ($device['cloud_id'] ?? 'N/A') . "\n";
                echo "Name     : " . ($device['name'] ?? 'N/A') . "\n";
                echo "Status   : " . ($device['status'] ?? 'N/A') . "\n";
            }
            echo "---------------------------------\n";
        } else {
            echo "No devices registered in this account.\n";
        }
    } else {
        // API Error
        echo "Failed to retrieve devices.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

// 9. Close cURL
curl_close($ch);

/*
Example Request Body:
------------------------------------------------------------
{
    "trans_id": "1704067200"
}

Example Response (Success):
------------------------------------------------------------
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "cloud_id": "ABC123456789",
            "name": "Main Office Entry",
            "status": "Online"
        },
        {
            "cloud_id": "XYZ987654321",
            "name": "Warehouse Exit",
            "status": "Offline"
        }
    ]
}

Example Response (Error):
------------------------------------------------------------
{
    "status": false,
    "message": "Unauthorized"
}
*/
?>
