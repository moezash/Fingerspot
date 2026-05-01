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

    // Ensure trans_id is present
    if (!isset($data['trans_id'])) {
        $data['trans_id'] = (string)time();
    }

    $headers = [
        'Authorization: Bearer ' . API_TOKEN,
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if (API_TOKEN === 'YOUR_API_TOKEN_HERE') {
        // Return mock data for testing/verification purposes
        return get_mock_response($endpoint, $data);
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['status' => false, 'message' => "cURL Error: $error"];
    }

    $decoded = json_decode($response, true);
    if ($decoded === null) {
        return ['status' => false, 'message' => "Invalid JSON response: $response"];
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

/**
 * Mock responses for verification
 */
function get_mock_response($endpoint, $data) {
    switch ($endpoint) {
        case 'get_device':
            return [
                'status' => true,
                'message' => 'Success',
                'data' => [
                    ['cloud_id' => 'DEV001', 'name' => 'Main Office'],
                    ['cloud_id' => 'DEV002', 'name' => 'Warehouse']
                ]
            ];
        case 'get_attlog':
            return [
                'status' => true,
                'message' => 'Success',
                'data' => [
                    ['pin' => '1', 'scan' => date('Y-m-d') . ' 08:00:00', 'status_scan' => '0', 'verify' => '1'],
                    ['pin' => '2', 'scan' => date('Y-m-d') . ' 08:05:00', 'status_scan' => '0', 'verify' => '1'],
                    ['pin' => '1', 'scan' => date('Y-m-d') . ' 17:00:00', 'status_scan' => '1', 'verify' => '1']
                ]
            ];
        case 'set_userinfo':
        case 'delete_userinfo':
        case 'set_time':
            return ['status' => true, 'message' => 'Mock Success'];
        default:
            return ['status' => false, 'message' => 'Unknown endpoint'];
    }
}
?>
