<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance scan logs (check-in/out)
 * from a specific device within a date range using pure PHP and cURL.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// You must provide cloud_id, start_date, and end_date.
$data = [
    'trans_id'   => (string)time(),     // Unique transaction ID
    'cloud_id'   => $cloudId,           // Target device
    'start_date' => date('Y-m-d'),      // Start date (YYYY-MM-DD)
    'end_date'   => date('Y-m-d')       // End date (YYYY-MM-DD)
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Error Handling
if (curl_errno($ch)) {
    echo "--- Get Attendance Logs Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Process and Display Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Attendance Logs ---\n";
    echo "Device ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        if (!empty($result['data'])) {
            echo "Successfully retrieved " . count($result['data']) . " logs:\n";
            echo str_repeat("-", 60) . "\n";
            echo sprintf("%-10s | %-20s | %-15s | %-10s\n", "PIN", "Scan Time", "Verify Mode", "Status");
            echo str_repeat("-", 60) . "\n";

            foreach ($result['data'] as $log) {
                echo sprintf(
                    "%-10s | %-20s | %-15s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['verify'] ?? 'N/A',
                    $log['status_scan'] ?? 'N/A'
                );
            }
            echo str_repeat("-", 60) . "\n";
        } else {
            echo "No logs found for the specified period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown API error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

// 8. Close cURL session
curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request Body (JSON):
---------------------------------------------------------------------------
{
    "trans_id": "1705824000",
    "cloud_id": "FTV123456",
    "start_date": "2024-01-20",
    "end_date": "2024-01-21"
}

---------------------------------------------------------------------------
Example Success Response:
---------------------------------------------------------------------------
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "pin": "101",
            "scan": "2024-01-21 08:00:05",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "101",
            "scan": "2024-01-21 17:05:30",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

---------------------------------------------------------------------------
Example Error Response (Invalid Device ID):
---------------------------------------------------------------------------
{
    "status": false,
    "message": "Cloud ID not found"
}
---------------------------------------------------------------------------
*/
?>
