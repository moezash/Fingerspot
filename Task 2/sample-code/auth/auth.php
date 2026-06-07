<?php
/**
 * Fingerspot Cloud API - Authentication Sample
 *
 * This sample code demonstrates how to set up the Authorization header
 * and basic cURL structure required for all Fingerspot API requests.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token from developer.fingerspot.io
 *
 * @author Internship Student
 * @link https://developer.fingerspot.io
 */

// 1. YOUR CONFIGURATION
$apiToken = 'YOUR_API_TOKEN_HERE';
$apiUrl   = 'https://developer.fingerspot.io/api/get_device'; // Testing with Get Device endpoint

// 2. PREPARE HEADERS
/**
 * All requests to Fingerspot Cloud API must include:
 * - Authorization: Bearer [Your_API_Token]
 * - Content-Type: application/json
 * - Accept: application/json
 */
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. INITIALIZE cURL
$ch = curl_init($apiUrl);

// 4. SET cURL OPTIONS
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true); // Fingerspot API uses POST for most endpoints
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['trans_id' => '1'])); // Basic payload
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Set to true for production security

// 5. EXECUTE REQUEST
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. ERROR HANDLING
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // 7. PROCESS RESPONSE
    $result = json_decode($response, true);

    echo "--- Authentication Demo ---\n";
    echo "HTTP Status Code: $httpCode\n";

    if ($httpCode === 200 && isset($result['status']) && $result['status']) {
        echo "Authentication Successful!\n";
        echo "Response Message: " . $result['message'] . "\n";
    } else {
        echo "Authentication Failed or Error occurred.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
}

curl_close($ch);

/**
 * Example Request Headers:
 *
 * POST /api/get_device HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
 * Content-Type: application/json
 * Accept: application/json
 *
 * Example Response (Success):
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": [...]
 * }
 *
 * Example Response (Unauthorized):
 * {
 *   "status": false,
 *   "message": "Unauthorized"
 * }
 */
?>
