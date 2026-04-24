<?php
/**
 * Sample code for Webhook Receiver
 *
 * This script demonstrates how to receive and process real-time data
 * sent by Fingerspot (e.g., attendance logs or command responses).
 *
 * Requirements:
 * - Your server must be publicly accessible (not localhost).
 * - The Webhook URL must be configured in the Fingerspot Developer Dashboard.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data from the request body
// Fingerspot sends data as a JSON payload.
$json = file_get_contents('php://input');

// 2. Decode the JSON data
$data = json_decode($json, true);

// 3. Check if data was received
if ($data) {
    /**
     * LOGGING (Optional)
     * For debugging, it is useful to log the raw JSON received.
     */
    // file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - " . $json . PHP_EOL, FILE_APPEND);

    /**
     * 4. Process based on the type of data
     * Fingerspot uses a "type" field to distinguish between different events.
     */
    if (isset($data['type'])) {
        switch ($data['type']) {
            case 'attlog':
                // Real-time attendance scan event
                processAttendance($data);
                break;

            case 'get_userinfo':
                // Response containing requested user information
                processUserInfo($data);
                break;

            case 'reg_online':
                // Result of a registration process (Online Registration)
                processRegistrationResult($data);
                break;

            case 'delete_userinfo':
                // Result of a user deletion process
                processDeleteResult($data);
                break;

            default:
                // Handle other unknown types
                break;
        }
    }

    // 5. Always respond with a 200 OK to acknowledge receipt.
    // If the API doesn't receive a 200 OK, it might retry sending the data.
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(["status" => true, "message" => "Data received"]);
} else {
    // No data received or invalid JSON
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Invalid or missing data"]);
}

/**
 * Example function to process real-time attendance logs
 */
function processAttendance($payload) {
    $cloudId = $payload['cloud_id'] ?? 'Unknown';
    $logData = $payload['data'] ?? [];

    $pin  = $logData['pin'] ?? 'N/A';
    $time = $logData['scan'] ?? 'N/A';

    // Logic: Save to database, send notification, etc.
}

/**
 * Example function to process User Information responses
 */
function processUserInfo($payload) {
    $cloudId = $payload['cloud_id'] ?? 'Unknown';
    $user    = $payload['data'] ?? [];

    $name = $user['name'] ?? 'N/A';
    $pin  = $user['pin'] ?? 'N/A';

    // Logic: Update local employee records
}

/**
 * Example function to process Registration results
 */
function processRegistrationResult($payload) {
    // Logic: Confirm if user registration was successful
}

/**
 * Example function to process Delete results
 */
function processDeleteResult($payload) {
    // Logic: Update local state to reflect the user is removed from machine
}

/*
---------------------------------------------------------------------------
EXAMPLE INCOMING REAL-TIME ATTLOG (JSON)
---------------------------------------------------------------------------
{
    "type": "attlog",
    "cloud_id": "FTV123456789",
    "data": {
        "pin": "101",
        "scan": "2024-01-21 10:11:00",
        "verify": "1",
        "status_scan": "0"
    }
}

---------------------------------------------------------------------------
EXAMPLE INCOMING USERINFO RESPONSE (JSON)
---------------------------------------------------------------------------
{
    "type": "get_userinfo",
    "cloud_id": "FTV123456789",
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
