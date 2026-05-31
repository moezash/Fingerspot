<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Note: It is recommended to request a maximum range of 2 days per request
 * for better performance.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID (SN) of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// The Fingerspot Cloud API expects start_date and end_date in 'YYYY-MM-DD' format.
$data = [
    'trans_id'   => (string)time(),     // Unique transaction identifier
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => date('Y-m-d'),      // Start date (e.g., today)
    'end_date'   => date('Y-m-d')       // End date (e.g., today)
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

// Set CURLOPT_SSL_VERIFYPEER to true for production security.
// Setting it to false is strictly for local development troubleshooting only.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// All Fingerspot Cloud API requests use the POST method.
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

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

    // Checking for both 'status' or 'success' keys for robustness.
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Logs retrieved successfully:\n";
        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            foreach ($result['data'] as $log) {
                // PIN: User identifier on the device
                // Scan: DateTime of the scan (YYYY-MM-DD HH:MM:SS)
                // Status_scan: 0 for Check-in, 1 for Check-out (depends on device settings)
                echo "- PIN: " . $log['pin'] . " | Time: " . $log['scan'] . " | Status: " . $log['status_scan'] . "\n";
            }
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/**
 * Example Request Body:
 * {
 *     "trans_id": "1710123456",
 *     "cloud_id": "FTV1234567890",
 *     "start_date": "2024-03-10",
 *     "end_date": "2024-03-11"
 * }
 */

/**
 * Example Response (Success):
 * {
 *     "status": true,
 *     "message": "Success",
 *     "data": [
 *         {
 *             "pin": "1",
 *             "scan": "2024-03-10 08:00:15",
 *             "verify": "1",
 *             "status_scan": "0"
 *         },
 *         {
 *             "pin": "1",
 *             "scan": "2024-03-10 17:05:30",
 *             "verify": "1",
 *             "status_scan": "1"
 *         }
 *     ]
 * }
 */

/**
 * Example Response (Error):
 * {
 *     "status": false,
 *     "message": "Invalid Cloud ID"
 * }
 */
?>
