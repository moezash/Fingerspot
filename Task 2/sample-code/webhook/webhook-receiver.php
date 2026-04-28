<?php
/**
 * Sample code for Webhook Receiver
 *
 * This script demonstrates how to receive real-time data from Fingerspot Cloud.
 * Your server MUST be publicly accessible for this to work.
 */

// 1. Get raw input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if ($data) {
    // 2. Process based on 'type'
    $type = $data['type'] ?? 'unknown';

    // Log for debugging
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " [$type] " . $rawInput . PHP_EOL, FILE_APPEND);

    switch ($type) {
        case 'attlog':
            // Logic for real-time attendance scan
            // $pin = $data['data']['pin'];
            // $scanTime = $data['data']['scan'];
            break;

        case 'get_userinfo':
            // Logic for user data response
            break;
    }

    // 3. Acknowledge receipt
    http_response_code(200);
    echo json_encode(['status' => true, 'message' => 'Received']);
} else {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'No JSON data']);
}
?>
