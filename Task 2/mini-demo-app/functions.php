<?php
/**
 * Reusable functions for the Fingerspot Dashboard
 */

require_once 'config.php';

/**
 * Main helper to send requests to Fingerspot API
 *
 * @param string $endpoint The endpoint name (e.g., 'get_device')
 * @param array $data The data to send in the request body
 * @return array|null The decoded JSON response
 */
function fingerspot_request($endpoint, $data = []) {
    $url = FINGERSPOT_API_BASE . '/' . $endpoint;

    // Add trans_id if not provided
    if (!isset($data['trans_id'])) {
        $data['trans_id'] = uniqid();
    }

    $headers = [
        'Authorization: Bearer ' . FINGERSPOT_API_TOKEN,
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Security: SSL Verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['success' => false, 'message' => 'cURL Error: ' . $error];
    }

    return json_decode($response, true);
}

/**
 * Feature 1: Get Device List
 */
function get_devices() {
    return fingerspot_request('get_device');
}

/**
 * Feature 2: Get Attendance Logs
 */
function get_attendance_logs($cloud_id, $start_date, $end_date) {
    return fingerspot_request('get_attlog', [
        'cloud_id' => $cloud_id,
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);
}

/**
 * Feature 3: Add/Update Employee
 */
function set_employee($cloud_id, $pin, $name, $privilege = 0) {
    return fingerspot_request('set_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin,
        'name' => $name,
        'privilege' => $privilege
    ]);
}

/**
 * Feature 4: Delete Employee
 */
function delete_employee($cloud_id, $pin) {
    return fingerspot_request('delete_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin
    ]);
}

/**
 * Feature 5: Sync Time
 */
function sync_time($cloud_id) {
    return fingerspot_request('set_time', [
        'cloud_id' => $cloud_id,
        'time' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Helper to display alert messages
 */
function display_alert($message, $type = 'info') {
    if (!$message) return '';
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
           . htmlspecialchars($message) .
           '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
           </div>';
}
?>
