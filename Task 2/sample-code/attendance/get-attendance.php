<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Requirements:
 * - PHP cURL extension
 * - A valid API Token from developer.fingerspot.io
 * - The Cloud ID of your registered device
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// Dates must be in YYYY-MM-DD format.
$data = [
    'trans_id'   => (string)time(),     // Unique transaction ID
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => date('Y-m-d'),      // Start date (YYYY-MM-DD)
    'end_date'   => date('Y-m-d')       // End date (YYYY-MM-DD)
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// SSL Verification: false for local development, true for production
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Note: The Fingerspot API returns 'success' boolean in the response
    if ($result && isset($result['success']) && $result['success']) {
        if (isset($result['data']) && !empty($result['data'])) {
            echo "Successfully retrieved " . count($result['data']) . " logs:\n";
            echo str_pad("PIN", 10) . " | " . str_pad("SCAN TIME", 25) . " | STATUS\n";
            echo str_repeat("-", 50) . "\n";

            foreach ($result['data'] as $log) {
                // status_scan: 0 for Check-in, 1 for Check-out (depends on device config)
                echo str_pad($log['pin'], 10) . " | " .
                     str_pad($log['scan'], 25) . " | " .
                     $log['status_scan'] . "\n";
            }
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error or invalid JSON') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
--------------------------------------------------------------------------------
Example Request:
--------------------------------------------------------------------------------
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1704067200",
    "cloud_id": "FTV123456",
    "start_date": "2024-01-01",
    "end_date": "2024-01-01"
}

--------------------------------------------------------------------------------
Example Response (Success):
--------------------------------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "success": true,
    "message": "Success",
    "data": [
        {
            "pin": "101",
            "scan": "2024-01-01 08:00:15",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "101",
            "scan": "2024-01-01 17:05:30",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

--------------------------------------------------------------------------------
Example Response (Invalid Cloud ID):
--------------------------------------------------------------------------------
HTTP/1.1 400 Bad Request
{
    "success": false,
    "message": "Invalid Cloud ID"
}
--------------------------------------------------------------------------------
*/
?>
