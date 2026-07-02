<?php
/**
 * Sample code for Webhook Receiver (Fingerspot Push Data)
 *
 * This script demonstrates how to handle real-time data pushed
 * from Fingerspot Cloud to your server.
 *
 * IMPORTANT: To use this, you must configure this URL in your
 * Fingerspot Developer Dashboard.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Log the incoming request (optional, for debugging)
$rawInput = file_get_contents('php://input');
$headers  = getallheaders();

// 2. Process only if there is data
if (!empty($rawInput)) {
    // Decode the JSON data
    $data = json_decode($rawInput, true);

    if ($data) {
        // 3. Determine the type of data received
        // Note: The exact structure depends on the command or trigger

        if (isset($data['type']) && $data['type'] === 'attlog') {
            // Handle Real-time Attendance Scan
            $pin  = $data['pin'];
            $scan = $data['scan'];
            $sn   = $data['cloud_id'];

            error_log("New Scan: User $pin at $scan on Device $sn");

        } elseif (isset($data['type']) && $data['type'] === 'get_userinfo') {
            // Handle User Info Response (Asynchronous result of /api/get_userinfo)
            $pin  = $data['pin'];
            $name = $data['name'];

            error_log("User Info Received: $name (PIN: $pin)");

        } else {
            // General command response or other notifications
            error_log("Webhook received data: " . $rawInput);
        }

        // 4. Respond to Fingerspot (Success 200 OK)
        http_response_code(200);
        echo json_encode(['status' => true, 'message' => 'Data received']);
    } else {
        // Invalid JSON
        http_response_code(400);
        echo json_encode(['status' => false, 'message' => 'Invalid JSON']);
    }
} else {
    // No data received
    http_response_code(200);
    echo "Fingerspot Webhook Receiver is active.";
}

/**
 * Example Webhook Payload (Attendance Scan):
 * {
 *   "type": "attlog",
 *   "cloud_id": "FTV123456",
 *   "pin": "1",
 *   "scan": "2024-01-20 15:00:01",
 *   "verify": "1",
 *   "status_scan": "0"
 * }
 *
 * Example Webhook Payload (Get User Info Result):
 * {
 *   "type": "get_userinfo",
 *   "cloud_id": "FTV123456",
 *   "trans_id": "65ac1234",
 *   "pin": "101",
 *   "name": "John Doe",
 *   "privilege": "0",
 *   "finger_data": "...",
 *   "face_data": "..."
 * }
 */
?>
