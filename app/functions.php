<?php
/**
 * Utility functions for Fingerspot Integration App
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Check if API response indicates success (Fingerspot uses "success", older docs use "status")
 */
function is_api_success($result) {
    if (!is_array($result)) {
        return false;
    }
    if (array_key_exists('success', $result)) {
        return (bool) $result['success'];
    }
    if (array_key_exists('status', $result)) {
        return (bool) $result['status'];
    }
    return false;
}

function api_error_message($result) {
    if (!is_array($result)) {
        return 'Invalid response';
    }
    $message = $result['message'] ?? $result['msg'] ?? 'Unknown error';
    if (!empty($result['error_code'])) {
        $message = $result['error_code'] . ': ' . $message;
    }
    return $message;
}

/**
 * Normalize API response to always include boolean "status" for UI compatibility
 */
function normalize_api_result($result) {
    if (!is_array($result)) {
        return ['status' => false, 'message' => 'Invalid response'];
    }
    $result['status'] = is_api_success($result);
    if (!$result['status'] && empty($result['message'])) {
        $result['message'] = api_error_message($result);
    }
    return $result;
}

/**
 * Return Cloud IDs from the environment-backed configuration.
 */
function get_configured_cloud_ids() {
    $raw = trim(DEFAULT_CLOUD_IDS);
    if ($raw === '') {
        return [];
    }
    return array_values(array_filter(array_map('trim', explode(',', $raw))));
}

/**
 * Generate a unique trans_id for Fingerspot API requests.
 *
 * Schema stores trans_id as VARCHAR(50). Sample/API usage accepts free-form
 * strings (including numeric samples). Output is 32-char hex (fits VARCHAR(50))
 * and is generated once per request lifecycle in fingerspot_request().
 */
function generate_trans_id() {
    try {
        return bin2hex(random_bytes(16));
    } catch (Exception $e) {
        // Extremely unlikely fallback — still unique enough for local use
        return str_replace('.', '', uniqid((string) getmypid(), true));
    }
}

/**
 * Send a POST request to the Fingerspot API and log to database
 */
