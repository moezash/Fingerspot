<?php
/**
 * Sample code for Webhook Receiver for Fingerspot API
 *
 * This sample demonstrates how to handle real-time data pushed from
 * Fingerspot Cloud to your server.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Log incoming request for debugging
$rawInput = file_get_contents('php://input');
$headers = getallheaders();

// 2. Log to a file (optional)
// file_put_contents('webhook.log', "[" . date('Y-m-d H:i:s') . "] " . $rawInput . PHP_EOL, FILE_APPEND);

// 3. Process the JSON payload
$payload = json_decode($rawInput, true);

if ($payload) {
    /**
     * Webhook data can be:
     * - Real-time attendance scan
     * - Result of an asynchronous command (Get Userinfo, Reg Online, etc.)
     */

    // Check if it's an attendance scan
    if (isset($payload['pin']) && isset($payload['scan'])) {
        // Process Attendance Log
        $pin = $payload['pin'];
        $scanTime = $payload['scan'];
        $cloudId = $payload['cloud_id'] ?? 'Unknown';

        // Example: Save to your database here
    }

    // Check if it's a response to a previous request (via trans_id)
    if (isset($payload['trans_id'])) {
        $transId = $payload['trans_id'];
        // Handle command result (e.g., received user data from get_userinfo)
    }

    /**
     * IMPORTANT:
     * Fingerspot Webhook expects a successful HTTP response (200 OK)
     * as an acknowledgement that you've received the data.
     */
    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'Webhook received successfully'
    ]);
} else {
    // Invalid request
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'Invalid JSON payload'
    ]);
}

/*
Example Incoming Webhook (Attendance Scan):
{
    "cloud_id": "FTV123456",
    "pin": "1",
    "scan": "2024-01-01 08:30:45",
    "status_scan": "0",
    "verify": "1"
}

Example Incoming Webhook (User Info Response):
{
    "cloud_id": "FTV123456",
    "trans_id": "65b2a1c3e4f5e",
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "finger_data": ["...", "..."],
    "face_data": "..."
}
*/
?>
