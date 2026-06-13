<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP with cURL extension enabled
 * - API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header
// and use application/json for content types.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * This is a boilerplate for making authenticated POST requests to Fingerspot API.
 *
 * @param string $url The API endpoint URL
 * @param array $headers The authentication and content-type headers
 * @param array $payload The data to be sent in the request body
 * @return array The response details
 */
function fingerspot_api_request($url, $headers, $payload = []) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    /**
     * SECURITY NOTE:
     * CURLOPT_SSL_VERIFYPEER should be set to true in production for security.
     * Setting it to false is strictly for local development troubleshooting only.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response,
        'error' => $error_msg ?? null
    ];
}

// Displaying how headers and request should look
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: This is a configuration and boilerplate sample. Use these headers and logic in all your API requests.\n";

/*
Example Request (via cURL command line):
curl -X POST "https://developer.fingerspot.io/api/get_device" \
     -H "Authorization: Bearer YOUR_API_TOKEN_HERE" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"trans_id": "1"}'

Example Response (if token is invalid):
{
    "status": false,
    "message": "Unauthorized"
}

Example Response (if token is valid):
{
    "status": true,
    "message": "Success",
    "data": []
}
*/
?>