function fingerspot_request($endpoint, $data = [], $commandType = null) {
    global $pdo;

    if (!isset($data['trans_id']) || trim((string) $data['trans_id']) === '') {
        $data['trans_id'] = generate_trans_id();
    } else {
        $data['trans_id'] = trim((string) $data['trans_id']);
    }

    // Do not call the remote API when token is missing (never log/expose token).
    if (!is_api_token_configured()) {
        $configError = 'Konfigurasi error: FINGERSPOT_API_TOKEN belum di-set. Set environment variable atau file .env di root project.';
        $requestPayload = json_encode($data);

        $stmt = $pdo->prepare("INSERT INTO api_requests (endpoint, method, cloud_id, trans_id, request_payload, status, error_message) VALUES (?, 'POST', ?, ?, ?, 'failed', ?)");
        $stmt->execute([
            $endpoint,
            $data['cloud_id'] ?? null,
            $data['trans_id'],
            $requestPayload,
            $configError,
        ]);

        if ($commandType) {
            $stmt = $pdo->prepare("INSERT INTO command_logs (command_type, cloud_id, trans_id, pin, status, request_payload) VALUES (?, ?, ?, ?, 'failed', ?)");
            $stmt->execute([
                $commandType,
                $data['cloud_id'] ?? '',
                $data['trans_id'],
                $data['pin'] ?? null,
                $requestPayload,
            ]);
        }

        return [
            'status' => false,
            'success' => false,
            'message' => $configError,
        ];
    }

    $headers = [
        'Authorization: Bearer ' . API_TOKEN,
        'Content-Type: application/json',
    ];

    $requestPayload = json_encode($data);

    $stmt = $pdo->prepare("INSERT INTO api_requests (endpoint, method, cloud_id, trans_id, request_payload, status) VALUES (?, 'POST', ?, ?, ?, 'pending')");
    $stmt->execute([
        $endpoint,
        $data['cloud_id'] ?? null,
        $data['trans_id'],
        $requestPayload,
    ]);
    $apiRequestId = $pdo->lastInsertId();

    $commandLogId = null;
    if ($commandType) {
        $stmt = $pdo->prepare("INSERT INTO command_logs (command_type, cloud_id, trans_id, pin, status, request_payload) VALUES (?, ?, ?, ?, 'pending', ?)");
        $stmt->execute([
            $commandType,
            $data['cloud_id'] ?? '',
            $data['trans_id'],
            $data['pin'] ?? null,
            $requestPayload,
        ]);
        $commandLogId = $pdo->lastInsertId();
    }

    $url = API_URL . '/' . $endpoint;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestPayload);
    // Avoid intermittent connection resets seen with the default cURL transport.
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, SSL_VERIFY);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, SSL_VERIFY ? 2 : 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errorNumber = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        $status = 'failed';
        $errorMessage = "cURL Error $errorNumber: $error (HTTP $httpCode)";
        $result = ['status' => false, 'success' => false, 'message' => $errorMessage];
    } else {
        $result = json_decode($response, true);
        if ($result === null) {
            $status = 'failed';
            $errorMessage = 'Invalid JSON response';
            $result = ['status' => false, 'success' => false, 'message' => "Invalid JSON: $response"];
        } elseif (is_api_success($result)) {
            $status = 'success';
            $errorMessage = null;
        } else {
            $status = 'failed';
            $errorMessage = api_error_message($result);
        }
        $result = normalize_api_result($result);
    }

    $stmt = $pdo->prepare("UPDATE api_requests SET response_payload = ?, http_status = ?, status = ?, error_message = ? WHERE id = ?");
    $stmt->execute([$response, $httpCode, $status, $errorMessage, $apiRequestId]);

    if ($commandLogId) {
        if ($status === 'success' && !command_needs_webhook($commandType)) {
            $stmt = $pdo->prepare("UPDATE command_logs SET status = 'success', response_payload = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$response, $commandLogId]);
        } elseif ($status === 'failed') {
            $stmt = $pdo->prepare("UPDATE command_logs SET status = 'failed', response_payload = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$response, $commandLogId]);
        }
    }

    return $result;
}

/**
 * Commands that stay pending until a matching webhook callback arrives.
 *
 * Synchronous (complete from API response, not listed here):
 * - get_attlog — attendance data comes in the API response
 * - restart    — API ack is enough; webhook is optional / not always sent
 *
 * Asynchronous (listed here): remain pending until webhook with same trans_id
 */
function command_needs_webhook($commandType) {
    return in_array($commandType, [
        'get_userinfo',
        'get_allpin',
        'set_userinfo',
        'delete_userinfo',
        'set_time',
        'reg_online',
    ], true);
}

/**
 * Fail async commands that have waited too long for their webhook callback.
 *
 * Synchronous commands are deliberately excluded by the explicit allow-list.
 * The pending predicate also makes this idempotent and preserves final states.
 *
 * @param int|null $timeoutMinutes Positive timeout override (primarily for tests)
 * @return int Number of commands expired by this call
 */
function expire_pending_commands($timeoutMinutes = null) {
    global $pdo;

    $timeoutMinutes = $timeoutMinutes === null
        ? COMMAND_PENDING_TIMEOUT_MINUTES
        : filter_var($timeoutMinutes, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($timeoutMinutes === false || $timeoutMinutes < 1) {
        throw new InvalidArgumentException('Pending command timeout must be a positive integer.');
    }

    $asyncTypes = [
        'get_userinfo',
        'get_allpin',
        'set_userinfo',
        'delete_userinfo',
        'set_time',
        'reg_online',
    ];
    $placeholders = implode(',', array_fill(0, count($asyncTypes), '?'));
    $reason = 'Command timed out waiting for webhook';

    // $timeoutMinutes is validated as a positive integer before interpolation.
    $sql = "UPDATE command_logs
            SET status = 'failed',
                notes = CASE
                    WHEN notes IS NULL OR TRIM(notes) = '' THEN ?
                    ELSE CONCAT(notes, '\n', ?)
                END,
                updated_at = NOW()
            WHERE status = 'pending'
              AND command_type IN ($placeholders)
              AND created_at < DATE_SUB(NOW(), INTERVAL " . (int) $timeoutMinutes . " MINUTE)";
    $params = array_merge([$reason, $reason], $asyncTypes);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->rowCount();
}

/**
 * Save attlog records from API or webhook payload (idempotent).
 *
 * Identical scans (cloud_id, pin, scan_time, verify, status_scan) are inserted
 * once via UNIQUE KEY + INSERT IGNORE — safe for get_attlog re-pull and
 * realtime attlog webhook overlap.
 *
 * @return int|false Count of newly inserted rows (0 = duplicate/no-op success),
 *                   or false when payload has no valid pin records.
 */
function save_attlogs($cloudId, $records, $rawJson) {
    global $pdo;

    if (!is_array($records)) {
        return false;
    }

    if (isset($records['pin'])) {
        $records = [$records];
    }

    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO attlogs (cloud_id, pin, scan_time, verify, status_scan, raw_payload)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $saved = 0;
    $attempted = 0;

    foreach ($records as $record) {
        if (!is_array($record) || empty($record['pin'])) {
            continue;
        }
        $attempted++;

        $scanRaw = $record['scan'] ?? $record['scan_time'] ?? date('Y-m-d H:i:s');
        $scanTs = strtotime((string) $scanRaw);
        $scanTime = ($scanTs === false) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $scanTs);

        $stmt->execute([
            $cloudId,
            (string) $record['pin'],
            $scanTime,
            (int) ($record['verify'] ?? 0),
            (int) ($record['status_scan'] ?? 0),
            $rawJson,
        ]);
        $saved += (int) $stmt->rowCount();
    }

    if ($attempted === 0) {
        return false;
    }

    return $saved;
}

/**
 * Save userinfo from API or webhook payload
 */
function save_userinfo($cloudId, $userData, $rawJson) {
    global $pdo;

    if (!is_array($userData) || empty($userData['pin'])) {
        return false;
    }

    $stmt = $pdo->prepare("INSERT INTO userinfos (cloud_id, pin, name, privilege, finger, face, password, rfid, raw_payload)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                           ON DUPLICATE KEY UPDATE
                           name = VALUES(name), privilege = VALUES(privilege), finger = VALUES(finger),
                           face = VALUES(face), password = VALUES(password), rfid = VALUES(rfid),
                           raw_payload = VALUES(raw_payload), updated_at = NOW()");
    return $stmt->execute([
        $cloudId,
        (string) $userData['pin'],
        $userData['name'] ?? '',
        (string) ($userData['privilege'] ?? '0'),
        (int) ($userData['finger'] ?? 0),
        (int) ($userData['face'] ?? 0),
        $userData['password'] ?? '',
        $userData['rfid'] ?? '',
        $rawJson,
    ]);
}

/**
 * Replace PIN list for a device atomically.
 *
 * DELETE + INSERT run in one transaction so a mid-refresh failure rolls back
 * and leaves the previous PIN set intact. Duplicate PIN values in the payload
 * are collapsed before insert (UNIQUE cloud_id+pin).
 */
function save_pins($cloudId, $pins, $rawJson) {
    global $pdo;

    if (!is_array($pins)) {
        return 0;
    }

    $uniquePins = [];
    foreach ($pins as $pin) {
        $pinValue = is_array($pin) ? ($pin['pin'] ?? null) : $pin;
        if ($pinValue === null || $pinValue === '') {
            continue;
        }
        $uniquePins[(string) $pinValue] = true;
    }
    $pinList = array_keys($uniquePins);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("DELETE FROM pins WHERE cloud_id = ?");
        $stmt->execute([$cloudId]);

        $insert = $pdo->prepare("INSERT INTO pins (cloud_id, pin, raw_payload) VALUES (?, ?, ?)");
        $saved = 0;
        foreach ($pinList as $pinValue) {
            $insert->execute([$cloudId, $pinValue, $rawJson]);
            $saved++;
        }

        $pdo->commit();
        return $saved;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Update a pending async command_logs row by trans_id.
 *
 * Never finalizes get_attlog via webhook (realtime attlog is a separate event).
 * When $expectedTypes is provided, only matching command_type rows are updated
 * (e.g. get_userid_list callback → get_allpin command).
 * When $cloudId is provided, also match cloud_id for safer pairing.
 * Duplicate callbacks are safe: already-finalized rows (not pending) are ignored.
 *
 * @param string $transId
 * @param string $status success|failed
 * @param string $responsePayload
 * @param string|string[]|null $expectedTypes
 * @param string|null $cloudId
 * @return bool true if a pending row was updated
 */
function update_command_log($transId, $status, $responsePayload, $expectedTypes = null, $cloudId = null) {
    global $pdo;

    if ($transId === null || $transId === '') {
        return false;
    }

    $sql = "UPDATE command_logs
            SET status = ?, response_payload = ?, updated_at = NOW()
            WHERE trans_id = ? AND status = 'pending' AND command_type <> 'get_attlog'
              AND command_type IN ('get_userinfo', 'get_allpin', 'set_userinfo', 'delete_userinfo', 'set_time', 'reg_online')";
    $params = [$status, $responsePayload, $transId];

    if ($cloudId !== null && $cloudId !== '') {
        $sql .= " AND cloud_id = ?";
        $params[] = $cloudId;
    }

    if ($expectedTypes !== null) {
        $types = array_values(array_filter((array) $expectedTypes, function ($t) {
            return $t !== null && $t !== '' && $t !== 'get_attlog';
        }));
        if (empty($types)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($types), '?'));
        $sql .= " AND command_type IN ($placeholders)";
        foreach ($types as $type) {
            $params[] = $type;
        }
    }

    $sql .= " ORDER BY created_at DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount() > 0;
}

/**
 * Normalize PIN / user-id list payloads from get_allpin or get_userid_list webhooks.
 * Returns null if payload is not a usable list.
 *
 * Supports shapes such as:
 * - [{"pin":"1"}, ...] / ["1","2"]
 * - {"pin":"1"} / {"userid":"1"}
 * - {"total":3,"pin_arr":["1","2","3"]}
 */
function normalize_pin_list_payload($payload) {
    if (!is_array($payload)) {
        return null;
    }

    // Wrapper objects (get_userid_list style) — do not treat the whole object as PIN rows.
    $listKeys = ['pin_arr', 'pins', 'userids', 'userid_list', 'users', 'list'];
    foreach ($listKeys as $key) {
        if (isset($payload[$key]) && is_array($payload[$key])) {
            $payload = $payload[$key];
            break;
        }
    }

    // Single object with pin/userid
    if (isset($payload['pin']) || isset($payload['userid']) || isset($payload['user_id'])) {
        $payload = [$payload];
    }

    // Associative map of pins — reject plain assoc without numeric keys unless list-like
    if ($payload !== [] && array_keys($payload) !== range(0, count($payload) - 1)) {
        $metaKeys = ['total', 'count', 'message', 'msg', 'success', 'status', 'error_code', 'type'];
        $converted = [];
        foreach ($payload as $key => $value) {
            if (in_array((string) $key, $metaKeys, true)) {
                continue;
            }
            if (is_array($value)) {
                $converted[] = $value;
            } elseif (is_scalar($value) && $value !== '' && $value !== null) {
                $converted[] = ['pin' => (string) $value];
            } elseif (is_scalar($key) && (is_int($key) || ctype_digit((string) $key))) {
                $converted[] = ['pin' => (string) $key];
            }
        }
        if (empty($converted)) {
            return null;
        }
        $payload = $converted;
    }

    $pins = [];
    foreach ($payload as $item) {
        if (is_array($item)) {
            $pinValue = $item['pin'] ?? $item['userid'] ?? $item['user_id'] ?? null;
        } else {
            $pinValue = $item;
        }
        if ($pinValue === null || $pinValue === '') {
            continue;
        }
        $pins[] = (string) $pinValue;
    }

    return $pins;
}

/**
 * Whether a webhook payload explicitly signals failure.
 * Data-bearing success callbacks often omit success/status flags.
 */
function webhook_indicates_failure($data) {
    if (!is_array($data)) {
        return true;
    }
    if (array_key_exists('success', $data) && !(bool) $data['success']) {
        return true;
    }
    if (array_key_exists('status', $data) && $data['status'] === false) {
        return true;
    }
    if (!empty($data['error_code'])) {
        return true;
    }
    return false;
}

function get_device_info($cloud_id) {
    return fingerspot_request('get_device', [
        'cloud_id' => $cloud_id,
        'trans_id' => generate_trans_id(),
    ], null);
}

function get_devices() {
    $cloudIds = get_configured_cloud_ids();
    $devices = [];

    foreach ($cloudIds as $cloudId) {
        $info = get_device_info($cloudId);
        $devices[] = [
            'cloud_id' => $cloudId,
            'name' => $info['data']['name'] ?? $info['data']['device_name'] ?? '-',
            'status' => is_api_success($info) ? ($info['data']['status'] ?? 'Online') : 'Error',
            'api' => $info,
        ];
    }

    return [
        'status' => !empty($devices),
        'data' => $devices,
        'message' => empty($cloudIds) ? 'Cloud ID belum dikonfigurasi. Set FINGERSPOT_CLOUD_IDS di environment/.env.' : null,
    ];
}

function get_attendance($cloud_id, $start_date, $end_date) {
    $result = fingerspot_request('get_attlog', [
        'cloud_id' => $cloud_id,
        'start_date' => $start_date,
        'end_date' => $end_date,
    ], 'get_attlog');

    if (is_api_success($result) && !empty($result['data']) && is_array($result['data'])) {
        $raw = json_encode($result, JSON_UNESCAPED_UNICODE);
        $saved = save_attlogs($cloud_id, $result['data'], $raw);
        $result['saved_count'] = ($saved === false) ? 0 : $saved;
    }

    return normalize_api_result($result);
}

function get_userinfo($cloud_id, $pin) {
    return normalize_api_result(fingerspot_request('get_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin,
    ], 'get_userinfo'));
}

function set_userinfo($cloud_id, $pin, $name, $privilege = '0') {
    return normalize_api_result(fingerspot_request('set_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin,
        'name' => $name,
        'privilege' => $privilege,
    ], 'set_userinfo'));
}

