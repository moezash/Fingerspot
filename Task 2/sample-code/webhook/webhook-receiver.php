<?php
/**
 * Sample code for Webhook Receiver
 *
 * This script demonstrates how to receive and process real-time data
 * sent by Fingerspot (e.g., attendance logs or command responses).
 *
 * Note: Your server must be publicly accessible and the URL
 * configured in the Fingerspot Developer Dashboard.
 *
 * Requirements:
 * - Pure PHP (no frameworks)
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data from the request body
$json = file_get_contents('php://input');

// 2. Decode the JSON data
$data = json_decode($json, true);

// 3. Check if data was received
if ($data) {
    // Optional: Log the data for debugging
    // file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - " . $json . PHP_EOL, FILE_APPEND);

    /**
     * Fingerspot Webhooks usually contain a 'type' field to identify the payload.
     * Common types: 'attlog', 'get_userinfo', 'reg_online', 'delete_userinfo'.
     */
    if (isset($data['type'])) {
        switch ($data['type']) {
            case 'attlog':
                // Real-time attendance scan data
                handleAttendance($data);
                break;

            case 'get_userinfo':
                // Response from a previous 'get_userinfo' API request
                handleUserInfo($data);
                break;

            case 'reg_online':
                // Result of a remote registration process
                handleRegistrationResult($data);
                break;

            default:
                // Handle other notification types
                break;
        }
    }

    // 4. Always respond with a 200 OK to acknowledge receipt
    // If the API doesn't receive a 200 OK, it may retry sending the data.
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(["status" => true, "message" => "Webhook received"]);
} else {
    // No data received or invalid JSON
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Invalid data"]);
}

/**
 * Example logic for processing attendance logs
 */
function handleAttendance($payload) {
    $cloudId = $payload['cloud_id'] ?? 'N/A';
    $log = $payload['data'] ?? [];

    $pin = $log['pin'] ?? 'N/A';
    $time = $log['scan'] ?? 'N/A';

    // In a real app, you would save this to a database
    // saveToDatabase($pin, $time, $cloudId);
}

function handleUserInfo($payload) {
    // Process user templates and info
}

function handleRegistrationResult($payload) {
    // Check if registration was successful
}

/*
Example Incoming Payload (Attendance Log):
------------------------------------------------------------
{
    "type": "attlog",
    "cloud_id": "FTV123456",
    "data": {
        "pin": "101",
        "scan": "2024-03-01 10:11:00",
        "verify": "1",
        "status_scan": "0"
    }
}

Example Response:
------------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "status": true,
    "message": "Webhook received"
}
*/
?>
