<?php
/**
 * Fingerspot Cloud API - Get Attendance Logs Sample
 *
 * This sample code demonstrates how to retrieve attendance scan data
 * from a specific device within a designated date range.
 *
 * Documentation: https://developer.fingerspot.io
 *
 * @author Internship Student
 */

// 1. CONFIGURATION
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of the device
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. PREPARE REQUEST DATA
/**
 * Parameters for get_attlog:
 * - trans_id: Unique transaction ID
 * - cloud_id: The ID of the attendance machine
 * - start_date: Fetch logs from this date (YYYY-MM-DD)
 * - end_date: Fetch logs until this date (YYYY-MM-DD)
 */
$requestData = [
    'trans_id'   => (string)time(),
    'cloud_id'   => $cloudId,
    'start_date' => date('Y-m-d', strtotime('-1 day')), // Yesterday
    'end_date'   => date('Y-m-d')                     // Today
];

// 3. PREPARE HEADERS
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. INITIALIZE cURL
$ch = curl_init($apiUrl);

// 5. SET cURL OPTIONS
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. EXECUTE REQUEST
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. ERROR HANDLING & OUTPUT
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs ---\n";
    echo "Device ID: $cloudId\n";
    echo "Period: " . $requestData['start_date'] . " to " . $requestData['end_date'] . "\n";
    echo "HTTP Status: $httpCode\n\n";

    if (isset($result['status']) && $result['status']) {
        if (!empty($result['data'])) {
            echo "Successfully retrieved " . count($result['data']) . " log(s):\n";
            echo str_repeat("-", 60) . "\n";
            echo sprintf("%-10s | %-20s | %-10s | %-10s\n", "PIN", "Scan Time", "Verify", "Status");
            echo str_repeat("-", 60) . "\n";

            foreach ($result['data'] as $log) {
                echo sprintf(
                    "%-10s | %-20s | %-10s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['verify'],
                    $log['status_scan']
                );
            }
            echo str_repeat("-", 60) . "\n";
        } else {
            echo "No attendance logs found for this period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
}

curl_close($ch);

/**
 * Example Request:
 *
 * POST /api/get_attlog HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_TOKEN
 * Content-Type: application/json
 *
 * {
 *   "trans_id": "1717772405",
 *   "cloud_id": "FTV123456789",
 *   "start_date": "2024-06-06",
 *   "end_date": "2024-06-07"
 * }
 *
 * Example Response (Success):
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": [
 *     {
 *       "pin": "101",
 *       "scan": "2024-06-07 08:00:15",
 *       "verify": "1",
 *       "status_scan": "0"
 *     },
 *     {
 *       "pin": "101",
 *       "scan": "2024-06-07 17:05:30",
 *       "verify": "1",
 *       "status_scan": "1"
 *     }
 *   ]
 * }
 */
?>
