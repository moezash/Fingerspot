<?php
/**
 * Sample code for Get Attendance Logs from Fingerspot API
 *
 * This script retrieves scan logs from a specific machine within a date range.
 *
 * Requirements:
 * - Pure PHP + cURL
 * - API Token & Cloud ID (Serial Number)
 *
 * Documentation: https://developer.fingerspot.io
 */

// --- 1. CONFIGURATION ---
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // The Serial Number of the machine
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// --- 2. PREPARE REQUEST DATA ---
$data = [
    'trans_id'   => '1',                // Unique transaction ID
    'cloud_id'   => $cloudId,           // Target device
    'start_date' => date('Y-m-d'),      // Today (YYYY-MM-DD)
    'end_date'   => date('Y-m-d')       // Today (YYYY-MM-DD)
];

$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json'
];

// --- 3. EXECUTE cURL ---
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// --- 4. PROCESS RESULT ---
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);

    echo "=== Get Attendance Logs ===\n";
    echo "Cloud ID: $cloudId\n";
    echo "Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n\n";

    if ($result && isset($result['status']) && $result['status']) {
        if (!empty($result['data'])) {
            echo "PIN      | Scan Time           | Status\n";
            echo "---------|---------------------|-------\n";
            foreach ($result['data'] as $log) {
                printf("%-8s | %-19s | %s\n",
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
        echo "Error: " . ($result['message'] ?? 'Unknown API Error') . "\n";
    }
}

curl_close($ch);

/*
---------------------------------------------------------------------------
EXAMPLE REQUEST:
---------------------------------------------------------------------------
POST /api/get_attlog HTTP/1.1
Host: developer.fingerspot.io
Authorization: Bearer YOUR_API_TOKEN_HERE
Content-Type: application/json

{
    "trans_id": "1",
    "cloud_id": "FTVXXXXXX",
    "start_date": "2024-01-01",
    "end_date": "2024-01-01"
}

---------------------------------------------------------------------------
EXAMPLE RESPONSE (SUCCESS):
---------------------------------------------------------------------------
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
*/
?>