function delete_userinfo($cloud_id, $pin) {
    return normalize_api_result(fingerspot_request('delete_userinfo', [
        'cloud_id' => $cloud_id,
        'pin' => $pin,
    ], 'delete_userinfo'));
}

function get_allpin($cloud_id) {
    return normalize_api_result(fingerspot_request('get_allpin', [
        'cloud_id' => $cloud_id,
    ], 'get_allpin'));
}

function set_time($cloud_id) {
    return normalize_api_result(fingerspot_request('set_time', [
        'cloud_id' => $cloud_id,
    ], 'set_time'));
}

function register_online($cloud_id, $pin, $verification = '0') {
    return normalize_api_result(fingerspot_request('reg_online', [
        'cloud_id' => $cloud_id,
        'pin' => $pin,
        'verification' => $verification,
    ], 'reg_online'));
}

function restart_machine($cloud_id) {
    return normalize_api_result(fingerspot_request('restart', [
        'cloud_id' => $cloud_id,
    ], 'restart'));
}

function status_badge($status) {
    switch ($status) {
        case 'success':
        case 'processed':
            return '<span class="badge bg-success">● ' . ($status === 'processed' ? 'Processed' : 'Success') . '</span>';
        case 'pending':
            return '<span class="badge bg-warning status-pending"><span class="status-pulse-dot" aria-hidden="true"></span>Pending</span>';
        case 'received':
            return '<span class="badge bg-warning">● Received</span>';
        case 'failed':
            return '<span class="badge bg-danger">● Failed</span>';
        default:
            return '<span class="badge bg-secondary">' . htmlspecialchars((string) $status) . '</span>';
    }
}

