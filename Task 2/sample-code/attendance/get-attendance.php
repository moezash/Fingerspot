<?php
/**
 * Fingerspot API Sample Code: Get Attendance Logs
 *
 * This sample demonstrates how to retrieve attendance scan logs from a
 * specific device within a specified date range.
 *
 * Requirements:
 * - api_token: Obtain from Fingerspot Developer Dashboard
 * - cloud_id: The unique ID of your registered device
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Configuration
$apiToken = 'YOUR_API_TOKEN_HERE';
$cloudId  = 'YOUR_CLOUD_ID_HERE'; // E.g., 'FTV12345678'
$apiUrl   = 'https://developer.fingerspot.io/api/get_attlog';

// 2. Prepare Request Data
// Note: Fingerspot API recommends a maximum range of 2 days per request for performance.
$data = [
    'trans_id'   => uniqid('attlog_'),   // Unique transaction identifier
    'cloud_id'   => $cloudId,            // Device Identifier
    'start_date' => date('Y-m-d'),       // Start date (YYYY-MM-DD)
    'end_date'   => date('Y-m-d')        // End date (YYYY-MM-DD)
];

// 3. Prepare Headers
$headers = [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
];

// 4. Initialize and Configure cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

/**
 * SECURITY NOTE:
 * CURLOPT_SSL_VERIFYPEER is set to true by default for production security.
 * Setting it to false is strictly for local development troubleshooting ONLY.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// 5. Execute Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Check for cURL Errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // 7. Parse and Process Response
    $result = json_decode($response, true);

    echo "--- Fingerspot API: Get Attendance Logs Sample ---\n";
    echo "Requesting logs for Cloud ID: $cloudId\n";
    echo "Date Range: " . $data['start_date'] . " to " . $data['end_date'] . "\n";
    echo "HTTP Status Code: $httpCode\n\n";

    // Handle JSON decode errors
    if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "Error: Invalid JSON response from server.\n";
        exit;
    }

    // Check for application-level success
    $success = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($success) {
        echo "Logs retrieved successfully:\n";
        if (!empty($result['data'])) {
            foreach ($result['data'] as $log) {
                echo "- PIN: " . htmlspecialchars($log['pin']) . " | Time: " . ($log['scan'] ?? 'N/A') . " | Status: " . ($log['status_scan'] ?? '0') . "\n";
            }
        } else {
            echo "No logs found for the selected period.\n";
        }
    } else {
        echo "Failed to retrieve logs.\n";
        echo "Error Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        if (isset($result['error_code'])) {
            echo "Error Code: " . $result['error_code'] . "\n";
        }
    }
}

curl_close($ch);

/*
Example Request Body:
{
    "trans_id": "attlog_65f1234567890",
    "cloud_id": "FTV12345678",
    "start_date": "2024-05-20",
    "end_date": "2024-05-21"
}

Example Response (Success):
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "pin": "101",
            "scan": "2024-05-20 08:00:00",
            "verify": "1",
            "status_scan": "0"
        },
        {
            "pin": "101",
            "scan": "2024-05-20 17:05:32",
            "verify": "1",
            "status_scan": "1"
        }
    ]
}

Example Response (Error):
{
    "status": false,
    "error_code": "1002",
    "message": "Invalid Cloud ID"
}
*/
?>
