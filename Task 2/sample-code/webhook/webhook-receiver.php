<?php
/**
 * Sample code for Webhook Receiver for Fingerspot API
 *
 * This script demonstrates how to receive and process real-time push data
 * from the Fingerspot Cloud API (e.g., scan logs, command responses).
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get raw POST data
$jsonData = file_get_contents('php://input');

// 2. Decode JSON
$data = json_decode($jsonData, true);

// 3. Process the data
if ($data) {
    // Log the received data for debugging
    // In production, use a proper logging system
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - Received: " . $jsonData . PHP_EOL, FILE_APPEND);

    /**
     * Identify data type based on available keys
     */
    if (isset($data['type']) && $data['type'] === 'attlog') {
        // Handle Real-time Attendance Scan
        $pin = $data['pin'];
        $scanTime = $data['scan'];
        $cloudId = $data['cloud_id'];

        // Logic to save to database goes here
        // ...
    } elseif (isset($data['trans_id'])) {
        // Handle Asynchronous Command Response (e.g., Get Userinfo, Set Userinfo)
        $transId = $data['trans_id'];
        $status = $data['status'] ?? 'unknown';

        // Logic to update command status in your system
        // ...
    }

    // 4. Respond to Fingerspot (Optional but good practice)
    // Some systems expect a 200 OK to acknowledge receipt
    http_response_code(200);
    echo json_encode(['status' => true, 'message' => 'Data received']);
} else {
    // Invalid or empty request
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Invalid data']);
}

/*
Example Webhook Payload (Real-time Attendance):
{
    "type": "attlog",
    "cloud_id": "FTV123456",
    "pin": "1",
    "scan": "2024-01-01 08:00:15",
    "verify": "1",
    "status_scan": "0"
}

Example Webhook Payload (Get Userinfo Response):
{
    "trans_id": "get_usr_65a1234567890",
    "cloud_id": "FTV123456",
    "status": true,
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "template": [
        {
            "index": "0",
            "type": "finger",
            "data": "..."
        }
    ]
}
*/
?>
