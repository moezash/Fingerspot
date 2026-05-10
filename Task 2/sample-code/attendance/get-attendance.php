<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range using pure PHP and cURL.
 *
 * Requirements:
 * - Pure PHP + cURL only
 * - Beginner-friendly and professional
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// You must provide trans_id, cloud_id, start_date, and end_date.
$data = [
    'trans_id'   => (string)time(),     // Unique transaction identifier
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
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // Return the response as a string
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        // Set the custom headers
curl_setopt($ch, CURLOPT_POST, true);              // Use POST method
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Attach JSON payload
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // Disable SSL verification for local testing (Enable in production!)

// 6. Execute Request
$response = curl_exec($ch);

// 7. Get HTTP Response Code
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 8. Check for cURL errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    // 9. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['success']) && $result['success']) {
        echo "Logs retrieved successfully:\n";
        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            foreach ($result['data'] as $log) {
                // 'pin' is the employee ID, 'scan' is the timestamp, 'status_scan' is In/Out status
                echo "- PIN: " . $log['pin'] . " | Time: " . $log['scan'] . " | Status: " . ($log['status_scan'] ?? '0') . "\n";
            }
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

// 10. Close cURL session
curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request:
---------------------------------------------------------------------------
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1700000001",
    "cloud_id": "FTV123456",
    "start_date": "2024-01-01",
    "end_date": "2024-01-01"
}

---------------------------------------------------------------------------
Example Response (Success):
---------------------------------------------------------------------------
{
    "success": true,
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

---------------------------------------------------------------------------
Example Response (Error):
---------------------------------------------------------------------------
{
    "success": false,
    "message": "Invalid Cloud ID"
}
*/
?>
