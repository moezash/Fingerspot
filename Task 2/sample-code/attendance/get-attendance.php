<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Requirements:
 * - PHP cURL extension
 * - API Token from developer.fingerspot.io
 * - A valid Cloud ID (Serial Number) of a Fingerspot device
 *
 * Note: Each request can cover a maximum of 2 days (e.g., from 2024-03-01 to 2024-03-02).
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Serial Number of your device
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// Fingerspot documentation recommends a maximum range of 2 days per request.
$startDate = date('Y-m-d', strtotime('-1 day'));
$endDate   = date('Y-m-d');

$data = [
    'trans_id'   => uniqid(),           // Unique ID for this transaction
    'cloud_id'   => $cloudId,           // Device Serial Number
    'start_date' => $startDate,         // Format: YYYY-MM-DD
    'end_date'   => $endDate            // Format: YYYY-MM-DD
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

// Security: Always verify SSL in production.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

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
    echo "Cloud ID  : $cloudId\n";
    echo "Period    : $startDate to $endDate\n";
    echo "HTTP Status: $httpCode\n\n";

    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($isSuccess && isset($result['data'])) {
        if (!empty($result['data'])) {
            echo "Logs retrieved (" . count($result['data']) . " records):\n";
            echo str_repeat("-", 60) . "\n";
            echo sprintf("%-10s | %-20s | %-10s | %-10s\n", "PIN", "Scan Time", "Verify", "Status");
            echo str_repeat("-", 60) . "\n";

            foreach ($result['data'] as $log) {
                // Status 0: Check-in, 1: Check-out, etc. (depends on device settings)
                echo sprintf(
                    "%-10s | %-20s | %-10s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['verify'] ?? '-',
                    $log['status_scan'] ?? '-'
                );
            }
            echo str_repeat("-", 60) . "\n";
        } else {
            echo "No scan logs found for this period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error occurred.') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "65e6d8a7c2e9b",
    "cloud_id": "FTV123456",
    "start_date": "2024-03-01",
    "end_date": "2024-03-02"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "pin": "101",
            "scan": "2024-03-01 08:00:15",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "101",
            "scan": "2024-03-01 17:05:30",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "message": "Invalid Cloud ID or date range exceeded"
}
*/
?>
