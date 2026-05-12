<?php
/**
 * Sample code for Authentication with Fingerspot Cloud API
 *
 * This sample demonstrates how to set up the Authorization header
 * using a Bearer Token, which is required for all API calls.
 *
 * Requirements:
 * - PHP cURL extension
 * - API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Replace with your actual API Token from Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Fingerspot API requires Bearer Token authentication and JSON content type
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate a simple authenticated request
 */
function checkConnection($headers) {
    // We use /api/get_device as a simple way to test if our token is valid
    $apiUrl = 'https://developer.fingerspot.io/api/get_device';

    $ch = curl_init($apiUrl);

    // Request data
    $postData = json_encode([
        'trans_id' => (string)time()
    ]);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Recommended for local dev only

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        return 'cURL Error: ' . curl_error($ch);
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

// --- Displaying Sample Output ---
echo "=== Fingerspot API Authentication Setup ===\n";
echo "Headers prepared:\n";
foreach ($headers as $header) {
    echo "  [OK] $header\n";
}

echo "\nTo use these headers in your projects, simply pass the \$headers array \nto your curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers) function.\n";

/*
---------------------------------------------------------------------------
Example Request:
---------------------------------------------------------------------------
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1705824000"
}

---------------------------------------------------------------------------
Example Response (Success - 200 OK):
---------------------------------------------------------------------------
{
    "success": true,
    "message": "Success",
    "data": []
}

---------------------------------------------------------------------------
Example Response (Error - 401 Unauthorized):
---------------------------------------------------------------------------
{
    "success": false,
    "message": "Unauthorized"
}
---------------------------------------------------------------------------
*/
?>
