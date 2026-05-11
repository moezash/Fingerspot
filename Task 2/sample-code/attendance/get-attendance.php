<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance scan data
 * from a specific device within a date range.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid Fingerspot API Token
 * - Device Cloud ID
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // Replace with your actual device Cloud ID
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Request Data
$data = [
    'trans_id'   => (string)time(),     // Unique identifier for this request
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => date('Y-m-d'),      // Start date (YYYY-MM-DD), e.g., today
    'end_date'   => date('Y-m-d')       // End date (YYYY-MM-DD), e.g., today
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
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// SSL Verification: Disable for local testing if needed, enable for production
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling and Response Processing
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Attendance Logs ---\n";
    echo "Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['success']) && $result['success']) {
        echo "Logs retrieved successfully:\n";
        if (!empty($result['data'])) {
            echo str_pad("PIN", 10) . " | " . str_pad("Scan Time", 20) . " | Status\n";
            echo str_repeat("-", 45) . "\n";
            foreach ($result['data'] as $log) {
                echo str_pad($log['pin'], 10) . " | " .
                     str_pad($log['scan'], 20) . " | " .
                     $log['status_scan'] . "\n";
            }
        } else {
            echo "No logs found for the specified period.\n";
        }
    } else {
        echo "Failed to retrieve attendance logs.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error occurred') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1700000000",
    "cloud_id": "FTV123456789",
    "start_date": "2024-05-20",
    "end_date": "2024-05-20"
}

Example Response (Success):
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "pin": "101",
            "scan": "2024-05-20 08:00:15",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "101",
            "scan": "2024-05-20 17:05:30",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

Example Response (Error):
{
    "success": false,
    "message": "Invalid Cloud ID"
}
*/
?>
