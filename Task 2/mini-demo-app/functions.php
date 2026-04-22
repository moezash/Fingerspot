<?php
/**
 * Utility functions for Fingerspot Mini Demo App
 * Handles API communication with developer.fingerspot.io
 */
require_once 'config.php';

/**
 * Send a POST request to the Fingerspot API using cURL
 *
 * @param string $endpoint The API endpoint (e.g., 'get_device')
 * @param array $data The data to send in the request body
 * @return array The decoded JSON response
 */
function fingerspot_request($endpoint, $data = []) {
    $url = API_URL . '/' . $endpoint;

    // Generate a unique trans_id if not provided
    if (!isset($data['trans_id'])) {
        $data['trans_id'] = (string)time();
    }

    // Prepare headers with Bearer Token
    $headers = [
        'Authorization: Bearer ' . API_TOKEN,
        'Content-Type: application/json'
    ];

    // Initialize cURL session
    $ch = curl_init($url);

    // Set options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disabled for simplicity in demo
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Execute request
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Handle cURL errors
    if ($error) {
        return ['status' => false, 'message' => "cURL Error: $error"];
    }

    // Decode JSON response
    $decoded = json_decode($response, true);
    if ($decoded === null) {
        return ['status' => false, 'message' => "Invalid JSON response ($httpCode): $response"];
    }

    return $decoded;
}

/**
 * Get the list of all registered devices
 */
function get_devices() {
    return fingerspot_request('get_device');
}

/**
 * Get attendance logs for a specific device and date range
 */
function get_attendance($cloud_id, $start_date, $end_date) {
    return fingerspot_request('get_attlog', [
        'cloud_id' => $cloud_id,
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);
}

/**
 * Send new employee information to the device
 */
function add_employee($cloud_id, $pin, $name) {
    return fingerspot_request('set_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin,
        'name' => $name,
        'privilege' => '0'
    ]);
}

/**
 * Command the device to delete a specific employee
 */
function delete_employee($cloud_id, $pin) {
    return fingerspot_request('delete_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin
    ]);
}

/**
 * Request the device to synchronize its time with the server
 */
function sync_time($cloud_id) {
    return fingerspot_request('set_time', [
        'cloud_id' => $cloud_id
    ]);
}

/**
 * Command the device to restart
 */
function restart_device($cloud_id) {
    return fingerspot_request('restart', [
        'cloud_id' => $cloud_id
    ]);
}
?>
