<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot Cloud API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Best Practice:
 * Fingerspot API recommends a maximum range of 2 days per request for stability.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID (SN) of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// Format: YYYY-MM-DD
$startDate = date('Y-m-d', strtotime('-1 day'));
$endDate   = date('Y-m-d');

$data = [
    'trans_id'   => uniqid(),           // Unique ID for this request
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => $startDate,         // Start date
    'end_date'   => $endDate            // End date
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

/**
 * SECURITY NOTE:
 * CURLOPT_SSL_VERIFYPEER should be set to true in production.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo "--- Get Attendance Logs Sample ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: $startDate to $endDate\n";
    echo "HTTP Status Code: $httpCode\n\n";

    $isSuccess = (isset($result['status']) && $result['status']) ||
                 (isset($result['success']) && $result['success']);

    if ($isSuccess) {
        if (isset($result['data']) && !empty($result['data'])) {
            echo "Logs retrieved successfully:\n";
            echo str_repeat("-", 60) . "\n";
            echo sprintf("%-10s | %-20s | %-15s | %-10s\n", "PIN", "Scan Time", "Verify Mode", "Status");
            echo str_repeat("-", 60) . "\n";

            foreach ($result['data'] as $log) {
                // Verify mode: 1 (Finger), 2 (Face), etc.
                // Status scan: 0 (Check-in), 1 (Check-out), etc.
                echo sprintf(
                    "%-10s | %-20s | %-15s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['verify'] ?? 'N/A',
                    $log['status_scan'] ?? 'N/A'
                );
            }
            echo str_repeat("-", 60) . "\n";
        } else {
            echo "No logs found for the selected period ($startDate to $endDate).\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";

        if (isset($result['error_code'])) {
            echo "Error Code: " . $result['error_code'] . "\n";
        }
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
    "trans_id": "65a1234567890",
    "cloud_id": "FTV123456789",
    "start_date": "2024-05-20",
    "end_date": "2024-05-21"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "pin": "101",
            "scan": "2024-05-20 08:00:15",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "101",
            "scan": "2024-05-20 17:05:30",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "message": "Device is offline"
}
*/
?>
