<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve scan data (attendance logs)
 * from a specific device within a date range using the /api/get_attlog endpoint.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token and Cloud ID
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of the attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Request Body
/**
 * Parameters:
 * - trans_id: Unique transaction ID
 * - cloud_id: The specific machine to pull logs from
 * - start_date: Format YYYY-MM-DD
 * - end_date: Format YYYY-MM-DD
 *
 * Note: It is recommended to pull data in small date ranges (e.g., max 2 days per request).
 */
$requestBody = [
    'trans_id'   => (string)time(),
    'cloud_id'   => $cloudId,
    'start_date' => date('Y-m-d'), // Pulling logs for today
    'end_date'   => date('Y-m-d')
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));

/**
 * SSL Verification:
 * Set to true for production security.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling and Output
if (curl_errno($ch)) {
    echo "--- Get Attendance Logs Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // Decode JSON response
    $result = json_decode($response, true);

    echo "--- Fingerspot Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Period: " . $requestBody['start_date'] . " to " . $requestBody['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        if (!empty($result['data'])) {
            echo "Successfully retrieved " . count($result['data']) . " log(s):\n";
            echo str_repeat("-", 80) . "\n";
            echo sprintf("%-10s | %-20s | %-10s | %-10s\n", "PIN", "Scan Time", "Status", "Verify");
            echo str_repeat("-", 80) . "\n";

            foreach ($result['data'] as $log) {
                echo sprintf(
                    "%-10s | %-20s | %-10s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['status_scan'],
                    $log['verify']
                );
            }
            echo str_repeat("-", 80) . "\n";
        } else {
            echo "No scan logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve attendance logs.\n";
        echo "API Message: " . ($result['message'] ?? 'No message provided') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/**
 * Example Request:
 * ------------------------------------------------------------
 * POST /api/get_attlog HTTP/1.1
 * Host: developer.fingerspot.io
 * Authorization: Bearer YOUR_API_TOKEN_HERE
 * Content-Type: application/json
 * Accept: application/json
 *
 * {
 *   "trans_id": "1716736000",
 *   "cloud_id": "FTV12345678",
 *   "start_date": "2024-05-26",
 *   "end_date": "2024-05-26"
 * }
 *
 * Example Response (Success):
 * ------------------------------------------------------------
 * {
 *   "status": true,
 *   "message": "Success",
 *   "data": [
 *     {
 *       "pin": "1",
 *       "scan": "2024-05-26 08:00:15",
 *       "verify": "1",
 *       "status_scan": "0"
 *     }
 *   ]
 * }
 *
 * Example Response (Error):
 * ------------------------------------------------------------
 * {
 *   "status": false,
 *   "message": "Invalid Cloud ID"
 * }
 */
?>
