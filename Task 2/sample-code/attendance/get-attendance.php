<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range using pure PHP and cURL.
 *
 * Requirements:
 * - PHP cURL extension
 * - Valid API Token
 * - Device Cloud ID
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The unique Cloud ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// The API expects start_date and end_date in YYYY-MM-DD format.
$data = [
    'trans_id'   => (string)time(),     // Unique transaction ID
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => date('Y-m-d'),      // Start date (defaults to today)
    'end_date'   => date('Y-m-d')       // End date (defaults to today)
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

/**
 * SSL Verification:
 * Set to false for local development.
 * Set to true in production.
 */
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
    echo "Endpoint   : $apiUrl\n";
    echo "Cloud ID   : $cloudId\n";
    echo "Date Range : " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Code  : $httpCode\n\n";

    // Validate JSON response
    if ($result === null) {
        echo "Error: Failed to decode JSON response.\n";
        echo "Raw Response: " . $response . "\n";
    } elseif (isset($result['status']) && $result['status']) {
        // Success
        echo "Message: " . ($result['message'] ?? 'Success') . "\n";
        echo "Logs retrieved successfully:\n";

        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            echo "PIN\t| Scan Time\t\t| Status\n";
            echo "--------------------------------------------------\n";
            foreach ($result['data'] as $log) {
                // PIN: Employee identifier
                // Scan: DateTime of the scan
                // Status_scan: Attendance status (0: In, 1: Out, etc. depends on machine config)
                $pin  = $log['pin'] ?? 'N/A';
                $scan = $log['scan'] ?? 'N/A';
                $stat = $log['status_scan'] ?? 'N/A';
                echo "$pin\t| $scan\t| $stat\n";
            }
            echo "--------------------------------------------------\n";
        } else {
            echo "No logs found for the selected period on this device.\n";
        }
    } else {
        // API Error
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

// 9. Close cURL
curl_close($ch);

/*
Example Request Body:
------------------------------------------------------------
{
    "trans_id": "1704067200",
    "cloud_id": "ABC123456789",
    "start_date": "2024-01-01",
    "end_date": "2024-01-07"
}

Example Response (Success):
------------------------------------------------------------
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

Example Response (Error):
------------------------------------------------------------
{
    "status": false,
    "message": "Invalid Cloud ID"
}
*/
?>
