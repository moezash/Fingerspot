<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Requirements:
 * - Pure PHP (no frameworks)
 * - PHP cURL extension
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// start_date and end_date must be in YYYY-MM-DD format.
$data = [
    'trans_id'   => (string)time(),     // Unique transaction ID
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => date('Y-m-d'),      // Start date (Today)
    'end_date'   => date('Y-m-d')       // End date (Today)
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

// SSL Verification:
// Set to false for local development if needed, true for production.
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
    echo "Cloud ID   : $cloudId\n";
    echo "Date Range : " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Logs retrieved successfully:\n";
        if (isset($result['data']) && !empty($result['data'])) {
            echo "------------------------------------------------------------\n";
            echo sprintf("%-10s | %-20s | %-10s\n", "PIN", "Scan Time", "Status");
            echo "------------------------------------------------------------\n";
            foreach ($result['data'] as $log) {
                echo sprintf(
                    "%-10s | %-20s | %-10s\n",
                    $log['pin'] ?? 'N/A',
                    $log['scan'] ?? 'N/A',
                    $log['status_scan'] ?? 'N/A'
                );
            }
            echo "------------------------------------------------------------\n";
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
------------------------------------------------------------
{
    "trans_id": "1710000000",
    "cloud_id": "FTV123456",
    "start_date": "2024-03-01",
    "end_date": "2024-03-01"
}

Example Response (Success):
------------------------------------------------------------
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
------------------------------------------------------------
{
    "status": false,
    "message": "Invalid Cloud ID"
}
*/
?>
