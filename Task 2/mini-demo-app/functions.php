<?php
/**
 * Utility functions for Fingerspot Mini Demo App
 *
 * This script provides a wrapper for API requests and common logic
 * used throughout the dashboard.
 */
require_once 'config.php';

/**
 * Send a POST request to the Fingerspot API using cURL.
 *
 * @param string $endpoint The API endpoint name (e.g., 'get_device')
 * @param array $data The payload to send in the request body
 * @return array The decoded JSON response
 */
function fingerspot_api_request($endpoint, $data = []) {
    $url = API_URL . '/' . $endpoint;

    // Ensure a transaction ID is present for tracking
    if (!isset($data['trans_id'])) {
        $data['trans_id'] = (string)time();
    }

    // Set headers
    $headers = [
        'Authorization: Bearer ' . API_TOKEN,
        'Content-Type: application/json'
    ];

    // Initialize cURL
    $ch = curl_init($url);

    // Set options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local/internship testing
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Execute request
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($error) {
        return [
            'status' => false,
            'message' => "cURL Error: $error"
        ];
    }

    if ($httpCode === 401) {
        return [
            'status' => false,
            'message' => "Unauthorized: Please check your API Token in config.php"
        ];
    }

    $decoded = json_decode($response, true);
    if ($decoded === null) {
        return [
            'status' => false,
            'message' => "Invalid API response format"
        ];
    }

    return $decoded;
}

/**
 * Retrieve all registered devices.
 */
function get_all_devices() {
    return fingerspot_api_request('get_device');
}

/**
 * Retrieve attendance logs for a specific device and date range.
 */
function get_device_logs($cloud_id, $start_date, $end_date) {
    return fingerspot_api_request('get_attlog', [
        'cloud_id' => $cloud_id,
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);
}

/**
 * Send employee information to a device.
 */
function push_employee($cloud_id, $pin, $name) {
    return fingerspot_api_request('set_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin,
        'name' => $name,
        'privilege' => '0'
    ]);
}

/**
 * Remote deletion of an employee from a device.
 */
function remove_employee($cloud_id, $pin) {
    return fingerspot_api_request('delete_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin
    ]);
}

/**
 * Trigger time synchronization on a device.
 */
function sync_device_time($cloud_id) {
    return fingerspot_api_request('set_time', [
        'cloud_id' => $cloud_id
    ]);
}
?>
