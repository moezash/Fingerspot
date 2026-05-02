<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token
 * - Valid Cloud ID (Device SN)
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// Define the date range for logs. Format: YYYY-MM-DD
$startDate = date('Y-m-d');
$endDate   = date('Y-m-d');

$data = [
    'trans_id'   => (string)time(),     // Unique transaction ID
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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
    echo "Target Device: $cloudId\n";
    echo "Period       : $startDate to $endDate\n";
    echo "HTTP Status  : $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status'] === true) {
        $logs = $result['data'] ?? [];
        echo "Found " . count($logs) . " log(s):\n";

        if (!empty($logs)) {
            echo str_repeat("-", 65) . "\n";
            echo sprintf("%-10s | %-20s | %-10s | %-10s\n", "PIN", "Scan Time", "Verify", "Status");
            echo str_repeat("-", 65) . "\n";

            foreach ($logs as $log) {
                echo sprintf(
                    "%-10s | %-20s | %-10s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['verify'],
                    $log['status_scan']
                );
            }
            echo str_repeat("-", 65) . "\n";
        } else {
            echo "No scan data found for this period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unexpected API response') . "\n";
        echo "Raw Response : " . $response . "\n";
    }
}

curl_close($ch);

/**
 * Example Request:
 * ----------------
 * POST /api/get_attlog HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 *
 * {
 *   "trans_id": "1672531200",
 *   "cloud_id": "FTV0001",
 *   "start_date": "2024-01-01",
 *   "end_date": "2024-01-01"
 * }
 *
 * Example Response (Success):
 * ---------------------------
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": [
 *     {
 *       "pin": "123",
 *       "scan": "2024-01-01 08:05:12",
 *       "verify": "1",
 *       "status_scan": "0"
 *     },
 *     {
 *       "pin": "123",
 *       "scan": "2024-01-01 17:10:45",
 *       "verify": "1",
 *       "status_scan": "1"
 *     }
 *   ]
 * }
 *
 * Example Response (Empty):
 * -------------------------
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": []
 * }
 */
?>
