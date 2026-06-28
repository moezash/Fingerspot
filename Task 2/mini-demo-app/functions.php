<?php
/**
 * Utility functions for Fingerspot Mini Demo App
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

    // Use uniqid() for better collision avoidance in transaction IDs
    if (!isset($data['trans_id'])) {
        $data['trans_id'] = uniqid();
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

    /**
     * SECURITY NOTE:
     * CURLOPT_SSL_VERIFYPEER is set to true for production security.
     * Ensure your hosting environment has updated CA root certificates.
     */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return [
            'status' => false,
            'message' => "cURL Error: $error"
        ];
    }

    $decoded = json_decode($response, true);
    if ($decoded === null) {
        return [
            'status' => false,
            'message' => "Invalid JSON response from API: $response"
        ];
    }

    // Fingerspot API sometimes uses 'status' and sometimes 'success'
    if (!isset($decoded['status']) && isset($decoded['success'])) {
        $decoded['status'] = $decoded['success'];
    }

    return $decoded;
}

/**
 * Get the list of devices
 */
function get_devices() {
    return fingerspot_request('get_device');
}

/**
 * Get attendance logs for a device
 */
function get_attendance($cloud_id, $start_date, $end_date) {
    return fingerspot_request('get_attlog', [
        'cloud_id' => $cloud_id,
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);
}

/**
 * Add a new employee to a device
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
 * Delete an employee from a device
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
?>
