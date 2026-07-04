<?php
/**
 * Fingerspot API Sample Code: Authentication & Headers
 *
 * This file demonstrates how to properly set up authentication headers
 * for communicating with the Fingerspot Cloud API.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Obtain your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * Most Fingerspot API endpoints use the POST method.
 *
 * @param string $url The API endpoint URL
 * @param array $data The payload to be sent as JSON
 * @param string $token The Bearer Token
 * @return array Response code and body
 */
function fingerspot_request($url, $data, $token) {
    // Prepare Headers
    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    /**
     * SECURITY NOTE:
     * CURLOPT_SSL_VERIFYPEER is set to true for production security.
     * If you are testing in a local environment with SSL certificate issues,
     * you may temporarily set this to false, but it is NOT recommended for production.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }

    curl_close($ch);

    if (isset($error_msg)) {
        return ['error' => $error_msg];
    }

    return [
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// --- Usage Demonstration ---
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Required Headers:\n";
echo "- Authorization: Bearer [Your_Token]\n";
echo "- Content-Type: application/json\n";
echo "- Accept: application/json\n\n";

echo "Usage Example:\n";
echo "Headers are passed via curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n";

/*
Example Request Header:
POST /api/get_device HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

Example JSON Response (Unauthorized):
{
    "status": false,
    "message": "Unauthorized"
}

Example JSON Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [...]
}
*/
?>
