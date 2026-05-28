<?php
/**
 * Sample code for Webhook Receiver
 *
 * This script demonstrates how to receive and process real-time data
 * sent by Fingerspot Cloud (e.g., attendance logs or command responses).
 *
 * Requirements:
 * - Publicly accessible URL
 * - URL must be configured in your Fingerspot Developer Dashboard
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data from the request body
$json = file_get_contents('php://input');

// 2. Decode the JSON data
$data = json_decode($json, true);

// 3. Process the incoming data
if ($data) {
    // Log receipt for debugging
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - Received: " . $json . PHP_EOL, FILE_APPEND);

    // 4. Identify data type and process accordingly
    if (isset($data['type'])) {
        switch ($data['type']) {
            case 'attlog':
                // Handled when someone scans their finger/face on the machine
                handleRealtimeAttendance($data);
                break;

            case 'get_userinfo':
                // Handled as a response to the /api/get_userinfo request
                handleUserInfoResponse($data);
                break;

            case 'reg_online':
                // Handled as a response to the /api/reg_online request
                handleRegistrationResponse($data);
                break;
        }
    }

    // 5. Always respond with HTTP 200 OK to acknowledge receipt
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(["status" => true, "message" => "Acknowledged"]);
} else {
    // Invalid request
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Invalid JSON payload"]);
}

/**
 * Processing functions
 */
function handleRealtimeAttendance($payload) {
    $pin = $payload['data']['pin'] ?? 'N/A';
    $scanTime = $payload['data']['scan'] ?? 'N/A';
    // Logic: Save to database, send notification, etc.
}

function handleUserInfoResponse($payload) {
    $name = $payload['data']['name'] ?? 'N/A';
    // Logic: Update local employee records
}

function handleRegistrationResponse($payload) {
    // Logic: Confirm user was successfully registered
}

/*
Example Incoming Payload (Attendance):
------------------------------------
{
    "type": "attlog",
    "cloud_id": "FTV123456",
    "data": {
        "pin": "101",
        "scan": "2024-01-21 14:00:00",
        "verify": "1",
        "status_scan": "0"
    }
}
*/
?>
