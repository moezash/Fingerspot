<?php
/**
 * Sample code for Webhook Receiver for Fingerspot API
 *
 * This sample demonstrates how to handle incoming push data from
 * Fingerspot Cloud. This includes real-time attendance logs and
 * responses to asynchronous commands (like Get User Info).
 *
 * Requirements:
 * - This script must be accessible via a public URL.
 * - Configure this URL in your Fingerspot Developer Dashboard.
 *
 * Documentation: https://developer.fingerspot.io
 */

// 1. Get the raw POST data
$rawData = file_get_contents('php://input');

// 2. Decode JSON data
$data = json_decode($rawData, true);

// 3. Log the incoming request for debugging (Optional)
// file_put_contents('webhook.log', "[" . date('Y-m-d H:i:s') . "] " . $rawData . PHP_EOL, FILE_APPEND);

// 4. Process the data
if ($data) {
    /**
     * Fingerspot Webhooks usually contain a 'type' or specific keys
     * to identify the data.
     *
     * Common types:
     * - 'attlog': Real-time scan data
     * - 'userinfo': Response from get_userinfo
     */

    // Example: Handling Real-time Attendance Logs
    if (isset($data['type']) && $data['type'] === 'attlog') {
        // Process attendance log
        $pin  = $data['pin'] ?? 'unknown';
        $scan = $data['scan'] ?? 'unknown';

        // TODO: Save to your database
        // error_log("Received scan for PIN $pin at $scan");
    }

    // Example: Handling User Info Response
    if (isset($data['type']) && $data['type'] === 'userinfo') {
        $pin  = $data['pin'] ?? 'unknown';
        $name = $data['name'] ?? 'unknown';

        // TODO: Update user record in your system
        // error_log("Received user info for $name (PIN: $pin)");
    }

    // 5. Always respond with a 200 OK to acknowledge receipt
    http_response_code(200);
    echo json_encode(['status' => true, 'message' => 'Received']);
} else {
    // Invalid request
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Invalid data']);
}

/*
Example Incoming Payload (Attendance Log):
{
    "type": "attlog",
    "cloud_id": "FTV123456",
    "pin": "101",
    "scan": "2024-01-01 08:00:15",
    "verify": "1",
    "status_scan": "0"
}

Example Incoming Payload (User Info):
{
    "type": "userinfo",
    "cloud_id": "FTV123456",
    "pin": "101",
    "name": "Alice Smith",
    "privilege": "0",
    "template": [
        {"type": "fingerprint", "index": "0", "data": "..."}
    ]
}
*/
?>
