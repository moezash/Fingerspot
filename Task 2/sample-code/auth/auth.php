<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP cURL extension
 * - API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

/**
 * Helper function to demonstrate how headers are used in a cURL request
 *
 * @param string $url The endpoint URL
 * @param string $apiToken Your API Bearer Token
 * @param array $payload Optional data to send
 * @return array Response code and body
 */
function testAuthentication($url, $apiToken, $payload = []) {
    // 2. Prepare Headers
    // Every request to Fingerspot API must include the Bearer Token in the Authorization header
    $headers = [
        'Authorization: Bearer ' . $apiToken,
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    $ch = curl_init($url);
    $error_msg = null;

    // 3. Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Security: Always verify SSL in production.
    // Set to false only for local development troubleshooting if needed.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Fingerspot Cloud API typically uses POST for all requests
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    // 4. Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }

    curl_close($ch);

    return [
        'code'     => $httpCode,
        'response' => $response,
        'error'    => $error_msg ?? null
    ];
}

// Displaying how headers should look
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Required Headers:\n";
echo "- Authorization: Bearer $apiToken\n";
echo "- Content-Type: application/json\n";
echo "- Accept: application/json\n\n";

echo "Note: Use these headers in all your API requests to https://developer.fingerspot.io\n";

/*
Example Request:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "65e6d8a7c2e9b"
}

Example Response (Unauthorized - 401):
{
    "success": false,
    "message": "Unauthorized"
}

Example Response (Success - 200):
{
    "success": true,
    "data": [...]
}
*/
?>
