<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance scan data
 * from a specific device within a date range.
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

// 2. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Request Body
// start_date and end_date format: YYYY-MM-DD
// Recommended range: maximum 2 days for optimal performance
$data = [
    'trans_id'   => (string)time(),
    'cloud_id'   => $cloudId,
    'start_date' => date('Y-m-d'),
    'end_date'   => date('Y-m-d')
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Security: Enable SSL verification in production
// Set to false only for local development troubleshooting if you face SSL certificate issues
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

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
    echo "Period  : " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && (isset($result['status']) && $result['status'] || isset($result['success']) && $result['success'])) {
        echo "Logs retrieved successfully:\n";
        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            echo "------------------------------------------------------------\n";
            echo sprintf("%-10s | %-20s | %-10s\n", "PIN", "Scan Time", "Status");
            echo "------------------------------------------------------------\n";
            foreach ($result['data'] as $log) {
                $pin    = $log['pin'] ?? 'N/A';
                $scan   = $log['scan'] ?? 'N/A';
                $status = $log['status_scan'] ?? 'N/A';
                echo sprintf("%-10s | %-20s | %-10s\n", $pin, $scan, $status);
            }
            echo "------------------------------------------------------------\n";
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1715846400",
    "cloud_id": "FTV12345678",
    "start_date": "2024-05-15",
    "end_date": "2024-05-16"
}

Example Response (Success):
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "pin": "123",
            "scan": "2024-05-15 08:00:05",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "123",
            "scan": "2024-05-15 17:05:30",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

Example Response (Error):
{
    "success": false,
    "message": "Invalid Cloud ID or no access"
}
*/
?>
