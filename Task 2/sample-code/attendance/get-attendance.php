<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Requirements:
 * - PHP cURL extension
 * - API Token and Cloud ID from developer.fingerspot.io
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
$data = [
    'trans_id'   => (string)time(),     // Unique ID for this request
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => date('Y-m-d'),      // Start date (YYYY-MM-DD), default to today
    'end_date'   => date('Y-m-d')       // End date (YYYY-MM-DD), default to today
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
 * SSL Verification Note:
 * We disable SSL peer verification for local development compatibility.
 * In a production environment, you SHOULD set this to true for security.
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
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Check if the API returned a success status
    if ($result && isset($result['success']) && $result['success']) {
        echo "Logs retrieved successfully:\n";
        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            foreach ($result['data'] as $log) {
                echo "- PIN: " . $log['pin'] . "\n";
                echo "  Scan Time: " . $log['scan'] . "\n";
                echo "  Verify Mode: " . ($log['verify'] ?? 'N/A') . "\n";
                echo "  Status Scan: " . ($log['status_scan'] ?? 'N/A') . "\n";
                echo "---------------------------\n";
            }
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

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
    "trans_id": "1700000000",
    "cloud_id": "FTV12345678",
    "start_date": "2024-01-01",
    "end_date": "2024-01-07"
}

---------------------------------------------------------------------------
Example Response (Success):
---------------------------------------------------------------------------
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "pin": "123",
            "scan": "2024-01-01 08:00:15",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "123",
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
---------------------------------------------------------------------------
*/
?>
