<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance scan data
 * from a specific device within a defined date range.
 *
 * Requirements:
 * - PHP cURL extension
 * - Valid API Token and Cloud ID (Device SN)
 *
 * Documentation: https://developer.fingerspot.io
 */

// --- 1. Configuration ---
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID (Serial Number) of your device
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// --- 2. Prepare Request Data ---
/**
 * Parameters for /api/get_attlog:
 * - trans_id  : Unique ID for the transaction (string)
 * - cloud_id  : The target device's Cloud ID (string)
 * - start_date: Start date for log retrieval (YYYY-MM-DD)
 * - end_date  : End date for log retrieval (YYYY-MM-DD)
 */
$requestData = [
    'trans_id'   => (string)time(),
    'cloud_id'   => $cloudId,
    'start_date' => date('Y-m-d'), // Defaults to today
    'end_date'   => date('Y-m-d')  // Defaults to today
];

// --- 3. Prepare Headers ---
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// --- 4. Initialize and Configure cURL ---
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

/**
 * Production Security: Keep CURLOPT_SSL_VERIFYPEER as true.
 * Use false only for local troubleshooting of SSL certificate issues.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// --- 5. Execute Request ---
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// --- 6. Check for Errors and Process Response ---
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Cloud ID    : $cloudId\n";
    echo "Date Range  : " . $requestData['start_date'] . " to " . $requestData['end_date'] . "\n";
    echo "HTTP Status : $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        echo "Logs retrieved successfully:\n";

        if (!empty($result['data'])) {
            echo "--------------------------------------------------------\n";
            echo sprintf("%-10s | %-20s | %-10s\n", "PIN", "Scan Time", "Status");
            echo "--------------------------------------------------------\n";

            foreach ($result['data'] as $log) {
                // status_scan: 0 for Check-In, 1 for Check-Out (typical)
                $status = ($log['status_scan'] == '0') ? 'In' : 'Out';
                echo sprintf("%-10s | %-20s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $status
                );
            }
            echo "--------------------------------------------------------\n";
        } else {
            echo "No logs found for the selected date range.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full API Response: " . $response . "\n";
    }
}

// --- 7. Close Connection ---
curl_close($ch);

/**
 * Example Request Body:
 * {
 *    "trans_id": "1700000000",
 *    "cloud_id": "FTV123456789",
 *    "start_date": "2024-01-01",
 *    "end_date": "2024-01-01"
 * }
 *
 * Example Response (Success):
 * {
 *    "status": true,
 *    "message": "Success",
 *    "data": [
 *        {
 *            "pin": "101",
 *            "scan": "2024-01-01 08:00:05",
 *            "verify": "1",
 *            "status_scan": "0"
 *        },
 *        {
 *            "pin": "101",
 *            "scan": "2024-01-01 17:05:30",
 *            "verify": "1",
 *            "status_scan": "1"
 *        }
 *    ]
 * }
 *
 * Example Response (Error - Invalid ID):
 * {
 *    "status": false,
 *    "message": "Invalid Cloud ID"
 * }
 */
?>