function get_pagination($table, $where = '1=1', $params = []) {
    global $pdo;

    if (!is_allowed_db_table($table)) {
        throw new InvalidArgumentException('Invalid table for pagination');
    }

    // $where must be built by trusted app code with placeholders — never from raw request strings.
    if (!is_string($where) || $where === '') {
        throw new InvalidArgumentException('Invalid pagination where clause');
    }

    $page = isset($_GET['p']) ? max(1, (int) $_GET['p']) : 1;
    $offset = ($page - 1) * ITEMS_PER_PAGE;

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `$table` WHERE $where");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();
    $totalPages = max(1, (int) ceil($total / ITEMS_PER_PAGE));

    return [
        'page' => $page,
        'offset' => $offset,
        'limit' => ITEMS_PER_PAGE,
        'total' => $total,
        'total_pages' => $totalPages,
    ];
}

/**
 * Whitelisted database tables for dynamic SQL identifiers.
 */
function allowed_db_tables() {
    return ['attlogs', 'userinfos', 'pins', 'api_requests', 'webhook_responses', 'command_logs'];
}

function is_allowed_db_table($table) {
    return is_string($table) && in_array($table, allowed_db_tables(), true);
}

/**
 * CSRF token helpers (session-based). Not used by external webhook.
 */
function csrf_token() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new RuntimeException('Session required for CSRF token');
    }
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function verify_csrf_token($token) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
        return false;
    }
    if (!is_string($token) || $token === '') {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Webhook shared-secret check.
 *
 * Fingerspot registers a URL — use query ?secret=... (or ?token=...).
 * Optional header X-Webhook-Secret is accepted if present, but not required by provider.
 *
 * Production without configured secret → deny (not insecure by default).
 * Development without secret → allow (with config warning).
 */
function verify_webhook_request() {
    if (!is_webhook_secret_configured()) {
        return APP_ENV !== 'production';
    }

    $provided = '';
    if (isset($_GET['secret']) && is_string($_GET['secret'])) {
        $provided = $_GET['secret'];
    } elseif (isset($_GET['token']) && is_string($_GET['token'])) {
        $provided = $_GET['token'];
    } elseif (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) && is_string($_SERVER['HTTP_X_WEBHOOK_SECRET'])) {
        $provided = $_SERVER['HTTP_X_WEBHOOK_SECRET'];
    }

    if ($provided === '') {
        return false;
    }

    return hash_equals(WEBHOOK_SECRET, $provided);
}

