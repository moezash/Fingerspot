<?php
/**
 * Mini Demo App: Helper Functions
 */

require_once 'config.php';

/**
 * Sends a POST request to the Fingerspot API.
 *
 * @param string $endpoint The API endpoint (e.g., 'get_device')
 * @param array $data The payload data
 * @return array|null The decoded JSON response or null on failure
 */
function fingerspot_request($endpoint, $data = []) {
    $url = FINGERSPOT_API_URL . '/' . $endpoint;

    // Ensure trans_id is always present
    if (!isset($data['trans_id'])) {
        $data['trans_id'] = uniqid($endpoint . '_');
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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        return ['status' => false, 'message' => 'cURL Error: ' . curl_error($ch)];
    }

    curl_close($ch);

    $result = json_decode($response, true);

    // If decoding failed
    if ($result === null) {
        return ['status' => false, 'message' => 'Invalid JSON response'];
    }

    return $result;
}

/**
 * Checks if the API response indicates success.
 * Handles both 'status' and 'success' keys used by Fingerspot API.
 */
function is_success($result) {
    return (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']);
}

/**
 * Sanitizes output for HTML display.
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
