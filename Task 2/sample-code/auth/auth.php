<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * All Fingerspot Cloud API endpoints require a Bearer Token for authorization.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard (Account Settings)
$apiToken = 'YOUR_API_TOKEN_HERE';

// 2. Prepare Headers
// Every request to Fingerspot API must include the Bearer Token in the Authorization header.
// It also needs Content-Type: application/json for the request body and
// Accept: application/json to handle the response correctly.
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

/**
 * Helper function to demonstrate how headers are used in a cURL request.
 * This is a boilerplate function that can be used for most API requests.
 *
 * @param string $url The API endpoint URL.
 * @param array $headers The headers prepared above.
 * @param array|null $postData Data to be sent as JSON in the POST body.
 * @return array The HTTP response code and response body.
 */
function fingerspot_api_request($url, $headers, $postData = null) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Set CURLOPT_SSL_VERIFYPEER to true for production security.
    // Setting it to false is strictly for local development troubleshooting only.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    if ($postData !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    }

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'code' => $httpCode,
            'response' => json_encode(['status' => false, 'message' => $error])
        ];
    }

    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => $response
    ];
}

// Displaying how headers should look
echo "--- Fingerspot API Authentication Sample ---\n";
echo "Headers to be used in cURL:\n";
foreach ($headers as $header) {
    echo "- $header\n";
}

echo "\nNote: This is a configuration sample. Use these headers in all your API requests.\n";

/**
 * Example Request Headers:
 *
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 * Accept: application/json
 */

/**
 * Example Response (if token is invalid):
 * {
 *     "status": false,
 *     "message": "Unauthorized"
 * }
 */

/**
 * Example Response (if token is valid):
 * {
 *     "status": true,
 *     "message": "Success",
 *     "data": [...]
 * }
 */
?>
