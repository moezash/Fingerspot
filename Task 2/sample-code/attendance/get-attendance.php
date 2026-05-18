<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// Parameters for retrieving attendance logs
$data = [
    'trans_id'   => (string)time(),     // Unique identifier for this transaction
    'cloud_id'   => $cloudId,           // Device identifier
    'start_date' => date('Y-m-d'),      // Start date (YYYY-MM-DD format)
    'end_date'   => date('Y-m-d')       // End date (YYYY-MM-DD format)
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

// Disable SSL verification for local testing.
// WARNING: Always enable SSL verification (true) in production.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL Errors
if (curl_errno($ch)) {
    echo "--- Get Attendance Logs Sample ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API returns success status in the JSON body
    // Note: Some versions use 'status', others might use 'success'.
    if ($result && ((isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']))) {
        echo "Logs retrieved successfully:\n";

        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            echo str_pad("PIN", 10) . " | " . str_pad("Scan Time", 20) . " | Status\n";
            echo str_repeat("-", 45) . "\n";

            foreach ($result['data'] as $log) {
                $pin  = $log['pin'] ?? 'N/A';
                $time = $log['scan'] ?? 'N/A';
                $type = $log['status_scan'] ?? 'N/A'; // 0: Check-in, 1: Check-out, etc.

                echo str_pad($pin, 10) . " | " . str_pad($time, 20) . " | $type\n";
            }
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        $message = $result['message'] ?? 'Unknown error. Check your credentials and Cloud ID.';
        echo "Error Message: $message\n";
        echo "Raw Response: " . $response . "\n";
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
    "trans_id": "1704067200",
    "cloud_id": "FTV123456",
    "start_date": "2024-01-01",
    "end_date": "2024-01-01"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "pin": "1",
            "scan": "2024-01-01 08:00:15",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "1",
            "scan": "2024-01-01 17:05:30",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "message": "Invalid Cloud ID"
}
*/
?>
