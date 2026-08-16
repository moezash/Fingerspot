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
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data from the request body
$json = file_get_contents('php://input');

// 2. Decode the JSON data
$data = json_decode($json, true);

// 3. Check if data was received
if ($data) {
    // Log the data for debugging (optional)
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - " . $json . PHP_EOL, FILE_APPEND);

    // 4. Process based on the type of data
    // Fingerspot often includes a "type" or checks for specific fields

    if (isset($data['type'])) {
        switch ($data['type']) {
            case 'attlog':
                // Realtime attendance scan
                processAttendance($data);
                break;

            case 'get_userinfo':
                // Response from a Get Userinfo request
                processUserInfo($data);
                break;

            case 'reg_online':
                // Result of a registration process
                processRegistration($data);
                break;

            case 'delete_userinfo':
                // Result of a delete process
                processDeleteStatus($data);
                break;
        }
    }

    // 5. Always respond with a 200 OK to acknowledge receipt
    http_response_code(200);
    echo json_encode(["status" => true, "message" => "Data received"]);
} else {
    // No data received
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "No data received"]);
}

/**
 * Example function to process attendance logs
 */
function processAttendance($data) {
    $pin = $data['data']['pin'] ?? 'Unknown';
    $time = $data['data']['scan'] ?? 'Unknown';
    $cloudId = $data['cloud_id'] ?? 'Unknown';

    // Log to a file or database
    // error_log("Attendance: User $pin scanned at $time on device $cloudId");
}

function processUserInfo($data) {
    // Handle user data received from machine
}

function processRegistration($data) {
    // Handle registration result
}

function processDeleteStatus($data) {
    // Handle delete result
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
    "trans_id": "1",
    "data": {
        "pin": "1",
        "name": "Budi",
        "privilege": "0",
        "finger": "1",
        "face": "0",
        "password": "111",
        "rfid": "",
        "template": "..."
    }
}
*/
?>
