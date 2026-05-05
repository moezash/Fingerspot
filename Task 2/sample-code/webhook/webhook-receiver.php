<?php
/**
 * Sample code for Webhook Receiver (Fingerspot API)
 *
 * This script demonstrates how to receive and process real-time data
 * sent from the Fingerspot Cloud API to your server.
 *
 * To use this:
 * 1. Host this file on a public-facing web server.
 * 2. Configure the URL of this file in your Fingerspot Developer Dashboard.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Log file for debugging
$logFile = 'webhook_log.txt';

// 2. Read the raw POST data
$rawData = file_get_contents('php://input');

// 3. Log the raw request for debugging
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Raw Data: " . $rawData . PHP_EOL, FILE_APPEND);

// 4. Decode the JSON data
$data = json_decode($rawData, true);

if ($data) {
    // 5. Process based on data type
    // Fingerspot sends different types of data: 'attlog', 'get_userinfo', 'reg_online', etc.
    $type = $data['type'] ?? 'unknown';

    switch ($type) {
        case 'attlog':
            // Real-time scan log
            $pin = $data['pin'] ?? 'N/A';
            $scanTime = $data['scan'] ?? 'N/A';
            file_put_contents($logFile, "Processed Scan: PIN $pin at $scanTime" . PHP_EOL, FILE_APPEND);
            break;

        case 'get_userinfo':
            // Response from get_userinfo command
            $pin = $data['pin'] ?? 'N/A';
            $name = $data['name'] ?? 'N/A';
            file_put_contents($logFile, "Processed User Info: $name (PIN: $pin)" . PHP_EOL, FILE_APPEND);
            break;

        case 'reg_online':
            // Response from reg_online command
            $status = $data['status'] ?? 'unknown';
            file_put_contents($logFile, "Registration Result: $status" . PHP_EOL, FILE_APPEND);
            break;

        default:
            file_put_contents($logFile, "Received unknown webhook type: $type" . PHP_EOL, FILE_APPEND);
            break;
    }

    // 6. Respond to Fingerspot API
    // It's good practice to respond with a 200 OK
    http_response_code(200);
    echo json_encode(['status' => true, 'message' => 'Data received']);
} else {
    // Invalid data
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Invalid JSON']);
}

/*
Example Incoming Webhook (attlog):
{
    "type": "attlog",
    "cloud_id": "FTV12345678",
    "pin": "101",
    "scan": "2024-05-14 10:30:45",
    "verify": "1",
    "status_scan": "0"
}

Example Incoming Webhook (get_userinfo):
{
    "type": "get_userinfo",
    "cloud_id": "FTV12345678",
    "trans_id": "1715679300",
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "password": "",
    "rfid": "",
    "finger_data": "...",
    "face_data": "..."
}
*/
?>
