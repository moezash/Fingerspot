<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token and Cloud ID from Fingerspot
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// start_date and end_date should be in YYYY-MM-DD format.
$data = [
    'trans_id'   => '1',                // Unique ID for this request
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => date('Y-m-d'),      // Start date (Today)
    'end_date'   => date('Y-m-d')       // End date (Today)
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Logs retrieved successfully:\n";

        if (isset($result['data']) && !empty($result['data'])) {
            foreach ($result['data'] as $log) {
                echo "  [+] PIN: " . $log['pin'] . " | Time: " . $log['scan'] . " | Status: " . $log['status_scan'] . "\n";
            }
        } else {
            echo "  [-] No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
EXAMPLE REQUEST
---------------------------------------------------------------------------
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1",
    "cloud_id": "FTV123456789",
    "start_date": "2024-01-01",
    "end_date": "2024-01-07"
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (SUCCESS)
---------------------------------------------------------------------------
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

---------------------------------------------------------------------------
EXAMPLE RESPONSE (ERROR)
---------------------------------------------------------------------------
{
    "status": false,
    "message": "Invalid Cloud ID"
}
*/
?>
