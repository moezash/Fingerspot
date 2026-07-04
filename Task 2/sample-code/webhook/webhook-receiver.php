<?php
/**
 * Fingerspot API Sample Code: Webhook Receiver
 *
 * This sample demonstrates how to handle incoming push data from the
 * Fingerspot Cloud. The Cloud sends data to this URL when a scan occurs
 * or when an asynchronous command (like get_userinfo) completes.
 *
 * Instructions:
 * 1. Host this file on a publicly accessible server.
 * 2. Configure the URL in your Fingerspot Developer Dashboard.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data from the request body
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// 2. Log the incoming data (for debugging/monitoring)
$logFile = 'webhook_log.txt';
$logEntry = date('Y-m-d H:i:s') . " - Received Data: " . $rawInput . PHP_EOL;
file_put_contents($logFile, $logEntry, FILE_APPEND);

// 3. Process the data based on its type
if ($data) {
    /**
     * Data format for Real-time Scan:
     * {
     *   "cloud_id": "FTV12345678",
     *   "pin": "101",
     *   "scan": "2024-05-20 10:30:00",
     *   "verify": "1",
     *   "status_scan": "0"
     * }
     */
    if (isset($data['scan'])) {
        // Handle attendance scan
        // Example: Save to your local database
    }

    /**
     * Data format for Command Responses (e.g., get_userinfo):
     * {
     *   "cloud_id": "FTV12345678",
     *   "trans_id": "getuser_65f1234567890",
     *   "pin": "101",
     *   "name": "John Doe",
     *   "type": "get_userinfo",
     *   ... (templates)
     * }
     */
    if (isset($data['type'])) {
        switch ($data['type']) {
            case 'get_userinfo':
                // Handle user data retrieval
                break;
            case 'set_userinfo':
                // Handle confirmation of user update
                break;
            case 'delete_userinfo':
                // Handle confirmation of user deletion
                break;
        }
    }
}

// 4. Always respond with a 200 OK to the Fingerspot Cloud
http_response_code(200);
echo json_encode(['status' => true, 'message' => 'Data received']);

/*
Example Incoming Request Body (Real-time Scan):
{
    "cloud_id": "FTV12345678",
    "pin": "101",
    "scan": "2024-05-20 08:00:15",
    "verify": "1",
    "status_scan": "0"
}

Example Incoming Request Body (User Info Command Result):
{
    "cloud_id": "FTV12345678",
    "trans_id": "getuser_65f1234567890",
    "pin": "101",
    "name": "John Doe",
    "privilege": "0",
    "finger_data": "...",
    "face_data": "...",
    "type": "get_userinfo"
}

Example Response (from your server):
{
    "status": true,
    "message": "Data received"
}
*/
?>
