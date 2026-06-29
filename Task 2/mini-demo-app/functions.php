<?php
/**
 * Helper functions for Fingerspot Mini Demo App
 */

require_once 'config.php';

/**
 * Generic function to make a POST request to Fingerspot API
 *
 * @param string $endpoint The API endpoint (e.g., 'get_device')
 * @param array $data The payload data
 * @return array The decoded JSON response
 */
function fingerspot_request($endpoint, $data = []) {
    $apiUrl = API_URL . '/' . $endpoint;

    // Ensure trans_id exists
    if (!isset($data['trans_id'])) {
        $data['trans_id'] = uniqid();
    }

    $headers = [
        'Authorization: Bearer ' . API_TOKEN,
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Setting CURLOPT_SSL_VERIFYPEER to true for production security.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['status' => false, 'message' => "cURL Error: $error"];
    }

    $result = json_decode($response, true);
    if ($result === null) {
        return ['status' => false, 'message' => "Invalid response from server"];
    }

    return $result;
}

/**
 * Get all devices
 */
function get_devices() {
    return fingerspot_request('get_device');
}

/**
 * Get attendance logs
 */
function get_attendance($cloud_id, $start_date, $end_date) {
    return fingerspot_request('get_attlog', [
        'cloud_id' => $cloud_id,
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);
}

/**
 * Add an employee
 */
function add_employee($cloud_id, $pin, $name, $privilege = 0) {
    return fingerspot_request('set_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin,
        'name' => $name,
        'privilege' => $privilege
    ]);
}

/**
 * Delete an employee
 */
function delete_employee($cloud_id, $pin) {
    return fingerspot_request('delete_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin
    ]);
}

/**
 * Sync device time
 */
function sync_time($cloud_id) {
    return fingerspot_request('set_time', [
        'cloud_id' => $cloud_id
    ]);
}

/**
 * Helper to set flash message
 */
function set_flash_message($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Helper to get and clear flash message
 */
function get_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
