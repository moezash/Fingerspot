<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range using the /api/get_attlog endpoint.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// Documentation recommends a maximum range of 2 days per request for performance.
$data = [
    'trans_id'   => (string)time(),     // Unique transaction ID
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

/**
 * CURLOPT_SSL_VERIFYPEER should be set to true for production environments
 * to ensure a secure connection. If you encounter SSL certificate issues
 * in a local development environment, you might temporarily set it to false
 * for troubleshooting, but this is NOT recommended for production.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// Fingerspot API expects POST method
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Check if the request was successful
    if ($result && isset($result['success']) && $result['success']) {
        echo "Logs retrieved successfully:\n";
        if (isset($result['data']) && !empty($result['data'])) {
            foreach ($result['data'] as $log) {
                echo "- PIN: " . $log['pin'] . " | Time: " . $log['scan'] . " | Status: " . ($log['status_scan'] ?? 'N/A') . "\n";
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
Example Request:
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "1700000000",
    "cloud_id": "FTV123456",
    "start_date": "2024-01-01",
    "end_date": "2024-01-02"
}

Example Response (Success):
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

Example Response (Error):
{
    "success": false,
    "error_code": "1003",
    "message": "Invalid Cloud ID"
}
*/
?>
