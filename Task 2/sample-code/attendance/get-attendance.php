<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance scan data
 * from a specific device within a date range.
 *
 * Requirements:
 * - API Token
 * - Device Cloud ID
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // Example: FTV12345678
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// Fingerspot API expects dates in YYYY-MM-DD format.
// It is recommended to request a maximum of 2 days range for better performance.
$data = [
    'trans_id'   => uniqid(),           // Unique transaction ID
    'cloud_id'   => $cloudId,           // Target Device Cloud ID
    'start_date' => date('Y-m-d'),      // Start date (Today)
    'end_date'   => date('Y-m-d')       // End date (Today)
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && (isset($result['status']) && $result['status'] || isset($result['success']) && $result['success'])) {
        echo "Logs retrieved successfully:\n";

        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            // Display table-like output for CLI
            printf("%-10s | %-20s | %-10s\n", "PIN", "Scan Time", "Status");
            echo str_repeat("-", 45) . "\n";

            foreach ($result['data'] as $log) {
                printf("%-10s | %-20s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['status_scan']
                );
            }
        } else {
            echo "No scan logs found for this period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "65ba5678efgh",
    "cloud_id": "FTV12345678",
    "start_date": "2024-02-01",
    "end_date": "2024-02-02"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "pin": "101",
            "scan": "2024-02-01 08:00:05",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "101",
            "scan": "2024-02-01 17:05:12",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "message": "Cloud ID not found"
}
*/
?>
