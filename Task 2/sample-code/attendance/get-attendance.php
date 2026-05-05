<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve scan data from a specific
 * attendance machine within a date range.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of the device
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Payload
// The API expects start_date and end_date in YYYY-MM-DD format
$payload = [
    'trans_id'   => (string)time(),
    'cloud_id'   => $cloudId,
    'start_date' => date('Y-m-d'), // Today
    'end_date'   => date('Y-m-d')  // Today
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize and Execute cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 5. Error Handling and Output
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Attendance Logs ---\n";
    echo "Cloud ID: $cloudId\n";
    echo "Period: " . $payload['start_date'] . " to " . $payload['end_date'] . "\n";
    echo "HTTP Status: $httpCode\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        if (!empty($result['data'])) {
            echo "Scan Logs Found (" . count($result['data']) . "):\n";
            echo str_repeat("-", 60) . "\n";
            echo sprintf("%-10s | %-20s | %-10s | %-10s\n", "PIN", "Scan Time", "Verify", "Status");
            echo str_repeat("-", 60) . "\n";

            foreach ($result['data'] as $log) {
                echo sprintf("%-10s | %-20s | %-10s | %-10s\n",
                    $log['pin'],
                    $log['scan'],
                    $log['verify'],
                    $log['status_scan']
                );
            }
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve attendance logs.\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response: " . $response . "\n";
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "1715679000",
    "cloud_id": "FTV12345678",
    "start_date": "2024-05-14",
    "end_date": "2024-05-14"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "pin": "101",
            "scan": "2024-05-14 08:00:05",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "101",
            "scan": "2024-05-14 17:05:22",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}
*/
?>
