<?php
/**
 * Utility functions for Fingerspot Mini Demo App
 *
 * This file contains the core logic for communicating with the
 * Fingerspot Cloud API using pure PHP and cURL.
 */
require_once 'config.php';

/**
 * Send a POST request to the Fingerspot API
 *
 * @param string $endpoint The API endpoint (e.g., 'get_device')
 * @param array $data The data to send in the request body
 * @return array The decoded JSON response
 */
function fingerspot_request($endpoint, $data = []) {
    $url = API_URL . '/' . $endpoint;

    // Ensure trans_id is present for request tracking
    if (!isset($data['trans_id'])) {
        $data['trans_id'] = (string)time();
    }

    $headers = [
        'Authorization: Bearer ' . API_TOKEN,
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // SSL Verification:
    // Set to false for local testing if needed. Set to true in production.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['status' => false, 'message' => "cURL Error: $error"];
    }

    $decoded = json_decode($response, true);
    if ($decoded === null) {
        return ['status' => false, 'message' => "Invalid JSON response from API."];
    }

    return $decoded;
}

/**
 * Retrieve all registered devices
 */
function get_devices() {
    return fingerspot_request('get_device');
}

/**
 * Retrieve attendance logs for a specific device and date range
 */
function get_attendance($cloud_id, $start_date, $end_date) {
    return fingerspot_request('get_attlog', [
        'cloud_id' => $cloud_id,
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);
}

/**
 * Add a new employee to a machine
 */
function add_employee($cloud_id, $pin, $name) {
    return fingerspot_request('set_userinfo', [
        'cloud_id'  => $cloud_id,
        'pin'       => $pin,
        'name'      => $name,
        'privilege' => '0'
    ]);
}

/**
 * Delete an employee from a machine
 */
function delete_employee($cloud_id, $pin) {
    return fingerspot_request('delete_userinfo', [
        'cloud_id' => $cloud_id,
        'pin'      => $pin
    ]);
}

/**
 * Remotely synchronize machine time
 */
function sync_time($cloud_id) {
    return fingerspot_request('set_time', [
        'cloud_id' => $cloud_id
    ]);
}

/**
 * Check if the API response indicates success
 * Fingerspot API usually returns 'status' as a boolean.
 */
function is_success($response) {
    return isset($response['status']) && $response['status'] === true;
}
?>
