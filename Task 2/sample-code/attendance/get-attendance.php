<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot Cloud API
 *
 * This sample demonstrates how to retrieve attendance scan data
 * from a specific device within a date range.
 *
 * Requirements: Pure PHP + cURL
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Serial Number/Cloud ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// Fingerspot API expects a POST request with JSON body
$data = [
    'trans_id'   => '1',                // Unique transaction identifier
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => date('Y-m-d'),      // Start date (YYYY-MM-DD)
    'end_date'   => date('Y-m-d')       // End date (YYYY-MM-DD)
];

// Note: It is recommended to request a maximum of 2 days range
// per request to ensure better performance and stability.

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

// Security: SSL Verification
// In production, CURLOPT_SSL_VERIFYPEER must be set to true.
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
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Code : $httpCode\n\n";

    // Check for success using 'status' or 'success' key
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($isSuccess) {
        if (isset($result['data']) && !empty($result['data'])) {
            echo "Logs retrieved successfully (" . count($result['data']) . " logs):\n";
            echo str_pad("PIN", 10) . " | " . str_pad("Scan Time", 20) . " | Status\n";
            echo str_repeat("-", 45) . "\n";

            foreach ($result['data'] as $log) {
                // status_scan: 0 = Check In, 1 = Check Out, etc. (Machine dependent)
                echo str_pad($log['pin'], 10) . " | " .
                     str_pad($log['scan'], 20) . " | " .
                     $log['status_scan'] . "\n";
            }
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve attendance logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error occurred') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/**
 * Example Request:
 *
 * POST /api/get_attlog HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 *
 * {
 *     "trans_id": "1",
 *     "cloud_id": "FTV123456",
 *     "start_date": "2024-01-01",
 *     "end_date": "2024-01-02"
 * }
 *
 * Example Response (Success):
 * {
 *     "status": true,
 *     "message": "Success",
 *     "data": [
 *         {
 *             "pin": "101",
 *             "scan": "2024-01-01 08:00:15",
 *             "verify": "1",
 *             "status_scan": "0"
 *         },
 *         {
 *             "pin": "101",
 *             "scan": "2024-01-01 17:05:30",
 *             "verify": "1",
 *             "status_scan": "1"
 *         }
 *     ]
 * }
 */
?>
