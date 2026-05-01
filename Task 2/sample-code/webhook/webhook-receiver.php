<?php
/**
 * Sample code for Webhook Receiver
 *
 * This script demonstrates how to receive and process real-time data
 * sent by Fingerspot (e.g., attendance logs or command responses).
 *
 * Note: Your server must be publicly accessible and the URL
 * configured in the Fingerspot Developer Dashboard under "Webhook URL".
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data from the request body
$json = file_get_contents('php://input');

// 2. Decode the JSON data
$data = json_decode($json, true);

// 3. Check if data was received
if ($data) {
    // Log the data for debugging (optional but recommended during development)
    // Ensure the web server has permission to write to this file
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " [RECEIVED]: " . $json . PHP_EOL, FILE_APPEND);

    // 4. Process based on the type of data
    // Fingerspot includes a "type" field to identify the content
    $type = $data['type'] ?? '';

    switch ($type) {
        case 'attlog':
            // Real-time attendance scan event
            processAttendance($data);
            break;

        case 'get_userinfo':
            // Asynchronous response from a 'get_userinfo' request
            processUserInfo($data);
            break;

        case 'reg_online':
            // Result of a remote registration process
            processRegistrationStatus($data);
            break;

        case 'set_userinfo':
        case 'delete_userinfo':
        case 'set_time':
        case 'restart':
            // Result status of various commands sent to the machine
            processCommandResult($data);
            break;

        default:
            // Handle unknown or other types
            file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " [UNKNOWN TYPE]: " . $type . PHP_EOL, FILE_APPEND);
            break;
    }

    // 5. Respond with a 200 OK and a JSON success message to acknowledge receipt.
    // Fingerspot expects a successful HTTP response.
    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode(["status" => true, "message" => "Data received"]);
} else {
    // No valid JSON data received
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "No valid data received"]);
}

/**
 * Example function to process attendance logs
 */
function processAttendance($data) {
    $cloudId = $data['cloud_id'] ?? 'Unknown';
    $log = $data['data'] ?? [];
    $pin = $log['pin'] ?? 'Unknown';
    $time = $log['scan'] ?? 'Unknown';

    // Business Logic: Save to database, send notification, etc.
}

/**
 * Example function to process user data from Get Userinfo
 */
function processUserInfo($data) {
    $cloudId = $data['cloud_id'] ?? 'Unknown';
    $userInfo = $data['data'] ?? [];
    $pin = $userInfo['pin'] ?? 'Unknown';
    $name = $userInfo['name'] ?? 'Unknown';

    // Business Logic: Sync local employee record with machine data
}

/**
 * Example function to handle registration results
 */
function processRegistrationStatus($data) {
    $status = $data['status'] ?? false;
    $message = $data['message'] ?? '';
    // Handle success/failure of remote registration
}

/**
 * Example function to handle general command results
 */
function processCommandResult($data) {
    $type = $data['type'];
    $status = $data['status'] ?? false;
    // Log whether the command was executed successfully on the device
}

/*
Example Incoming Realtime Attlog:
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

Example Incoming Userinfo Response:
{
    "type": "get_userinfo",
    "cloud_id": "FTVXXXXXX",
    "trans_id": "1705824000",
    "status": true,
    "message": "Success",
    "data": {
        "pin": "101",
        "name": "John Doe",
        "privilege": "0",
        "password": "",
        "rfid": "",
        "finger": "1",
        "face": "0",
        "template": [...]
    }
}
*/
?>
