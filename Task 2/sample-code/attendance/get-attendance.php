<?php
/**
 * Fingerspot API Sample Code: Get Attendance Logs
 *
 * This script demonstrates how to retrieve scan logs (attendance data)
 * from a specific device within a designated date range.
 *
 * Documentation: https://developer.fingerspot.io
 * Requirements: Pure PHP, cURL extension
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of the attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Request Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Request Body
// 'start_date' and 'end_date' must be in YYYY-MM-DD format.
$data = [
    'trans_id'   => (string)time(),
    'cloud_id'   => $cloudId,
    'start_date' => date('Y-m-d'), // Default to today
    'end_date'   => date('Y-m-d')  // Default to today
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// IMPORTANT: Set to false for local development if SSL issues occur.
// Enable (true) in production for security.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Error Handling & Response Processing
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // Decode the JSON response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result === null) {
        echo "Error: Invalid JSON response received.\n";
        echo "Raw Response: " . $response . "\n";
    } elseif (isset($result['status']) && $result['status']) {
        echo "Successfully retrieved attendance logs:\n";

        if (!empty($result['data'])) {
            foreach ($result['data'] as $index => $log) {
                echo ($index + 1) . ". Employee PIN: " . ($log['pin'] ?? 'N/A') . "\n";
                echo "   Scan Time   : " . ($log['scan'] ?? 'N/A') . "\n";
                echo "   Verify Mode : " . ($log['verify'] ?? 'N/A') . "\n";
                echo "   Scan Status : " . ($log['status_scan'] ?? 'N/A') . "\n";
                echo "---------------------------\n";
            }
        } else {
            echo "No logs found for the selected period on this device.\n";
        }
    } else {
        echo "API Error: " . ($result['message'] ?? 'Unknown error occurred') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

// 8. Close cURL Session
curl_close($ch);

/*
---------------------------------------------------------
EXAMPLE REQUEST (RAW HTTP)
---------------------------------------------------------
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1700000000",
    "cloud_id": "FTV123456789",
    "start_date": "2024-01-01",
    "end_date": "2024-01-01"
}

---------------------------------------------------------
EXAMPLE RESPONSE (JSON)
---------------------------------------------------------
{
    "status": true,
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
*/
?>
