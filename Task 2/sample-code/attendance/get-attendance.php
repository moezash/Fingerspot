<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot Cloud API
 *
 * This sample demonstrates how to retrieve scan logs from a specific
 * attendance device within a selected date range.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of your device
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 3. Prepare Body Data
$data = [
    'trans_id'   => (string)time(),
    'cloud_id'   => $cloudId,
    'start_date' => date('Y-m-d'), // Defaults to today
    'end_date'   => date('Y-m-d')  // Defaults to today
];

// 4. Initialize cURL
$ch = curl_init($apiUrl);

// 5. Set cURL Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 6. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 7. Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "=== Get Attendance Logs Result ===\n";
    echo "Device ID: $cloudId\n";
    echo "Period: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status: $httpCode\n\n";

    if (isset($result['success']) && $result['success']) {
        if (!empty($result['data'])) {
            echo "Successfully retrieved " . count($result['data']) . " log(s):\n";
            echo str_repeat("-", 45) . "\n";
            echo sprintf("%-10s | %-19s | %-6s\n", "PIN", "Scan Time", "Verify");
            echo str_repeat("-", 45) . "\n";

            foreach ($result['data'] as $log) {
                echo sprintf("%-10s | %-19s | %-6s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['verify']
                );
            }
            echo str_repeat("-", 45) . "\n";
        } else {
            echo "No logs found for this period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Raw Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
Example Request Body:
---------------------------------------------------------------------------
{
    "trans_id": "1705824000",
    "cloud_id": "FIO123456789",
    "start_date": "2024-01-20",
    "end_date": "2024-01-21"
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
            "scan": "2024-01-21 08:00:12",
            "verify": 1,
            "status_scan": 0
        },
        {
            "pin": "102",
            "scan": "2024-01-21 08:05:45",
            "verify": 1,
            "status_scan": 0
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