/**
 * Input validators for command forms / API helpers.
 */
function validate_cloud_id($value) {
    $value = trim((string) $value);
    if ($value === '' || strlen($value) > 50) {
        return null;
    }
    if (!preg_match('/^[A-Za-z0-9_-]+$/', $value)) {
        return null;
    }
    return $value;
}

function validate_pin($value) {
    $value = trim((string) $value);
    if ($value === '' || strlen($value) > 50) {
        return null;
    }
    if (!preg_match('/^[A-Za-z0-9_-]+$/', $value)) {
        return null;
    }
    return $value;
}

function validate_date_ymd($value) {
    $value = trim((string) $value);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return null;
    }
    $dt = DateTime::createFromFormat('Y-m-d', $value);
    if (!$dt || $dt->format('Y-m-d') !== $value) {
        return null;
    }
    return $value;
}

function validate_name($value) {
    $value = trim((string) $value);
    if ($value === '' || strlen($value) > 100) {
        return null;
    }
    return $value;
}

function allowed_post_actions() {
    return [
        'get_attendance',
        'get_userinfo',
        'set_userinfo',
        'delete_userinfo',
        'get_allpin',
        'set_time',
        'register_online',
        'restart',
    ];
}

function allowed_redirect_pages() {
    return ['dashboard', 'attlog', 'userinfo', 'pins', 'api_logs', 'webhook_logs', 'detail', 'devices', 'commands'];
}
