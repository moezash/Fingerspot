<?php
/**
 * Sample code for Webhook Receiver
 *
 * This script demonstrates how to receive and process real-time data
 * sent by Fingerspot (e.g., attendance logs or command responses).
 *
 * Requirements:
 * - A publicly accessible web server (URL must be configured in
 *   the Fingerspot Developer Dashboard).
 * - PHP file_get_contents('php://input') to read the raw JSON body.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data from the request body
$json = file_get_contents('php://input');

// 2. Decode the JSON data
$data = json_decode($json, true);

// 3. Check if data was received
if ($data) {
    // Log the data for debugging (optional)
    // Ensure the script has write permissions to this file
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - " . $json . PHP_EOL, FILE_APPEND);

    // 4. Process based on the type of data
    // Fingerspot uses the "type" field to identify the content
    if (isset($data['type'])) {
        switch ($data['type']) {
            case 'attlog':
                // Real-time attendance scan occurred
                processAttendance($data);
                break;

            case 'get_userinfo':
                // Response from a previously sent Get Userinfo request
                processUserInfo($data);
                break;

            case 'reg_online':
                // Result of a remote registration process
                processRegistration($data);
                break;

            case 'delete_userinfo':
                // Result of a delete user process
                processDeleteStatus($data);
                break;

            default:
                // Handle other types if necessary
                break;
        }
    }

    // 5. Always respond with a 200 OK to acknowledge receipt
    // Fingerspot will retry if a 2xx status code is not returned
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(["status" => true, "message" => "Data received"]);
} else {
    // No data received or invalid JSON
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "No data received or invalid format"]);
}

/**
 * Example function to process real-time attendance logs
 */
function processAttendance($data) {
    $cloudId = $data['cloud_id'] ?? 'Unknown';
    $pin     = $data['data']['pin'] ?? 'Unknown';
    $time    = $data['data']['scan'] ?? 'Unknown';
    $status  = $data['data']['status_scan'] ?? '0';

    // Business Logic: Save to database, send email/SMS, etc.
    // error_log("Attendance: User $pin scanned at $time on device $cloudId");
}

/**
 * Example function to process user data requested from machine
 */
function processUserInfo($data) {
    $cloudId = $data['cloud_id'] ?? 'Unknown';
    $pin     = $data['data']['pin'] ?? 'Unknown';
    $name    = $data['data']['name'] ?? 'Unknown';

    // Business Logic: Sync local database with device data
}

function processRegistration($data) {
    // Handle registration success/failure
}

function processDeleteStatus($data) {
    // Handle delete status update
}

/*
---------------------------------------------------------------------------
Example Incoming Realtime Attlog:
---------------------------------------------------------------------------
{
    "type": "attlog",
    "cloud_id": "FTVXXXXXX",
    "data": {
        "pin": "1",
        "scan": "2024-01-21 10:11:00",
        "verify": "1",
        "status_scan": "0"
    }
}

---------------------------------------------------------------------------
Example Incoming Userinfo Response:
---------------------------------------------------------------------------
{
    "type": "get_userinfo",
    "cloud_id": "FTVXXXXXX",
    "trans_id": "1",
    "data": {
        "pin": "101",
        "name": "John Doe",
        "privilege": "0",
        "finger": "1",
        "face": "0",
        "password": "",
        "rfid": "",
        "template": "..."
    }
}
*/
?>
