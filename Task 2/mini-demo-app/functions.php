<?php
/**
 * Core functions for the Attendance Monitoring Dashboard
 */

require_once 'config.php';

/**
 * Generic function to make API requests to Fingerspot Cloud
 */
function fingerspot_api_request($endpoint, $data = []) {
    global $api_headers;

    $url = BASE_URL . $endpoint;

    // Ensure trans_id is present
    if (!isset($data['trans_id'])) {
        $data['trans_id'] = (string)time();
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $api_headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['status' => false, 'message' => "cURL Error: $error"];
    }

    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['status' => false, 'message' => "Invalid JSON response: " . json_last_error_msg()];
    }

    return $result;
}

/**
 * Get list of devices
 */
function get_devices() {
    $result = fingerspot_api_request('/get_device');

    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($isSuccess && isset($result['data'])) {
        return $result['data'];
    }

    return [];
}

/**
 * Get attendance logs for a specific device and date range
 */
function get_attendance_logs($cloud_id, $start_date, $end_date) {
    $data = [
        'cloud_id'   => $cloud_id,
        'start_date' => $start_date,
        'end_date'   => $end_date
    ];

    $result = fingerspot_api_request('/get_attlog', $data);

    $isSuccess = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);

    if ($isSuccess && isset($result['data'])) {
        return $result['data'];
    }

    return [];
}

/**
 * Add a new employee to a device
 */
function add_employee($cloud_id, $pin, $name, $privilege = '0') {
    $data = [
        'cloud_id'  => $cloud_id,
        'pin'       => $pin,
        'name'      => $name,
        'privilege' => $privilege
    ];

    return fingerspot_api_request('/set_userinfo', $data);
}

/**
 * Delete an employee from a device
 */
function delete_employee($cloud_id, $pin) {
    $data = [
        'cloud_id' => $cloud_id,
        'pin'      => $pin
    ];

    return fingerspot_api_request('/delete_userinfo', $data);
}

/**
 * Helper to format scan status
 */
function format_scan_status($status) {
    $statuses = [
        '0' => 'In',
        '1' => 'Out',
        '2' => 'Break Out',
        '3' => 'Break In',
        '4' => 'Overtime In',
        '5' => 'Overtime Out'
    ];

    return $statuses[$status] ?? 'Scan (' . $status . ')';
}
?>
