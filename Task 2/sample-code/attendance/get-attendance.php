<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This sample demonstrates how to retrieve attendance scan data
 * from a specific machine within a date range.
 *
 * Important Constraints:
 * 1. Maximum date range per request is 2 days.
 * 2. History retrieval is limited to the last 60 days.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Cloud ID of your device
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Request Data
// Note: start_date and end_date should be within a 2-day range
$data = [
    'trans_id'   => (string)time(),
    'cloud_id'   => $cloudId,
    'start_date' => date('Y-m-d', strtotime('-1 day')),
    'end_date'   => date('Y-m-d')
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
$curlErr  = curl_error($ch);

// 7. Check for cURL errors
if ($curlErr) {
    echo "cURL Error: " . $curlErr . "\n";
} else {
    // 8. Process Response
    $result = json_decode($response, true);

    echo "--- Get Attendance Logs Sample ---\n";
    echo "Device ID: $cloudId\n";
    echo "Period: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    if ($result && (isset($result['status']) && $result['status'] || isset($result['success']) && $result['success'])) {
        echo "Logs retrieved successfully:\n";

        if (!empty($result['data'])) {
            foreach ($result['data'] as $log) {
                echo "- PIN: " . $log['pin'] . " | Scan: " . $log['scan'] . " | Verify: " . $log['verify'] . " | Status: " . $log['status_scan'] . "\n";
            }
        } else {
            echo "No scan data found for this period.\n";
        }
    } else {
        echo "Failed to retrieve attendance logs.\n";
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
    "trans_id": "1704067200",
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
            "pin": "101",
            "scan": "2024-01-01 08:05:22",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "101",
            "scan": "2024-01-01 17:10:45",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

Example Response (Error - Date range too long):
{
    "status": false,
    "message": "Maximum date range is 2 days"
}
*/
?>
