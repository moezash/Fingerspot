<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve scan data from a specific
 * device within a defined date range.
 *
 * Requirements:
 * - PHP cURL extension enabled
 * - Valid API Token and Cloud ID
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The ID of your attendance machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Data
// Fingerspot recommends a maximum range of 2 days for stability.
$data = [
    'trans_id'   => uniqid(),           // Unique ID for this transaction
    'cloud_id'   => $cloudId,           // Device Cloud ID
    'start_date' => date('Y-m-d'),      // Format: YYYY-MM-DD
    'end_date'   => date('Y-m-d')       // Format: YYYY-MM-DD
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
 * Security Note:
 * CURLOPT_SSL_VERIFYPEER is set to true by default for production security.
 * Setting it to false is strictly for local development troubleshooting ONLY.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Cloud ID  : $cloudId\n";
    echo "Period    : " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Code : $httpCode\n\n";

    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($isSuccess) {
        if (isset($result['data']) && !empty($result['data'])) {
            echo "Logs found (" . count($result['data']) . "):\n";
            echo str_repeat("-", 60) . "\n";
            echo sprintf("%-10s | %-20s | %-10s\n", "PIN", "Scan Time", "Status");
            echo str_repeat("-", 60) . "\n";

            foreach ($result['data'] as $log) {
                echo sprintf("%-10s | %-20s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['status_scan']
                );
            }
            echo str_repeat("-", 60) . "\n";
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response : " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request:
--------------------------------------------------
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "65a123b456789",
    "cloud_id": "FTV123456789",
    "start_date": "2024-01-01",
    "end_date": "2024-01-01"
}

Example Response (Success):
--------------------------------------------------
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
            "pin": "102",
            "scan": "2024-01-01 08:05:30",
            "verify": "1",
            "status_scan": "0"
        }
    ]
}

Example Response (Error):
--------------------------------------------------
{
    "status": false,
    "message": "Invalid Cloud ID"
}
*/
?>
