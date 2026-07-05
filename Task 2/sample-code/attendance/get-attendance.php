<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Documentation: https://developer.fingerspot.io
 *
 * Example Request:
 * POST /api/get_attlog HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 *
 * {
 *     "trans_id": "65a2345678901",
 *     "cloud_id": "FTV123456",
 *     "start_date": "2024-05-01",
 *     "end_date": "2024-05-02"
 * }
 *
 * Example Response (Success):
 * {
 *     "status": true,
 *     "message": "Success",
 *     "data": [
 *         {
 *             "pin": "1",
 *             "scan": "2024-05-01 08:00:15",
 *             "verify": "1",
 *             "status_scan": "0"
 *         },
 *         {
 *             "pin": "1",
 *             "scan": "2024-05-01 17:05:30",
 *             "verify": "1",
 *             "status_scan": "1"
 *         }
 *     ]
 * }
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Body
// Note: Documentation recommends a max range of 2 days per request for performance.
$body = [
    'trans_id'   => uniqid(),           // Unique ID for this request
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
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

// Set to true for production security.
// Set to false strictly for local development troubleshooting only.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: " . $body['start_date'] . " to " . $body['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Fingerspot API may use 'status' or 'success' key
    $isSuccess = (isset($result['status']) && $result['status']) ||
                 (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Logs retrieved successfully:\n";
        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            echo str_pad("PIN", 10) . " | " . str_pad("Scan Time", 20) . " | Status\n";
            echo str_repeat("-", 45) . "\n";
            foreach ($result['data'] as $log) {
                echo str_pad($log['pin'], 10) . " | ";
                echo str_pad($log['scan'], 20) . " | ";
                echo $log['status_scan'] . "\n";
            }
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Message : " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Response: " . $response . "\n";
    }
}

curl_close($ch);
?>
