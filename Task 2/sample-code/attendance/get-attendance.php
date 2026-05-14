<?php
/**
 * Fingerspot API Sample Code: Get Attendance Logs
 *
 * This sample code demonstrates how to retrieve attendance scan logs
 * from a specific device within a designated date range.
 *
 * Requirements:
 * - PHP with cURL extension
 * - Valid API Token
 * - Device Cloud ID
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. API Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of the attendance device
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Request Body
// start_date and end_date must be in YYYY-MM-DD format
$data = [
    'trans_id'   => (string)time(),
    'cloud_id'   => $cloudId,
    'start_date' => date('Y-m-d'), // Defaults to today
    'end_date'   => date('Y-m-d')  // Defaults to today
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // See auth.php for security note

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Check for cURL errors
if (curl_errno($ch)) {
    echo "--- Get Attendance Logs Error ---\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Parse and Display Result
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Attendance Logs ---\n";
    echo "Device ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && isset($result['success']) && $result['success']) {
        if (isset($result['data']) && is_array($result['data']) && !empty($result['data'])) {
            echo "Scan logs found:\n";
            echo str_repeat("-", 60) . "\n";
            echo sprintf("%-10s | %-20s | %-10s | %-10s\n", "PIN", "Scan Time", "Verify", "Status");
            echo str_repeat("-", 60) . "\n";

            foreach ($result['data'] as $log) {
                /**
                 * status_scan typical values:
                 * 0: Check-In, 1: Check-Out, 2: Break-Out, 3: Break-In, 4: Overtime-In, 5: Overtime-Out
                 */
                echo sprintf(
                    "%-10s | %-20s | %-10s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['verify'],
                    $log['status_scan']
                );
            }
            echo str_repeat("-", 60) . "\n";
        } else {
            echo "No attendance logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request Body:
---------------------------------------------------------------------------
{
    "trans_id": "1706692800",
    "cloud_id": "FTV123456789",
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

---------------------------------------------------------------------------
Example Response (Error):
---------------------------------------------------------------------------
{
    "success": false,
    "message": "Device not found"
}
---------------------------------------------------------------------------
*/
?>
