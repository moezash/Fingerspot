<?php
/**
 * Sample code for Webhook Receiver
 *
 * This script demonstrates how to receive and process real-time data
 * sent by Fingerspot (e.g., attendance logs or command responses).
 *
 * Instructions:
 * 1. Host this script on a publicly accessible URL (e.g., https://yourdomain.com/webhook.php).
 * 2. Register this URL in your Fingerspot Developer Dashboard.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data from the request body
// Fingerspot sends data as a JSON object in the body of a POST request.
$json = file_get_contents('php://input');

// 2. Decode the JSON data into an associative array
$data = json_decode($json, true);

// 3. Process the incoming data
if ($data) {
    /**
     * Optional: Log the incoming data for debugging.
     * Ensure the 'logs' directory exists and is writable.
     */
    // file_put_contents('webhook_incoming.log', date('Y-m-d H:i:s') . " - " . $json . PHP_EOL, FILE_APPEND);

    // Identify the type of event/data
    $type = $data['type'] ?? '';
    $cloudId = $data['cloud_id'] ?? 'Unknown';

    switch ($type) {
        case 'attlog':
            /**
             * Real-time attendance scan.
             * Triggered whenever someone scans their finger/face on the device.
             */
            processAttendance($cloudId, $data['data']);
            break;

        case 'get_userinfo':
            /**
             * Response to a 'get_userinfo' command.
             * Contains the requested user details and templates.
             */
            processUserInfo($cloudId, $data['data']);
            break;

        case 'reg_online':
            /**
             * Result of a remote registration process.
             */
            processRegistrationResult($cloudId, $data['data']);
            break;

        case 'delete_userinfo':
            /**
             * Result of a delete user command.
             */
            processDeleteResult($cloudId, $data);
            break;

        default:
            // Unknown or unhandled type
            break;
    }

    // 4. Always respond with a 200 OK to acknowledge receipt.
    // If you don't respond with 200, Fingerspot may attempt to resend the data.
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(["status" => true, "message" => "Webhook received successfully"]);

} else {
    // No valid data received
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Invalid or empty payload"]);
}

/**
 * Handle real-time attendance logs
 */
function processAttendance($cloudId, $log) {
    $pin = $log['pin'] ?? '';
    $scanTime = $log['scan'] ?? '';
    $status = $log['status_scan'] ?? '';

    // Logic: Save to database, send notification, etc.
    // Example: error_log("Device $cloudId: User $pin scanned at $scanTime");
}

/**
 * Handle incoming user information
 */
function processUserInfo($cloudId, $userInfo) {
    $pin = $userInfo['pin'] ?? '';
    $name = $userInfo['name'] ?? '';
    // $template = $userInfo['template'] ?? '';

    // Logic: Update local user record or store templates
}

/**
 * Handle registration result
 */
function processRegistrationResult($cloudId, $regData) {
    // Logic: Confirm user was registered successfully
}

/**
 * Handle delete result
 */
function processDeleteResult($cloudId, $result) {
    // Logic: Verify deletion success
}

/*
---------------------------------------------------------------------------
Example Incoming Real-time Attlog (JSON):
---------------------------------------------------------------------------
{
    "type": "attlog",
    "cloud_id": "FTV123456",
    "data": {
        "pin": "101",
        "scan": "2024-01-21 10:15:00",
        "verify": "1",
        "status_scan": "0"
    }
}

---------------------------------------------------------------------------
Example Response from your Server:
---------------------------------------------------------------------------
HTTP/1.1 200 OK
Content-Type: application/json

{
    "status": true,
    "message": "Webhook received successfully"
}
---------------------------------------------------------------------------
*/
?>
