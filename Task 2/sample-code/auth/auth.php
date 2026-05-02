<?php
/**
 * Sample code for Authentication with Fingerspot API
 *
 * This sample demonstrates how to set up the authentication headers
 * required for every request to the Fingerspot API.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from your Fingerspot Developer Dashboard account
$apiToken = 'YOUR_API_TOKEN_HERE';

/**
 * 2. Prepare Headers
 *
 * Every request to Fingerspot API must include:
 * - Authorization: Bearer {token}
 * - Content-Type: application/json
 * - Accept: application/json
 */
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// --- Runnable Demonstration Output ---

echo "--- Fingerspot API Authentication Setup ---\n";
echo "This sample shows the correct headers required for all API calls.\n\n";

echo "Constructed Headers:\n";
foreach ($headers as $header) {
    echo "  [ ] $header\n";
}

echo "\nRecommended cURL implementation snippet:\n";
echo "----------------------------------------\n";
echo "\$ch = curl_init('https://developer.fingerspot.io/api/get_device');\n";
echo "curl_setopt(\$ch, CURLOPT_HTTPHEADER, [\n";
foreach ($headers as $header) {
    echo "    '$header',\n";
}
echo "]);\n";
echo "curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);\n";
echo "curl_setopt(\$ch, CURLOPT_POST, true);\n";
echo "----------------------------------------\n";

echo "\nSUCCESS: Authentication headers prepared correctly.\n";

/**
 * Example Request (Raw HTTP):
 * --------------------------
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 * Accept: application/json
 *
 * {
 *   "trans_id": "1"
 * }
 *
 * Example Response (JSON Output):
 * -------------------------------
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": [...]
 * }
 *
 * If Token is Invalid:
 * -------------------
 * {
 *   "status": false,
 *   "message": "Unauthorized"
 * }
 */
?>
