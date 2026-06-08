<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance logs (scan data)
 * from a specific device within a date range.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
// Get your API Token from the Fingerspot Developer Dashboard
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
/**
 * Fingerspot API recommends a maximum range of 2 days per request.
 * trans_id should be a unique identifier for the request.
 */
$data = [
    'trans_id'   => uniqid('req_', true), // Unique ID for this request
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
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

/**
 * CURLOPT_SSL_VERIFYPEER should be set to true for production to ensure secure connection.
 * Set to false only for local development/troubleshooting if certificates are not properly configured.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

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

    // Fingerspot API may use 'status' or 'success' key to indicate success
    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($result && $isSuccess) {
        echo "Logs retrieved successfully:\n";
        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            foreach ($result['data'] as $log) {
                echo "- PIN: " . ($log['pin'] ?? 'N/A') . " | Time: " . ($log['scan'] ?? 'N/A') . " | Status: " . ($log['status_scan'] ?? 'N/A') . "\n";
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

/*
Example Request:
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json
Accept: application/json

{
    "trans_id": "req_65c1234567890.12345678",
    "cloud_id": "FTV123456",
    "start_date": "2024-01-01",
    "end_date": "2024-01-02"
}

Example Response (Success):
{
    "status": true,
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
    "status": false,
    "message": "Invalid Cloud ID"
}
*/
?>
