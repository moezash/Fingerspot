<?php
/**
 * Sample code for Webhook Receiver for Fingerspot API
 *
 * This sample demonstrates how to receive and process real-time data
 * sent by Fingerspot Cloud to your server (e.g., scan logs, command results).
 *
 * Requirements:
 * 1. This file must be accessible via a public URL.
 * 2. Configure this URL in your Fingerspot Developer Dashboard.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data
$rawData = file_get_contents('php://input');

// 2. Decode the JSON data
$data = json_decode($rawData, true);

// 3. Log the request (Optional, for debugging)
file_put_contents('webhook_log.txt', date('[Y-m-d H:i:s] ') . $rawData . PHP_EOL, FILE_APPEND);

// 4. Process the data
if ($data) {
    // Check the type of data received
    // Fingerspot sends different data formats depending on the event

    if (isset($data['type'])) {
        switch ($data['type']) {
            case 'attlog':
                // Real-time attendance scan data
                error_log("Received scan log for PIN: " . ($data['pin'] ?? 'N/A'));
                break;

            case 'get_userinfo':
                // Result of get_userinfo command
                error_log("Received user info for PIN: " . ($data['pin'] ?? 'N/A'));
                break;

            case 'register':
                // Result of remote registration
                error_log("Received registration result for PIN: " . ($data['pin'] ?? 'N/A'));
                break;
        }
    }

    // 5. Always respond with a 200 OK to acknowledge receipt
    http_response_code(200);
    echo json_encode(['status' => true, 'message' => 'Data received']);
} else {
    // Invalid request
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Invalid JSON']);
}

/*
Example Incoming Request (Attendance Log):
POST /webhook-receiver.php HTTP/1.1
Content-Type: application/json

{
    "type": "attlog",
    "cloud_id": "FTV123456",
    "pin": "1",
    "scan": "2024-01-01 08:00:15",
    "verify": "1",
    "status_scan": "0"
}

Example Response (Success):
HTTP/1.1 200 OK
Content-Type: application/json

{
    "status": true,
    "message": "Data received"
}
*/
?>
